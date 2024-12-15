<?php

namespace App\Services;

use App\Models\AutomatedMessage;
use App\Models\Invitation;
use App\Models\MessageLog;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class MessageService
{
    /**
     * Process and send an automated message
     *
     * @param AutomatedMessage $message
     * @param Invitation $invitation
     * @param array $additionalData
     * @return MessageLog|null
     */
    public function send(AutomatedMessage $message, Invitation $invitation, array $additionalData = []): ?MessageLog
    {
        try {
            // Determine recipient language
            $locale = $invitation->user->language ?? 'he';
            
            // Get message content for the appropriate language
            $content = $message->content[$locale] ?? $message->content['he'];
            
            // Process template variables
            $subject = $this->processTemplate($content['subject'], $invitation, $additionalData);
            $body = $this->processTemplate($content['body'], $invitation, $additionalData);
            
            // Create message log entry
            $log = MessageLog::create([
                'invitation_id' => $invitation->id,
                'message_id' => $message->id,
                'recipient' => $invitation->user->email,
                'type' => 'email',
                'status' => 'pending',
            ]);

            // Send the email
            Mail::raw($body, function ($message) use ($invitation, $subject) {
                $message->to($invitation->user->email)
                        ->subject($subject);
            });

            // Update log status
            $log->update([
                'status' => 'sent',
                'sent_at' => now(),
            ]);

            return $log;
        } catch (\Exception $e) {
            \Log::error('Failed to send automated message', [
                'message_id' => $message->id,
                'invitation_id' => $invitation->id,
                'error' => $e->getMessage()
            ]);

            if (isset($log)) {
                $log->update([
                    'status' => 'failed',
                ]);
            }

            return null;
        }
    }

    /**
     * Process template variables
     */
    protected function processTemplate(string $template, Invitation $invitation, array $additionalData): string
    {
        $date = Carbon::parse($invitation->event_date);
        
        $variables = [
            'name' => $invitation->user->name,
            'date' => $date->format('d/m/Y'),
            'time' => $date->format('H:i'),
            'location' => $invitation->venue_name,
            'rsvp_link' => route('invitations.show', $invitation->slug),
            ...$additionalData,
        ];

        return Str::of($template)->replace(
            array_map(fn ($key) => '{' . $key . '}', array_keys($variables)),
            array_values($variables)
        );
    }

    /**
     * Schedule automated messages for an invitation
     */
    public function scheduleMessages(Invitation $invitation): void
    {
        $eventDate = Carbon::parse($invitation->event_date);

        // Schedule RSVP invitation
        $this->scheduleRsvpInvitation($invitation);

        // Schedule RSVP reminder (7 days before event)
        $this->scheduleRsvpReminder($invitation, $eventDate);

        // Schedule event reminder (1 day before event)
        $this->scheduleEventReminder($invitation, $eventDate);

        // Schedule thank you message (1 day after event)
        $this->scheduleThankYou($invitation, $eventDate);
    }

    /**
     * Schedule RSVP invitation message
     */
    protected function scheduleRsvpInvitation(Invitation $invitation): void
    {
        $message = AutomatedMessage::where('type', 'rsvp_invitation')
            ->where('is_active', true)
            ->first();

        if ($message) {
            // Send immediately
            $this->send($message, $invitation);
        }
    }

    /**
     * Schedule RSVP reminder message
     */
    protected function scheduleRsvpReminder(Invitation $invitation, Carbon $eventDate): void
    {
        $message = AutomatedMessage::where('type', 'rsvp_reminder')
            ->where('is_active', true)
            ->first();

        if ($message) {
            $scheduledDate = $eventDate->copy()->subDays(7);
            
            if ($scheduledDate->isFuture()) {
                // Schedule for 7 days before event
                $this->scheduleMessage($message, $invitation, $scheduledDate);
            }
        }
    }

    /**
     * Schedule event reminder message
     */
    protected function scheduleEventReminder(Invitation $invitation, Carbon $eventDate): void
    {
        $message = AutomatedMessage::where('type', 'event_reminder')
            ->where('is_active', true)
            ->first();

        if ($message) {
            $scheduledDate = $eventDate->copy()->subDay();
            
            if ($scheduledDate->isFuture()) {
                // Schedule for 1 day before event
                $this->scheduleMessage($message, $invitation, $scheduledDate);
            }
        }
    }

    /**
     * Schedule thank you message
     */
    protected function scheduleThankYou(Invitation $invitation, Carbon $eventDate): void
    {
        $message = AutomatedMessage::where('type', 'thank_you')
            ->where('is_active', true)
            ->first();

        if ($message) {
            $scheduledDate = $eventDate->copy()->addDay();
            
            // Schedule for 1 day after event
            $this->scheduleMessage($message, $invitation, $scheduledDate);
        }
    }

    /**
     * Schedule a message for future delivery
     */
    protected function scheduleMessage(AutomatedMessage $message, Invitation $invitation, Carbon $scheduledDate): void
    {
        // Use Laravel's built-in scheduling
        \App\Jobs\SendAutomatedMessage::dispatch($message, $invitation)
            ->delay($scheduledDate);
    }
}
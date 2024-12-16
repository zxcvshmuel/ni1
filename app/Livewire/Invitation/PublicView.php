<?php

namespace App\Livewire\Invitation;

use App\Models\Invitation;
use Livewire\Component;

class PublicView extends Component
{
    public Invitation $invitation;
    public bool $showRsvpModal = false;
    public bool $isPreview;
    
    public function mount(Invitation $invitation, bool $isPreview = false)
    {
        $this->invitation = $invitation;
        $this->isPreview = $isPreview;

        // Increment view count only for public views
        if (!$isPreview) {
            $this->invitation->increment('views_count');
        }
    }

    public function shareWhatsApp()
    {
        $url = route('invitations.show', $this->invitation->slug);
        $text = urlencode("מוזמנים לחגוג איתנו - {$this->invitation->title}");
        
        return redirect()->away("https://wa.me/?text={$text}%20{$url}");
    }

    public function addToCalendar()
    {
        // Generate calendar file for download
        $calendar = new \Spatie\IcalendarGenerator\Components\Calendar;
        $calendar->name($this->invitation->title);
        
        $event = \Spatie\IcalendarGenerator\Components\Event::create()
            ->name($this->invitation->title)
            ->description($this->invitation->content['greeting'] ?? '')
            ->address($this->invitation->venue_address)
            ->startsAt($this->invitation->event_date)
            ->endsAt($this->invitation->event_date->addHours(5));
            
        $calendar->event($event);
        
        return response($calendar->get())
            ->header('Content-Type', 'text/calendar')
            ->header('Content-Disposition', 'attachment; filename="event.ics"');
    }

    public function render()
    {
        return view('livewire.invitation.public-view')
            ->layout('layouts.invitation', ['invitation' => $this->invitation]);
    }
}
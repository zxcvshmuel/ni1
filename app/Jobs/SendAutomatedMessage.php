<?php

namespace App\Jobs;

use App\Models\AutomatedMessage;
use App\Models\Invitation;
use App\Services\MessageService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendAutomatedMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Number of times the job should be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * Delete the job if its models no longer exist.
     *
     * @var bool
     */
    public $deleteWhenMissingModels = true;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public AutomatedMessage $message,
        public Invitation $invitation
    ) {}

    /**
     * Execute the job.
     */
    public function handle(MessageService $messageService): void
    {
        // Only send if invitation is still active
        if ($this->invitation->is_active && $this->message->is_active) {
            $messageService->send($this->message, $this->invitation);
        }
    }

    /**
     * Get the tags that should be assigned to the job.
     *
     * @return array<int, string>
     */
    public function tags(): array
    {
        return [
            'automated-message',
            "message:{$this->message->id}",
            "invitation:{$this->invitation->id}",
            "type:{$this->message->type}"
        ];
    }

    /**
     * Calculate the number of seconds to wait before retrying the job.
     */
    public function backoff(): array
    {
        return [60, 300, 600]; // 1 minute, 5 minutes, 10 minutes
    }
}
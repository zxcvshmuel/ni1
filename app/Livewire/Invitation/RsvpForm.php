<?php

namespace App\Http\Livewire\Invitation;

use App\Models\Invitation;
use App\Models\RsvpResponse;
use App\Services\RsvpService;
use Livewire\Component;

class RsvpForm extends Component
{
    public Invitation $invitation;
    public string $show = 'false';
    
    // Form Fields
    public string $name = '';
    public ?string $email = null;
    public ?string $phone = null;
    public int $guests_count = 1;
    public string $status = 'attending';
    public array $preferences = [];
    public ?string $notes = null;

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'nullable|email|max:255',
        'phone' => 'nullable|string|max:20',
        'guests_count' => 'required|integer|min:1|max:10',
        'status' => 'required|in:attending,not_attending,maybe',
        'preferences' => 'nullable|array',
        'notes' => 'nullable|string|max:1000',
    ];

    public function submit(RsvpService $rsvpService)
    {
        $validated = $this->validate();
        
        try {
            $response = $rsvpService->handleResponse($this->invitation, $validated);
            
            $this->reset(['name', 'email', 'phone', 'guests_count', 'preferences', 'notes']);
            $this->show = 'false';
            
            $this->dispatchBrowserEvent('rsvp-success');
        } catch (\Exception $e) {
            $this->addError('submit', 'אירעה שגיאה בשמירת האישור.');
        }
    }

    public function render()
    {
        return view('livewire.invitation.rsvp-form');
    }
}
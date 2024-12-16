<?php

namespace App\View\Components;

use App\Models\Invitation;
use Livewire\Component;

class InvitationView extends Component
{
    public Invitation $invitation;
    
    public function mount(Invitation $invitation) 
    {
        $this->invitation = $invitation;
    }

    public function render()
    {
        return view('components.invitation-view');
    }
}
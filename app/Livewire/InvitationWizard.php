<?php

namespace App\Http\Livewire;

use App\Models\Invitation;
use Livewire\Component;

class InvitationWizard extends Component
{
    public int $currentStep = 1;
    public array $steps = [
        1 => ['title' => 'תבנית', 'icon' => 'template'],
        2 => ['title' => 'מוזיקה', 'icon' => 'musical-note'],
        3 => ['title' => 'אפקטים', 'icon' => 'sparkles'],
        4 => ['title' => 'פרטים', 'icon' => 'pencil'],
        5 => ['title' => 'אישור', 'icon' => 'check'],
        6 => ['title' => 'תשלום', 'icon' => 'credit-card'],
    ];

    protected $listeners = ['nextStep', 'previousStep'];

    public function nextStep()
    {
        if ($this->currentStep < count($this->steps)) {
            $this->currentStep++;
        }
    }

    public function previousStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function render()
    {
        return view('livewire.invitation-wizard')->layout('layouts.app');
    }
}
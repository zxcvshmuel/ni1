<?php

namespace App\Livewire\Invitation;

use Livewire\Component;

class CreateWizard extends Component
{
    public int $currentStep = 1;
    public ?int $selectedTemplateId = null;


    public function mount()
    {
        // $this->on('template-selected', function ($templateId) {
        //     $this->selectedTemplateId = $templateId;
        // });
    }
    public array $steps = [
        1 => [
            'key' => 'template',
            'title' => 'תבנית',
            'icon' => 'template',
        ],
        2 => [
            'key' => 'music',
            'title' => 'מוזיקה',
            'icon' => 'musical-note',
        ],
        3 => [
            'key' => 'effects',
            'title' => 'אפקטים',
            'icon' => 'sparkles',
        ],
        4 => [
            'key' => 'details',
            'title' => 'פרטים',
            'icon' => 'pencil',
        ],
        5 => [
            'key' => 'preview',
            'title' => 'אישור',
            'icon' => 'check',
        ],
        6 => [
            'key' => 'payment',
            'title' => 'תשלום',
            'icon' => 'credit-card',
        ]
    ];

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
        return view('livewire.invitation.create-wizard');
    }
}

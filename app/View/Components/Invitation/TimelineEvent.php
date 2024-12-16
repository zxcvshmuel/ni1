<?php

namespace App\View\Components\Invitation;

use Livewire\Component;

class TimelineEvent extends Component
{
    public string $time;
    public string $title;
    public string $icon;
    public ?string $description;
    public string $color;

    public function mount(
        string $time,
        string $title,
        string $icon = 'clock',
        ?string $description = null,
        string $color = 'primary'
    ) {
        $this->time = $time;
        $this->title = $title;
        $this->icon = $icon;
        $this->description = $description;
        $this->color = $color;
    }

    public function render()
    {
        return view('components.invitation.timeline-event');
    }
}
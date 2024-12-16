<?php

namespace App\View\Components\UI;

use Illuminate\View\Component;

class Card extends Component
{
    public string $title;
    public ?string $subtitle;
    public bool $rtl;

    public function __construct(string $title = '', ?string $subtitle = null, bool $rtl = true)
    {
        $this->title = $title;
        $this->subtitle = $subtitle;
        $this->rtl = $rtl;
    }

    public function render()
    {
        return view('components.ui.card');
    }
}
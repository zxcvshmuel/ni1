<?php

namespace App\View\Components\Layouts;

use App\Models\Invitation;
use Illuminate\View\Component;

class InvitationLayout extends Component
{
    public Invitation $invitation;
    public bool $preview;

    public function __construct(Invitation $invitation, bool $preview = false)
    {
        $this->invitation = $invitation;
        $this->preview = $preview;
    }

    public function render()
    {
        return view('layouts.invitation');
    }
}
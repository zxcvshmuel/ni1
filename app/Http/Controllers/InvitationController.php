<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvitationController extends Controller
{
    /**
     * Display the public invitation page
     */
    public function show(string $slug): View
    {
        $invitation = Invitation::where('slug', $slug)
            ->where('is_active', true)
            ->whereNull('expires_at')
            ->orWhere('expires_at', '>', now())
            ->firstOrFail();

        // Increment view counter
        $invitation->increment('views_count');

        // Track song plays if needed
        if ($invitation->songs()->exists()) {
            $invitation->songs->each(function ($song) {
                $song->increment('plays_count');
            });
        }

        return view('invitations.show', [
            'invitation' => $invitation,
            'template' => $invitation->template,
            'effects' => $invitation->effects,
            'songs' => $invitation->songs,
        ]);
    }

    /**
     * Display the invitation preview page (requires authentication)
     */
    public function preview(Invitation $invitation): View
    {
        $this->authorize('view', $invitation);

        return view('invitations.preview', [
            'invitation' => $invitation,
            'template' => $invitation->template,
            'effects' => $invitation->effects,
            'songs' => $invitation->songs,
            'isPreview' => true,
        ]);
    }
}
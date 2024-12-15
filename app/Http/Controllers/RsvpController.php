<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Models\RsvpResponse;
use App\Services\RsvpService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class RsvpController extends Controller
{
    public function __construct(
        protected RsvpService $rsvpService
    ) {}

    /**
     * Store a new RSVP response
     */
    public function store(Request $request, Invitation $invitation): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'guests_count' => ['required', 'integer', 'min:1', 'max:10'],
            'status' => ['required', 'in:attending,not_attending,maybe'],
            'preferences' => ['nullable', 'array'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $response = $this->rsvpService->handleResponse($invitation, $validated);

            return response()->json([
                'message' => 'תודה על האישור!',
                'response' => $response,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'אירעה שגיאה בשמירת האישור.',
            ], 500);
        }
    }

    /**
     * Update an existing RSVP response
     */
    public function update(
        Request $request,
        Invitation $invitation,
        RsvpResponse $response
    ): JsonResponse {
        // Verify the response belongs to this invitation
        if ($response->invitation_id !== $invitation->id) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'guests_count' => ['required', 'integer', 'min:1', 'max:10'],
            'status' => ['required', 'in:attending,not_attending,maybe'],
            'preferences' => ['nullable', 'array'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $response = $this->rsvpService->handleResponse($invitation, $validated);

            return response()->json([
                'message' => 'האישור עודכן בהצלחה!',
                'response' => $response,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'אירעה שגיאה בעדכון האישור.',
            ], 500);
        }
    }
}
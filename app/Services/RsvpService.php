<?php

namespace App\Services;

use App\Models\RsvpResponse;
use App\Models\Invitation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RsvpService
{
    /**
     * Create or update an RSVP response
     */
    public function handleResponse(Invitation $invitation, array $data): RsvpResponse
    {
        // Check if response already exists by email or phone
        $existingResponse = $this->findExistingResponse($invitation, $data);

        try {
            DB::beginTransaction();

            if ($existingResponse) {
                $response = $this->updateResponse($existingResponse, $data);
            } else {
                $response = $this->createResponse($invitation, $data);
            }

            // Log the activity
            activity()
                ->performedOn($invitation)
                ->withProperties([
                    'status' => $data['status'],
                    'guests_count' => $data['guests_count'],
                ])
                ->log('rsvp_response_' . ($existingResponse ? 'updated' : 'created'));

            DB::commit();
            return $response;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Find existing response by email or phone
     */
    protected function findExistingResponse(Invitation $invitation, array $data): ?RsvpResponse
    {
        $query = RsvpResponse::where('invitation_id', $invitation->id);

        if (!empty($data['email'])) {
            $query->where('email', $data['email']);
        } elseif (!empty($data['phone'])) {
            $query->where('phone', $data['phone']);
        } else {
            return null;
        }

        return $query->first();
    }

    /**
     * Create a new RSVP response
     */
    protected function createResponse(Invitation $invitation, array $data): RsvpResponse
    {
        return RsvpResponse::create([
            'invitation_id' => $invitation->id,
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'guests_count' => $data['guests_count'],
            'status' => $data['status'],
            'preferences' => $data['preferences'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);
    }

    /**
     * Update an existing RSVP response
     */
    protected function updateResponse(RsvpResponse $response, array $data): RsvpResponse
    {
        $response->update([
            'name' => $data['name'],
            'email' => $data['email'] ?? $response->email,
            'phone' => $data['phone'] ?? $response->phone,
            'guests_count' => $data['guests_count'],
            'status' => $data['status'],
            'preferences' => $data['preferences'] ?? $response->preferences,
            'notes' => $data['notes'] ?? $response->notes,
        ]);

        return $response;
    }

    /**
     * Get RSVP statistics for an invitation
     */
    public function getStats(Invitation $invitation): array
    {
        $stats = RsvpResponse::where('invitation_id', $invitation->id)
            ->select(
                DB::raw('COUNT(*) as total_responses'),
                DB::raw('SUM(CASE WHEN status = "attending" THEN guests_count ELSE 0 END) as attending_count'),
                DB::raw('SUM(CASE WHEN status = "not_attending" THEN 1 ELSE 0 END) as not_attending_count'),
                DB::raw('SUM(CASE WHEN status = "maybe" THEN 1 ELSE 0 END) as maybe_count')
            )
            ->first();

        return [
            'total_responses' => $stats->total_responses,
            'attending_count' => $stats->attending_count,
            'not_attending_count' => $stats->not_attending_count,
            'maybe_count' => $stats->maybe_count,
            'response_rate' => $stats->total_responses > 0 ? 
                ($stats->attending_count / $stats->total_responses) * 100 : 0,
        ];
    }
}
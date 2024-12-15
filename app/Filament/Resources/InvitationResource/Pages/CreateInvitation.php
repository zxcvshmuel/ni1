<?php

namespace App\Filament\Resources\InvitationResource\Pages;

use App\Filament\Resources\InvitationResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateInvitation extends CreateRecord
{
    protected static string $resource = InvitationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Ensure we have a unique slug
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }
        
        // Set the user
        $data['user_id'] = auth()->id();
        
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }
}
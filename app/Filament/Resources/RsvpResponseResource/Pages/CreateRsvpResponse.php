<?php

namespace App\Filament\Resources\RsvpResponseResource\Pages;

use App\Filament\Resources\RsvpResponseResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateRsvpResponse extends CreateRecord
{
    protected static string $resource = RsvpResponseResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

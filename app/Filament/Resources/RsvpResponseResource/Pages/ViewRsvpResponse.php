<?php

namespace App\Filament\Resources\RsvpResponseResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\RsvpResponseResource;

class ViewRsvpResponse extends ViewRecord
{
    protected static string $resource = RsvpResponseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('עריכה'),
        ];
    }
}

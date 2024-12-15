<?php

namespace App\Filament\Resources\RsvpResponseResource\Pages;

use App\Filament\Resources\RsvpResponseResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRsvpResponses extends ListRecords
{
    protected static string $resource = RsvpResponseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\AutomatedMessageResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\AutomatedMessageResource;

class ViewAutomatedMessage extends ViewRecord
{
    protected static string $resource = AutomatedMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('עריכה'),
        ];
    }
}

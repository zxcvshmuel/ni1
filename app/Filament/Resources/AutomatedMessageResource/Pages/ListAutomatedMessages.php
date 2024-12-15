<?php

namespace App\Filament\Resources\AutomatedMessageResource\Pages;

use App\Filament\Resources\AutomatedMessageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAutomatedMessages extends ListRecords
{
    protected static string $resource = AutomatedMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

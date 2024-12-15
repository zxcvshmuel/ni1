<?php

namespace App\Filament\Resources\AutomatedMessageResource\Pages;

use App\Filament\Resources\AutomatedMessageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAutomatedMessage extends EditRecord
{
    protected static string $resource = AutomatedMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make()
                ->label('צפייה'),
            Actions\DeleteAction::make()
                ->label('מחיקה'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

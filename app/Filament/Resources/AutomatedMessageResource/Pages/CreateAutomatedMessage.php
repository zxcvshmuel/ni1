<?php

namespace App\Filament\Resources\AutomatedMessageResource\Pages;

use App\Filament\Resources\AutomatedMessageResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAutomatedMessage extends CreateRecord
{
    protected static string $resource = AutomatedMessageResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

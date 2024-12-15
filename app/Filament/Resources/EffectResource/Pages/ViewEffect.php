<?php

namespace App\Filament\Resources\EffectResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\EffectResource;

class ViewEffect extends ViewRecord
{
    protected static string $resource = EffectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('עריכה'),
        ];
    }
}

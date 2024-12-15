<?php

namespace App\Filament\Resources\SongResource\Pages;

use Filament\Actions;
use App\Filament\Resources\SongResource;
use Filament\Resources\Pages\ViewRecord;

class ViewSong extends ViewRecord
{
    protected static string $resource = SongResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('עריכה'),
        ];
    }
}

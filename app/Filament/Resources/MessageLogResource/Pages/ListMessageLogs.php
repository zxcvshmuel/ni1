<?php

namespace App\Filament\Resources\MessageLogResource\Pages;

use App\Filament\Resources\MessageLogResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Components\Tab;

class ListMessageLogs extends ListRecords
{
    protected static string $resource = MessageLogResource::class;

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('הכל'),
            'pending' => Tab::make('ממתינים')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'pending'))
                ->badge(fn () => $this->getModel()::query()->where('status', 'pending')->count()),
            'sent' => Tab::make('נשלחו')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'sent'))
                ->badge(fn () => $this->getModel()::query()->where('status', 'sent')->count()),
            'failed' => Tab::make('נכשלו')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'failed'))
                ->badge(fn () => $this->getModel()::query()->where('status', 'failed')->count()),
        ];
    }
}
<?php

namespace App\Filament\Resources\InvitationResource\Pages;

use App\Filament\Resources\InvitationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Services\RsvpService;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListInvitations extends ListRecords
{
    protected static string $resource = InvitationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('יצירת הזמנה חדשה'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('כל ההזמנות'),
            'upcoming' => Tab::make('אירועים קרובים')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('event_date', '>=', now()))
                ->badge(fn () => $this->getModel()::query()->where('event_date', '>=', now())->count()),
            'past' => Tab::make('אירועים שעברו')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('event_date', '<', now()))
                ->badge(fn () => $this->getModel()::query()->where('event_date', '<', now())->count()),
        ];
    }
}
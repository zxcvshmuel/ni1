<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Components\Tab;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('יצירת הזמנה'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('הכל'),
            'pending' => Tab::make('ממתינים')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'pending'))
                ->badge(fn () => $this->getModel()::query()->where('status', 'pending')->count()),
            'completed' => Tab::make('הושלמו')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'completed'))
                ->badge(fn () => $this->getModel()::query()->where('status', 'completed')->count()),
            'failed' => Tab::make('נכשלו')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'failed'))
                ->badge(fn () => $this->getModel()::query()->where('status', 'failed')->count()),
        ];
    }
}
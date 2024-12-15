<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('עריכה'),
            Actions\Action::make('resend_invoice')
                ->label('שליחת חשבונית')
                ->icon('heroicon-o-paper-airplane')
                ->requiresConfirmation()
                ->action(fn () => $this->record->sendInvoice()),
        ];
    }
}
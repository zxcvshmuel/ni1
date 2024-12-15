<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Services\CreditService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make()
                ->label('צפייה'),
            Actions\DeleteAction::make()
                ->label('מחיקה'),
            Actions\Action::make('resend_invoice')
                ->label('שליחת חשבונית')
                ->icon('heroicon-o-paper-airplane')
                ->requiresConfirmation()
                ->action(fn () => $this->record->sendInvoice()),
        ];
    }

    protected function afterSave(): void
    {
        // If status changed to completed, add credits to user
        if ($this->record->wasChanged('status') && $this->record->status === 'completed') {
            app(CreditService::class)->addCredits(
                $this->record->user,
                $this->record->credits,
                "Order #{$this->record->id}"
            );
        }

        // If status changed to refunded, remove credits from user
        if ($this->record->wasChanged('status') && $this->record->status === 'refunded') {
            app(CreditService::class)->removeCredits(
                $this->record->user,
                $this->record->credits,
                "Refund - Order #{$this->record->id}"
            );
        }
    }
}
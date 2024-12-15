<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Services\CreditService;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    protected function afterCreate(): void
    {
        // If order is completed, add credits to user
        if ($this->record->status === 'completed') {
            app(CreditService::class)->addCredits(
                $this->record->user,
                $this->record->credits,
                "Order #{$this->record->id}"
            );
        }
    }
}
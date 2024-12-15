<?php

namespace App\Filament\Resources\InvitationResource\Pages;

use App\Filament\Resources\InvitationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Services\MessageService;

class EditInvitation extends EditRecord
{
    protected static string $resource = InvitationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make()
                ->label('צפייה'),
            Actions\DeleteAction::make()
                ->label('מחיקה'),
            Actions\Action::make('preview')
                ->label('תצוגה מקדימה')
                ->url(fn () => route('invitations.preview', $this->record))
                ->openUrlInNewTab()
                ->icon('heroicon-o-eye'),
            Actions\Action::make('copy_link')
                ->label('העתק קישור')
                ->icon('heroicon-o-link')
                ->action(fn () => "navigator.clipboard.writeText('" . route('invitations.show', $this->record->slug) . "')"),
        ];
    }

    protected function afterSave(): void
    {
        // Schedule automated messages if needed
        if ($this->record->wasChanged(['event_date', 'is_active'])) {
            app(MessageService::class)->scheduleMessages($this->record);
        }
    }

    protected function getFooterWidgets(): array
    {
        return [
            // Will implement these widgets later
            // InvitationStatsWidget::class,
            // RsvpListWidget::class,
        ];
    }
}
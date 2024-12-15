<?php

namespace App\Filament\Resources\InvitationResource\Pages;

use App\Filament\Resources\InvitationResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Services\RsvpService;

class ViewInvitation extends ViewRecord
{
    protected static string $resource = InvitationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('עריכה'),
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

    protected function getFooterWidgets(): array
    {
        return [
            // Will implement these widgets later
            // InvitationStatsWidget::class,
            // RsvpListWidget::class,
        ];
    }
}
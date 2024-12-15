<?php

namespace App\Filament\Resources\SettingResource\Pages;

use App\Filament\Resources\SettingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSetting extends EditRecord
{
    protected static string $resource = SettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make()
                ->label('צפייה'),
            Actions\DeleteAction::make()
                ->label('מחיקה')
                ->disabled(fn () => $this->record->locked),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Handle different payload types
        $value = $data['payload']['value'] ?? null;
        
        switch ($data['type']) {
            case 'number':
                $value = (float) $value;
                break;
            case 'boolean':
                $value = (bool) $value;
                break;
            case 'array':
            case 'json':
                if (is_string($value)) {
                    $value = json_decode($value, true);
                }
                break;
        }

        $data['payload'] = ['value' => $value];
        return $data;
    }
}
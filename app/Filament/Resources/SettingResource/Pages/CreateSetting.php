<?php

namespace App\Filament\Resources\SettingResource\Pages;

use App\Filament\Resources\SettingResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSetting extends CreateRecord
{
    protected static string $resource = SettingResource::class;

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
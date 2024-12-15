<?php

namespace App\Filament\Resources\TemplateCategoryResource\Pages;

use App\Filament\Resources\TemplateCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTemplateCategory extends CreateRecord
{
    protected static string $resource = TemplateCategoryResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

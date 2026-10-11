<?php

namespace App\Filament\Resources\ConfigurationResource\Pages;

use App\Filament\Resources\ConfigurationResource\ConfigurationResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\Width;

class EditConfiguration extends EditRecord
{
    protected static string $resource = ConfigurationResource::class;

    protected static ?Width $formMaxWidth = Width::Full;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

<?php

namespace App\Filament\Resources\ConfigurationResource\Pages;

use App\Filament\Resources\ConfigurationResource\ConfigurationResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;

class CreateConfiguration extends CreateRecord
{
    protected static string $resource = ConfigurationResource::class;

    protected static ?Width $formMaxWidth = Width::Full;
}

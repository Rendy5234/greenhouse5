<?php

namespace App\Filament\Resources\SensorReadingResource\Pages;

use App\Filament\Resources\SensorReadingResource\SensorReadingResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\Width;

class ViewSensorReading extends ViewRecord
{
    protected static string $resource = SensorReadingResource::class;

    protected static ?Width $formMaxWidth = Width::Full;
}

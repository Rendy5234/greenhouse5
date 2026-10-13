<?php

namespace App\Filament\Resources\SensorReadingResource;

use App\Filament\Resources\SensorReadingResource\Pages\ListSensorReadings;
use App\Filament\Resources\SensorReadingResource\Pages\ViewSensorReading;
use App\Filament\Resources\SensorReadingResource\Schemas\SensorReadingForm;
use App\Filament\Resources\SensorReadingResource\Tables\SensorReadingTable;
use App\Models\SensorReading;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SensorReadingResource extends Resource
{
    protected static ?string $model = SensorReading::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static ?string $navigationLabel = 'Sensor Readings';

    protected static ?int $navigationSort = 2;

    protected static ?string $slug = 'sensor-readings';

    public static function getNavigationGroup(): ?string
    {
        return 'Monitoring';
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) SensorReading::query()->count();
    }

    public static function form(Schema $schema): Schema
    {
        return (new SensorReadingForm)->form($schema);
    }

    public static function table(Table $table): Table
    {
        return (new SensorReadingTable)->table($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSensorReadings::route('/'),
            'view' => ViewSensorReading::route('/{record}'),
        ];
    }

    // Disable create, edit, delete - read-only resource
    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function canViewAny(): bool
    {
        return true;
    }

    public static function canView($record): bool
    {
        return true;
    }
}

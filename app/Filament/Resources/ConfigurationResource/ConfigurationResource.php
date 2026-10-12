<?php

namespace App\Filament\Resources\ConfigurationResource;

use App\Filament\Resources\ConfigurationResource\Pages\CreateConfiguration;
use App\Filament\Resources\ConfigurationResource\Pages\EditConfiguration;
use App\Filament\Resources\ConfigurationResource\Pages\ListConfigurations;
use App\Filament\Resources\ConfigurationResource\Schemas\ConfigurationForm;
use App\Filament\Resources\ConfigurationResource\Tables\ConfigurationTable;
use App\Models\Configuration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ConfigurationResource extends Resource
{
    protected static ?string $model = Configuration::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCog;

    protected static ?string $navigationLabel = 'Configurations';

    protected static ?int $navigationSort = 1;

    protected static ?string $slug = 'configurations';

    public static function getNavigationGroup(): ?string
    {
        return 'IoT Settings';
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) Configuration::query()->count();
    }

    public static function form(Schema $schema): Schema
    {
        return (new ConfigurationForm)->form($schema);
    }

    public static function table(Table $table): Table
    {
        return (new ConfigurationTable)->table($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListConfigurations::route('/'),
            'create' => CreateConfiguration::route('/create'),
            'edit' => EditConfiguration::route('/{record}/edit'),
        ];
    }

    /**
     * Judul hasil global search (fallback ke `key` jika `label` kosong).
     *
     * @param  Configuration  $record
     */
    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->label ?: $record->key;
    }

    /**
     * Detail tambahan yang ditampilkan di hasil global search.
     *
     * @param  Configuration  $record
     * @return array<string, string>
     */
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Key' => (string) $record->key,
            'Nilai' => (string) $record->value,
            'Tipe' => (string) $record->type,
        ];
    }

    /**
     * Kolom yang ikut dicari dari kolom pencarian global.
     *
     * @return array<string>
     */
    public static function getGloballySearchableAttributes(): array
    {
        return ['key', 'label', 'value', 'description'];
    }

    public static function getGlobalSearchResultsLimit(): int
    {
        return 10;
    }
}

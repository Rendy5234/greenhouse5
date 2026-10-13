<?php

namespace App\Filament\Resources\SensorReadingResource\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SensorReadingForm
{
    public function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Data Sensor')
                ->description('Data pembacaan sensor greenhouse (hanya baca).')
                ->columnSpanFull()
                ->columns(['default' => 1, 'md' => 2])
                ->schema([
                    TextInput::make('suhu')
                        ->label('Suhu (°C)')
                        ->disabled()
                        ->columnSpan(['default' => 1, 'md' => 2]),

                    TextInput::make('kelembapan_udara')
                        ->label('Kelembapan Udara (%)')
                        ->disabled()
                        ->columnSpan(['default' => 1, 'md' => 2]),

                    TextInput::make('kelembapan_tanah')
                        ->label('Kelembapan Tanah (%)')
                        ->disabled()
                        ->columnSpan(['default' => 1, 'md' => 2]),

                    Select::make('relay_fan')
                        ->label('Kipas (Relay Fan)')
                        ->options([
                            '0' => 'Mati',
                            '1' => 'Hidup',
                        ])
                        ->disabled()
                        ->columnSpan(['default' => 1, 'md' => 2]),

                    Select::make('relay_pump')
                        ->label('Pompa (Relay Pump)')
                        ->options([
                            '0' => 'Mati',
                            '1' => 'Hidup',
                        ])
                        ->disabled()
                        ->columnSpan(['default' => 1, 'md' => 2]),

                    Select::make('led_mode')
                        ->label('Mode LED')
                        ->options([
                            'normal' => 'Normal',
                            'kipas' => 'Kipas',
                            'pompa' => 'Pompa',
                            'kritis' => 'Kritis',
                        ])
                        ->disabled()
                        ->columnSpan(['default' => 1, 'md' => 2]),

                    DateTimePicker::make('created_at')
                        ->label('Waktu Pembacaan')
                        ->disabled()
                        ->displayFormat('d M Y H:i:s')
                        ->columnSpan(['default' => 1, 'md' => 2]),
                ]),

            Section::make('Snapshot Threshold (Saat Pembacaan)')
                ->description('Nilai threshold yang berlaku saat data ini terekam. Berguna untuk analisis historis meskipun threshold berubah di kemudian hari.')
                ->columnSpanFull()
                ->columns(['default' => 1, 'md' => 2])
                ->schema([
                    TextInput::make('temp_on_used')
                        ->label('Temp ON (°C)')
                        ->disabled()
                        ->columnSpan(['default' => 1, 'md' => 2]),

                    TextInput::make('temp_off_used')
                        ->label('Temp OFF (°C)')
                        ->disabled()
                        ->columnSpan(['default' => 1, 'md' => 2]),

                    TextInput::make('soil_dry_used')
                        ->label('Soil Dry (%)')
                        ->disabled()
                        ->columnSpan(['default' => 1, 'md' => 2]),

                    TextInput::make('soil_wet_used')
                        ->label('Soil Wet (%)')
                        ->disabled()
                        ->columnSpan(['default' => 1, 'md' => 2]),
                ]),
        ]);
    }
}

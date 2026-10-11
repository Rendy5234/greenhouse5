<?php

namespace App\Filament\Resources\ConfigurationResource\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;

class ConfigurationForm
{
    public function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Informasi Konfigurasi')
                ->columnSpanFull()
                ->description('Ubah nilai threshold untuk kontrol otomatis greenhouse.')
                ->schema([
                    TextInput::make('key')
                        ->label('Key')
                        ->readonly()
                        ->disabled(),

                    TextInput::make('label')
                        ->label('Label')
                        ->required()
                        ->maxLength(255),

                    // Value field: dinamis berdasarkan key
                    TextInput::make('value')
                        ->label('Nilai Threshold')
                        ->required()
                        ->numeric()
                        ->step(fn(Get $get): float|int => $get('type') === 'integer' ? 1 : 0.1)
                        ->rule(fn(Get $get): string => $get('type') === 'integer' ? 'integer' : 'numeric'),

                    Select::make('type')
                        ->label('Tipe Data')
                        ->options([
                            'float' => 'Float (desimal)',
                            'integer' => 'Integer (bulat)',
                        ])
                        ->live()
                        ->default('float')
                        ->disabled(),

                    Textarea::make('description')
                        ->label('Keterangan')
                        ->rows(3)
                        ->nullable()
                        ->helperText('Deskripsi opsional untuk dokumentasi.')
                        ->columnSpanFull(),
                ])
                ->columns([
                    'default' => 1,
                    'md' => 2,
                ]),
        ]);
    }
}

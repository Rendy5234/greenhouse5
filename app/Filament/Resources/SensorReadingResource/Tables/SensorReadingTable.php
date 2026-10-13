<?php

namespace App\Filament\Resources\SensorReadingResource\Tables;

use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SensorReadingTable
{
    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->orderBy('created_at', 'desc'))
            ->columns([
                TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d M Y H:i:s')
                    ->sortable()
                    ->weight('medium')
                    ->searchable(false),

                TextColumn::make('suhu')
                    ->label('Suhu (°C)')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('kelembapan_udara')
                    ->label('Kelembapan Udara (%)')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('kelembapan_tanah')
                    ->label('Kelembapan Tanah (%)')
                    ->sortable()
                    ->searchable(),

                BadgeColumn::make('relay_fan')
                    ->label('Kipas')
                    ->colors([
                        'success' => '1',
                        'gray' => '0',
                    ])
                    ->formatStateUsing(fn ($state) => $state ? 'Hidup' : 'Mati')
                    ->sortable(),

                BadgeColumn::make('relay_pump')
                    ->label('Pompa')
                    ->colors([
                        'success' => '1',
                        'gray' => '0',
                    ])
                    ->formatStateUsing(fn ($state) => $state ? 'Hidup' : 'Mati')
                    ->sortable(),

                BadgeColumn::make('led_mode')
                    ->label('Mode LED')
                    ->colors([
                        'success' => 'normal',
                        'info' => 'kipas',
                        'warning' => 'pompa',
                        'danger' => 'kritis',
                    ])
                    ->sortable()
                    ->searchable(),

                TextColumn::make('temp_on_used')
                    ->label('Temp ON (°C)')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('temp_off_used')
                    ->label('Temp OFF (°C)')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('soil_dry_used')
                    ->label('Soil Dry (%)')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('soil_wet_used')
                    ->label('Soil Wet (%)')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('led_mode')
                    ->label('Mode LED')
                    ->options([
                        'normal' => 'Normal',
                        'kipas' => 'Kipas',
                        'pompa' => 'Pompa',
                        'kritis' => 'Kritis',
                    ])
                    ->searchable()
                    ->preload(),

                Filter::make('created_at')
                    ->label('Rentang Waktu')
                    ->form([
                        DatePicker::make('from')->label('Dari'),
                        DatePicker::make('until')->label('Sampai'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'], fn (Builder $query, $date) => $query->whereDate('created_at', '>=', $date))
                            ->when($data['until'], fn (Builder $query, $date) => $query->whereDate('created_at', '<=', $date));
                    }),

                Filter::make('relay_fan')
                    ->label('Kipas Aktif')
                    ->query(fn (Builder $query) => $query->where('relay_fan', true))
                    ->toggle(),

                Filter::make('relay_pump')
                    ->label('Pompa Aktif')
                    ->query(fn (Builder $query) => $query->where('relay_pump', true))
                    ->toggle(),
            ])
            ->actions([])
            ->bulkActions([])
            ->paginated([25, 50, 100])
            ->defaultSort('created_at', 'desc');
    }
}

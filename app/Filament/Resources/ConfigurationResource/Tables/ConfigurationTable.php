<?php

namespace App\Filament\Resources\ConfigurationResource\Tables;

use App\Filament\Resources\ConfigurationResource\ConfigurationResource;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ConfigurationTable
{
    public function table(Table $table): Table
    {
        return $table
            ->recordUrl(fn ($record) => ConfigurationResource::getUrl('edit', ['record' => $record]))
            ->modifyQueryUsing(fn ($query) => $query->orderBy('key'))
            ->columns([
                TextColumn::make('key')
                    ->label('Key')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                TextColumn::make('label')
                    ->label('Label')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('value')
                    ->label('Nilai')
                    ->sortable(),

                TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'float' => 'info',
                        'integer' => 'success',
                        default => 'gray',
                    }),

                TextColumn::make('description')
                    ->label('Keterangan')
                    ->limit(30)
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([])
            ->actions([
                EditAction::make()
                    ->icon('heroicon-o-pencil'),
            ])
            ->bulkActions([])
            ->defaultSort('key', 'asc')
            ->paginated(false); // data hanya 4 baris
    }
}

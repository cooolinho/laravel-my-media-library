<?php

namespace App\Filament\Resources\WarezLinks\Tables;

use App\Models\WarezLink;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WarezLinksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make(WarezLink::title)
                    ->label('Titel')
                    ->searchable(),
                TextColumn::make(WarezLink::url)
                    ->label('URL')
                    ->searchable()
                    ->limit(50),
                TextColumn::make('placeholder_type')
                    ->label('Platzhalter-Typ')
                    ->formatStateUsing(fn(string $state): string => WarezLink::getPlaceholderTypes()[$state] ?? $state)
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        WarezLink::PLACEHOLDER_SERIES_NAME => 'success',
                        WarezLink::PLACEHOLDER_TVDB_ID => 'info',
                        WarezLink::PLACEHOLDER_SERIES_SLUG => 'warning',
                        default => 'gray',
                    }),
                IconColumn::make(WarezLink::active)
                    ->label('Aktiv')
                    ->boolean()
                    ->sortable(),
                TextColumn::make(WarezLink::created_at)
                    ->label('Erstellt am')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make(WarezLink::updated_at)
                    ->label('Aktualisiert am')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

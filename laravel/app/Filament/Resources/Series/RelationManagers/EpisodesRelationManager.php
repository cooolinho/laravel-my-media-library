<?php

namespace App\Filament\Resources\Series\RelationManagers;

use App\Filament\Resources\Episodes\EpisodeResource;
use App\Filament\Resources\Episodes\Tables\EpisodesTable;
use App\Models\Episode;
use App\Models\Series;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Checkbox;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EpisodesRelationManager extends RelationManager
{
    protected static string $relationship = Series::has_many_episodes;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Checkbox::make(Episode::owned),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute(Episode::theTvDbId)
            ->columns([
                TextColumn::make(Episode::seasonNumber),
                TextColumn::make(Episode::number),
                TextColumn::make(Episode::has_one_data . '.' . 'nameTranslation'),
                TextColumn::make(Episode::theTvDbId),
                IconColumn::make(Episode::owned)
                    ->boolean(),
            ])
            ->filters([
                EpisodesTable::getOwnedFilter(),
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                EditAction::make(),
                ViewAction::make()
                    ->url(fn($record) => EpisodeResource::getUrl('view', ['record' => $record->id]))
                    ->openUrlInNewTab(true),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

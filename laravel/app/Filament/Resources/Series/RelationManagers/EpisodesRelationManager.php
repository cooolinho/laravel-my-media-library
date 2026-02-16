<?php

namespace App\Filament\Resources\Series\RelationManagers;

use App\Filament\Resources\Episodes\Actions\SetNotOwnedBulkAction;
use App\Filament\Resources\Episodes\Actions\SetOwnedBulkAction;
use App\Filament\Resources\Episodes\Actions\ToggleOwnedAction;
use App\Filament\Resources\Episodes\Schemas\EpisodeForm;
use App\Filament\Resources\Episodes\Tables\Filters\OwnedFilter;
use App\Filament\Resources\Episodes\Tables\Filters\SeasonFilter;
use App\Models\Episode;
use App\Models\Series;
use App\Models\TheTvDB\EpisodeData;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EpisodesRelationManager extends RelationManager
{
    protected static string $relationship = Series::has_many_episodes;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema(EpisodeForm::getComponents(false));
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute(Episode::theTvDbId)
            ->columns([
                IconColumn::make(Episode::owned)
                    ->boolean(),
                TextColumn::make(Episode::seasonNumber),
                TextColumn::make(Episode::number),
                TextColumn::make(Episode::has_one_data . '.' . EpisodeData::name)
                    ->label('Name'),
                TextColumn::make(Episode::theTvDbId),
            ])
            ->filters([
                OwnedFilter::make(),
                SeasonFilter::make($this->getSeriesId()),
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    ToggleOwnedAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ])
                    ->color(Color::Indigo)
                    ->icon(Heroicon::ListBullet)
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    SetOwnedBulkAction::make(),
                    SetNotOwnedBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public function getSeriesId(): int
    {
        return $this->getOwnerRecord()->getAttribute(Series::id);
    }
}

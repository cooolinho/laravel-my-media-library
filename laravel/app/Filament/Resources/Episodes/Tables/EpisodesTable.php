<?php

namespace App\Filament\Resources\Episodes\Tables;

use App\Filament\Resources\Episodes\Actions\EditNotesAction;
use App\Filament\Resources\Episodes\Actions\SetNotOwnedBulkAction;
use App\Filament\Resources\Episodes\Actions\SetOwnedBulkAction;
use App\Filament\Resources\Episodes\Actions\ToggleOwnedAction;
use App\Filament\Resources\Episodes\Tables\Filters\OwnedFilter;
use App\Filament\Resources\Episodes\Tables\Filters\SeasonFilter;
use App\Filament\Resources\Episodes\Tables\Filters\SeriesFilter;
use App\Filament\Resources\Episodes\Tables\Filters\SpecialsFilter;
use App\Filament\Resources\Episodes\Tables\Filters\WithoutDataFilter;
use App\Filament\Resources\Episodes\Tables\Filters\WithoutNameFilter;
use App\Filament\Resources\Episodes\Tables\Filters\YearFilter;
use App\Models\Episode;
use App\Models\Series;
use App\Models\TheTvDB\EpisodeData;
use App\Models\TheTvDB\EpisodeTranslation;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EpisodesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                IconColumn::make(Episode::owned)
                    ->boolean(),
                TextColumn::make(Episode::seasonNumber),
                TextColumn::make(Episode::number),
                TextColumn::make(Episode::has_one_data . '.' . EpisodeData::name)
                    ->label('Name'),
                TextColumn::make(Episode::belongs_to_series . '.' . Series::name)
                    ->label('TV Show'),
                TextColumn::make(Episode::theTvDbId),
            ])
            ->searchable()
            ->filters([
                OwnedFilter::make(),
                SeriesFilter::make(),
                SeasonFilter::make(),
                YearFilter::make(),
                WithoutDataFilter::make(),
                WithoutNameFilter::make(),
                SpecialsFilter::make(),
            ])
            ->searchUsing(function (Builder $query, string $search) {
                $query->orWhere(Episode::notes, 'like', "%{$search}%");
                $query->orWhere(Episode::theTvDbId, 'like', "%{$search}%");
                $query->orWhereHas(Episode::has_one_data, function (Builder $subQuery) use ($search) {
                    $subQuery->whereHas(EpisodeData::has_many_translations, function (Builder $translationQuery) use ($search) {
                        $translationQuery
                            ->where(EpisodeTranslation::name, 'like', "%{$search}%")
                            ->orWhere(EpisodeTranslation::overview, 'like', "%{$search}%");
                    });
                });
            })
            ->recordActions([
                ActionGroup::make([
                    ToggleOwnedAction::make(),
                    EditNotesAction::make(),
                    ViewAction::make(),
                    EditAction::make(),
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
}

<?php

namespace App\Filament\Resources\Episodes\Tables;

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
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EpisodesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                IconColumn::make(Episode::owned)
                    ->boolean(),
                TextColumn::make(Episode::seasonNumber)
                    ->searchable(),
                TextColumn::make(Episode::number)
                    ->searchable(),
                TextColumn::make(Episode::has_one_data . '.' . EpisodeData::name)
                    ->searchable(true, function ($query, string $search) {
                        $query->orWhereHas(Episode::has_one_data, function ($subQuery) use ($search) {
                            $subQuery->where(EpisodeData::translations, 'like', "%{$search}%");
                        });
                    }),
                TextColumn::make(Episode::belongs_to_series . '.' . Series::name),
                TextColumn::make(Episode::theTvDbId)
                    ->searchable(),
            ])
            ->filters([
                OwnedFilter::make(),
                SeriesFilter::make(),
                SeasonFilter::make(),
                YearFilter::make(),
                WithoutDataFilter::make(),
                WithoutNameFilter::make(),
                SpecialsFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

<?php

namespace App\Filament\Resources\Series\Tables;

use App\Models\Episode;
use App\Models\Series;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SeriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn($query) => $query->with(['artworks', 'episodes']))
            ->columns([
                IconColumn::make('complete')
                    ->boolean()
                    ->state(function (Series $record) {
                        return $record->episodesComplete();
                    }),
                TextColumn::make(Series::name)
                    ->searchable()
                    ->sortable(),
                TextColumn::make(Series::theTvDbId)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('countEpisodes')
                    ->label('Episodes')
                    ->state(function (Series $record) {
                        return $record->episodes->count();
                    })
                    ->sortable(query: function ($query, string $direction) {
                        $query->withCount(Series::has_many_episodes)->orderBy(Series::has_many_episodes . '_count', $direction);
                    }),
                TextColumn::make('ownedPercentage')
                    ->label('Percentage')
                    ->state(function (Series $record) {
                        return $record->getEpisodeOwnedPercentage() . '%';
                    })
                    ->sortable(query: function ($query, string $direction) {
                        $query
                            ->withCount([Series::has_many_episodes, Series::has_many_episodes . ' as owned_episodes_count' => function ($query) {
                                $query->where(Episode::owned, true);
                            }])
                            ->orderByRaw('(owned_episodes_count / ' . Series::has_many_episodes . '_count) * 100 ' . $direction);
                    })
            ])
            ->filters([
                //
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

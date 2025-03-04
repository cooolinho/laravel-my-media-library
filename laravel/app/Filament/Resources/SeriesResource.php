<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SeriesResource\Pages;
use App\Filament\Resources\SeriesResource\RelationManagers;
use App\Filament\Resources\SeriesResource\Widgets\SeriesStatsWidget;
use App\Models\Episode;
use App\Models\Series;
use App\Models\TheTvDB\SeriesData;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SeriesResource extends Resource
{
    protected static ?string $model = Series::class;

    protected static ?string $navigationIcon = 'heroicon-o-tv';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\IconColumn::make('complete')
                ->boolean()
                ->state(function (Series $record) {
                    return $record->episodesComplete();
                }),
                Tables\Columns\TextColumn::make(Series::name)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make(Series::theTvDbId)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('countEpisodes')
                    ->label('Episodes')
                    ->state(function (Series $record) {
                        return $record->episodes->count();
                    })
                    ->sortable(query: function ($query, string $direction) {
                        $query->withCount(Series::has_many_episodes)->orderBy(Series::has_many_episodes . '_count', $direction);
                    }),
                Tables\Columns\TextColumn::make('ownedPercentage')
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
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\EpisodesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSeries::route('/'),
            'create' => Pages\CreateSeries::route('/create'),
            'view' => Pages\ViewSeries::route('/{record}'),
            'edit' => Pages\EditSeries::route('/{record}/edit'),
        ];
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\ImageEntry::make(Series::has_one_data . '.' . SeriesData::image)
                    ->label('Preview')
                    ->height('300px')
                    ->columnSpanFull(),
                Infolists\Components\TextEntry::make(Series::has_one_data . '.' . SeriesData::name)
                    ->label('Name'),
                Infolists\Components\TextEntry::make(Series::has_one_data . '.' . SeriesData::year)
                    ->label('Year'),
                Infolists\Components\TextEntry::make(Series::has_one_data . '.' . SeriesData::firstAired)
                    ->label('First Aired'),
                Infolists\Components\TextEntry::make(Series::has_one_data . '.' . SeriesData::lastAired)
                    ->label('Last Aired'),
                Infolists\Components\TextEntry::make(Series::has_one_data . '.' . SeriesData::status)
                    ->label('Status'),
                Infolists\Components\TextEntry::make(Series::has_one_data . '.' . SeriesData::originalCountry)
                    ->label('Country'),
                Infolists\Components\TextEntry::make(Series::has_one_data . '.' . SeriesData::originalLanguage)
                    ->label('Language'),
                Infolists\Components\TextEntry::make(Series::has_one_data . '.' . SeriesData::averageRuntime)
                    ->label('Average Runtime'),
                Infolists\Components\TextEntry::make(Series::has_one_data . '.' . SeriesData::overview)
                    ->label('Overview')
                    ->columnSpanFull(),
            ]);
    }

    public static function getWidgets(): array
    {
        return [
            SeriesStatsWidget::class,
        ];
    }
}

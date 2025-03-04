<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SeriesResource\Pages;
use App\Filament\Resources\SeriesResource\RelationManagers;
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
                Tables\Columns\TextColumn::make(Series::name)
                    ->searchable(),
                Tables\Columns\TextColumn::make(Series::theTvDbId)
                    ->searchable(),
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
}

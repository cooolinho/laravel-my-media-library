<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EpisodeResource\Pages;
use App\Models\Episode;
use App\Models\Series;
use App\Models\TheTvDB\EpisodeData;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EpisodeResource extends Resource
{
    protected static ?string $model = Episode::class;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\IconColumn::make(Episode::owned)
                    ->boolean(),
                Tables\Columns\TextColumn::make(Episode::seasonNumber)
                    ->searchable(),
                Tables\Columns\TextColumn::make(Episode::number)
                    ->searchable(),
                Tables\Columns\TextColumn::make(Episode::has_one_data . '.' . EpisodeData::name)
                    ->searchable(true, function ($query, string $search) {
                        $query->orWhereHas(Episode::has_one_data, function ($subQuery) use ($search) {
                            $subQuery->where(EpisodeData::translations, 'like', "%{$search}%");
                        });
                    }),
                Tables\Columns\TextColumn::make(Episode::belongs_to_series . '.' . Series::name),
                Tables\Columns\TextColumn::make(Episode::theTvDbId)
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make(Episode::series_id)
                    ->label('Series')
                    ->options(
                        Series::query()
                            ->orderBy(Series::name, 'ASC')
                            ->get()
                            ->pluck(Series::name, Series::id)
                    ),
                self::getOwnedFilter(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEpisodes::route('/'),
            'create' => Pages\CreateEpisode::route('/create'),
            'view' => Pages\ViewEpisode::route('/{record}'),
            'edit' => Pages\EditEpisode::route('/{record}/edit'),
        ];
    }

    public static function getOwnedFilter()
    {
        return Tables\Filters\SelectFilter::make(Episode::owned)
            ->options([
                true => 'Yes',
                false => 'No',
            ]);
    }
}

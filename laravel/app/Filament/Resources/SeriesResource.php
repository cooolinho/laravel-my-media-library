<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SeriesResource\Pages;
use App\Filament\Resources\SeriesResource\RelationManagers;
use App\Models\Series;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SeriesResource extends Resource
{
    protected static ?string $model = Series::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make(Series::name)
                    ->required()
                    ->name(Series::name)
                    ->maxLength(255),
                TextInput::make(Series::theTvDbId)
                    ->required()
                    ->name(Series::theTvDbId)
                    ->columnSpanFull(),
            ]);
    }

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
}

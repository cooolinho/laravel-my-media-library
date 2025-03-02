<?php

namespace App\Filament\Resources\SeriesResource\RelationManagers;

use App\Models\Episode;
use App\Models\Series;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class EpisodesRelationManager extends RelationManager
{
    protected static string $relationship = Series::has_many_episodes;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('theTvDbId')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute(Episode::theTvDbId)
            ->columns([
                Tables\Columns\TextColumn::make(Episode::number),
                Tables\Columns\TextColumn::make(Episode::season),
                Tables\Columns\TextColumn::make(Episode::theTvDbId),
                Tables\Columns\IconColumn::make(Episode::owned)
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}

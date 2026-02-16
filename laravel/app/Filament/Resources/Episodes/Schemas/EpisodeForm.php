<?php

namespace App\Filament\Resources\Episodes\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class EpisodeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('number')
                    ->required()
                    ->numeric(),
                TextInput::make('seasonNumber')
                    ->required()
                    ->numeric(),
                Toggle::make('owned')
                    ->required(),
                TextInput::make('theTvDbId')
                    ->required()
                    ->numeric(),
                Select::make('series_id')
                    ->relationship('series', 'name')
                    ->required(),
            ]);
    }
}

<?php

namespace App\Filament\Resources\Series\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SeriesForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('theTvDbId')
                    ->required()
                    ->numeric(),
            ]);
    }
}

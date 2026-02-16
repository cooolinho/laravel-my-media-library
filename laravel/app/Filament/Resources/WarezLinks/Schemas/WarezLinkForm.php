<?php

namespace App\Filament\Resources\WarezLinks\Schemas;

use App\Models\WarezLink;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class WarezLinkForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make(WarezLink::title)
                    ->columnSpanFull(),
                TextInput::make(WarezLink::url)
                    ->helperText('pls put a placeholder <SERIES_NAME> here to replace this')
                    ->columnSpanFull(),
            ]);
    }
}

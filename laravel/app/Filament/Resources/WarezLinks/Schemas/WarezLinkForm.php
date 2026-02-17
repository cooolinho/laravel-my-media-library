<?php

namespace App\Filament\Resources\WarezLinks\Schemas;

use App\Models\WarezLink;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class WarezLinkForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make(WarezLink::title)
                    ->label('Titel')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make(WarezLink::url)
                    ->label('URL')
                    ->helperText('Verwenden Sie Platzhalter <PLACEHOLDER> in der URL')
                    ->required()
                    ->columnSpanFull(),
                Select::make(WarezLink::placeholderType)
                    ->label('Platzhalter-Typ')
                    ->options(WarezLink::getPlaceholderTypes())
                    ->default(WarezLink::PLACEHOLDER_SERIES_NAME)
                    ->required()
                    ->helperText('Wählen Sie, welcher Platzhalter in der URL ersetzt werden soll')
                    ->columnSpanFull(),
                Toggle::make(WarezLink::active)
                    ->label('Aktiv')
                    ->helperText('Nur aktive Links werden in der Serie angezeigt')
                    ->default(true)
                    ->columnSpanFull(),
            ]);
    }
}

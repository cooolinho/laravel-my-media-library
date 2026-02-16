<?php

namespace App\Filament\Resources\WarezLinks;

use App\Filament\Resources\WarezLinks\Pages\CreateWarezLink;
use App\Filament\Resources\WarezLinks\Pages\EditWarezLink;
use App\Filament\Resources\WarezLinks\Pages\ListWarezLinks;
use App\Filament\Resources\WarezLinks\Schemas\WarezLinkForm;
use App\Filament\Resources\WarezLinks\Tables\WarezLinksTable;
use App\Models\WarezLink;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class WarezLinkResource extends Resource
{
    protected static ?string $model = WarezLink::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Link;
    protected static ?string $navigationLabel = 'Links';

    protected static ?string $recordTitleAttribute = 'title';
    protected static string|null|\UnitEnum $navigationGroup = 'Administration';
    protected static ?int $navigationSort = 97;

    public static function form(Schema $schema): Schema
    {
        return WarezLinkForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WarezLinksTable::configure($table);
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
            'index' => ListWarezLinks::route('/'),
            'create' => CreateWarezLink::route('/create'),
            'edit' => EditWarezLink::route('/{record}/edit'),
        ];
    }
}

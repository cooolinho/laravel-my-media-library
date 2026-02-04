<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WarezLinkResource\Pages;
use App\Models\WarezLink;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class WarezLinkResource extends Resource
{
    protected static ?string $model = WarezLink::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Warez Links';
    protected static ?int $navigationSort = 80;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make(WarezLink::title)
                    ->columnSpanFull(),
                TextInput::make(WarezLink::url)
                    ->helperText('pls put a placeholder <SERIES_NAME> here to replace this')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make(WarezLink::title)
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
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
            'index' => Pages\ListWarezLinks::route('/'),
            'create' => Pages\CreateWarezLink::route('/create'),
            'edit' => Pages\EditWarezLink::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\Resources\SeriesResource\Pages;

use App\Filament\Resources\SeriesResource;
use App\Models\Series;
use Filament\Actions;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;

class EditSeries extends EditRecord
{
    protected static string $resource = SeriesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make(Series::name)
                    ->required()
                    ->name(Series::name)
                    ->maxLength(255),
                TextInput::make(Series::theTvDbId)
                    ->readOnly()
                    ->name(Series::theTvDbId)
                    ->columnSpanFull(),
            ]);
    }
}

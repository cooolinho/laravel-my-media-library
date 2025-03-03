<?php

namespace App\Filament\Resources\SeriesResource\Pages;

use App\Filament\Resources\SeriesResource;
use App\Filament\UriParameterTrait;
use App\Models\Series;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;

class CreateSeries extends CreateRecord
{
    use UriParameterTrait;

    protected static string $resource = SeriesResource::class;

    public function form(Form $form): Form
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
}

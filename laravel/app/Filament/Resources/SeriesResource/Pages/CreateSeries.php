<?php

namespace App\Filament\Resources\SeriesResource\Pages;

use App\Filament\Resources\SeriesResource;
use App\Filament\UriParameterTrait;
use Filament\Resources\Pages\CreateRecord;

class CreateSeries extends CreateRecord
{
    use UriParameterTrait;

    protected static string $resource = SeriesResource::class;
}

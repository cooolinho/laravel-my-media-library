<?php

namespace App\Filament\Resources\Series\Pages;

use App\Filament\Resources\Series\SeriesResource;
use App\Filament\Resources\UriParameterTrait;
use Filament\Resources\Pages\CreateRecord;

class CreateSeries extends CreateRecord
{
    use UriParameterTrait;

    protected static string $resource = SeriesResource::class;
}

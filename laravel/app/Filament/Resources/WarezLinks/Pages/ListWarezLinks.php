<?php

namespace App\Filament\Resources\WarezLinks\Pages;

use App\Filament\Resources\WarezLinks\WarezLinkResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListWarezLinks extends ListRecords
{
    protected static string $resource = WarezLinkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

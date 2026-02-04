<?php

namespace App\Filament\Resources\WarezLinkResource\Pages;

use App\Filament\Resources\WarezLinkResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWarezLinks extends ListRecords
{
    protected static string $resource = WarezLinkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\WarezLinkResource\Pages;

use App\Filament\Resources\WarezLinkResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWarezLink extends EditRecord
{
    protected static string $resource = WarezLinkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

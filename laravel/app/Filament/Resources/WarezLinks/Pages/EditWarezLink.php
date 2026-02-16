<?php

namespace App\Filament\Resources\WarezLinks\Pages;

use App\Filament\Resources\WarezLinks\WarezLinkResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditWarezLink extends EditRecord
{
    protected static string $resource = WarezLinkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

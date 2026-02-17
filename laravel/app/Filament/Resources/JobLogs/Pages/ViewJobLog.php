<?php

namespace App\Filament\Resources\JobLogs\Pages;

use App\Filament\Resources\JobLogs\JobLogResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewJobLog extends ViewRecord
{
    protected static string $resource = JobLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}

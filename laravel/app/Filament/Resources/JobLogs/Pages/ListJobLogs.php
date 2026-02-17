<?php

namespace App\Filament\Resources\JobLogs\Pages;

use App\Filament\Resources\JobLogs\JobLogResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListJobLogs extends ListRecords
{
    protected static string $resource = JobLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

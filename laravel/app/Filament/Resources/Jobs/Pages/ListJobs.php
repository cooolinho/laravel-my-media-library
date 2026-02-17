<?php

namespace App\Filament\Resources\Jobs\Pages;

use App\Filament\Resources\Jobs\Actions\CreateJobAction;
use App\Filament\Resources\Jobs\JobResource;
use App\Filament\Resources\Jobs\Widgets\JobsStatsOverviewWidget;
use Filament\Resources\Pages\ListRecords;

class ListJobs extends ListRecords
{
    protected static string $resource = JobResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateJobAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            JobsStatsOverviewWidget::class,
        ];
    }
}

<?php

namespace App\Filament\Resources\Jobs\Pages;

use App\Filament\Resources\Jobs\JobResource;
use App\Filament\Resources\Jobs\Widgets\JobsChartWidget;
use App\Filament\Resources\Jobs\Widgets\JobsStatsOverviewWidget;
use Filament\Resources\Pages\ListRecords;

class ListJobs extends ListRecords
{
    protected static string $resource = JobResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            JobsChartWidget::class,
            JobsStatsOverviewWidget::class,
        ];
    }
}

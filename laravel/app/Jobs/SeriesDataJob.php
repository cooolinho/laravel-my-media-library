<?php

namespace App\Jobs;

use App\Models\Series;
use App\Services\TheTVDBApiService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SeriesDataJob implements ShouldQueue
{
    use Queueable;

    private Series $series;

    public function __construct(Series $series)
    {
        $this->series = $series;
    }

    public function handle(TheTVDBApiService $theTVDBApiService): void
    {
        $theTVDBApiService->login();
        $theTVDBApiService->importSeriesData($this->series);
    }
}

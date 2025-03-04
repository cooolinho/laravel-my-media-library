<?php

namespace App\Jobs;

use App\Models\Series;
use App\Services\TheTVDBApiService;
use App\Settings\JobSettings;
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

    public function handle(JobSettings $settings, TheTVDBApiService $theTVDBApiService): void
    {
        if (!$settings->seriesDataJob_enabled) {
            $this->fail(new JobNotActivatedException());
            return;
        }

        $theTVDBApiService->importSeriesData($this->series);
    }
}

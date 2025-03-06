<?php

namespace App\Jobs;

use App\Jobs\AbstractBaseJob as Job;
use App\Jobs\Exceptions\JobNotActivatedException;
use App\Models\Series;
use App\Services\ImportDataService;
use App\Settings\JobSettings;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SeriesDataJob extends Job implements ShouldQueue
{
    use Queueable;

    private Series $series;

    public function __construct(Series $series)
    {
        $this->series = $series;
    }

    public function handle(JobSettings $settings, ImportDataService $service): void
    {
        if (!$settings->seriesDataJob_enabled) {
            $this->fail(new JobNotActivatedException());
            return;
        }

        $service->importSeriesData($this->series);
    }
}

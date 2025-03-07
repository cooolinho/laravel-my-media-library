<?php

namespace App\Jobs;

use App\Jobs\Exceptions\JobNotActivatedException;
use App\Models\Series;
use App\Services\ImportDataService;
use App\Settings\JobSettings;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SeriesArtworkJob extends AbstractBaseJob implements ShouldQueue
{
    use Queueable;

    private Series $series;

    public function __construct(Series $series)
    {
        $this->series = $series;
    }

    public function handle(JobSettings $settings, ImportDataService $service): void
    {
        if (!$settings->seriesArtworksJob_enabled) {
            $this->fail(new JobNotActivatedException());
            return;
        }

        $service->importSeriesArtworks($this->series);
    }
}

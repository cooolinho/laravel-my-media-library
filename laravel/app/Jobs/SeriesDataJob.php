<?php

namespace App\Jobs;

use App\Jobs\AbstractBaseJob as Job;
use App\Jobs\Concerns\LogsJobActivity;
use App\Jobs\Exceptions\JobNotActivatedException;
use App\Models\Series;
use App\Services\ImportDataService;
use App\Settings\JobSettings;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

class SeriesDataJob extends Job implements ShouldQueue
{
    use Queueable;
    use LogsJobActivity;

    private Series $series;

    public function __construct(Series $series)
    {
        $this->series = $series;
    }

    public function handle(JobSettings $settings, ImportDataService $service): void
    {
        $this->logStart($this->series, 'Aktualisiere Serie: ' . $this->series->name, [
            'series_id' => $this->series->id,
            'theTvDbId' => $this->series->theTvDbId,
        ]);

        try {
            if (!$settings->seriesDataJob_enabled) {
                $this->logSkipped('Job ist nicht aktiviert');
                $this->fail(new JobNotActivatedException());
                return;
            }

            $service->importSeriesData($this->series);

            $this->logSuccess('Serie erfolgreich aktualisiert');
        } catch (Throwable $e) {
            $this->logFailure($e);
            throw $e;
        }
    }
}

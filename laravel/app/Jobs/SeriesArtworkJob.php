<?php

namespace App\Jobs;

use App\Jobs\Concerns\LogsJobActivity;
use App\Jobs\Exceptions\JobNotActivatedException;
use App\Models\Series;
use App\Services\ImportDataService;
use App\Settings\JobSettings;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

class SeriesArtworkJob extends AbstractBaseJob implements ShouldQueue
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
        $this->logStart($this->series, 'Importiere Artworks für Serie: ' . $this->series->name, [
            'series_id' => $this->series->id,
            'theTvDbId' => $this->series->theTvDbId,
        ]);

        try {
            if (!$settings->seriesArtworksJob_enabled) {
                $this->logSkipped('Job ist nicht aktiviert');
                $this->fail(new JobNotActivatedException());
                return;
            }

            $service->importSeriesArtworks($this->series);

            $this->logSuccess('Artworks erfolgreich importiert');
        } catch (Throwable $e) {
            $this->logFailure($e);
            throw $e;
        }
    }
}

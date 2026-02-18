<?php

namespace App\Jobs;

use App\Jobs\Concerns\LogsJobActivity;
use App\Models\Series;
use App\Services\ImportDataService;
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

    public function handle(ImportDataService $service): void
    {
        $this->logStart($this->series, 'Importiere Artworks für Serie: ' . $this->series->name, [
            'series_id' => $this->series->id,
            'theTvDbId' => $this->series->theTvDbId,
        ]);

        try {
            $service->importSeriesArtworks($this->series);
            $this->logSuccess('Artworks erfolgreich importiert');
        } catch (Throwable $e) {
            $this->logFailure($e);
            throw $e;
        }
    }
}

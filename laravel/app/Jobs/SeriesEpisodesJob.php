<?php

namespace App\Jobs;

use App\Jobs\AbstractBaseJob as Job;
use App\Jobs\Concerns\LogsJobActivity;
use App\Models\Series;
use App\Services\ImportDataService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

class SeriesEpisodesJob extends Job implements ShouldQueue
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
        $this->logStart($this->series, 'Importiere Episoden für Serie: ' . $this->series->name, [
            'series_id' => $this->series->id,
            'theTvDbId' => $this->series->theTvDbId,
        ]);

        try {
            $count = $service->importSeriesEpisodes($this->series);
            $this->logSuccess(sprintf('Erfolgreich %d Episoden importiert', $count));
        } catch (Throwable $e) {
            $this->logFailure($e);
            throw $e;
        }
    }
}

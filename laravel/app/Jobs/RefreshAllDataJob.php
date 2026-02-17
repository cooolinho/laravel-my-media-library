<?php

namespace App\Jobs;

use App\Jobs\AbstractBaseJob as Job;
use App\Jobs\Concerns\LogsJobActivity;
use App\Models\Episode;
use App\Models\Series;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Throwable;

class RefreshAllDataJob extends Job implements ShouldQueue
{
    use Queueable;
    use LogsJobActivity;

    public function __construct()
    {
        //
    }

    public function handle(): void
    {
        $this->logStart(null, 'Starte Aktualisierung aller Serien und Episoden', []);

        try {
            $totalSeries = Series::query()->count();
            $totalEpisodes = Episode::query()->count();

            Log::info("RefreshAllDataJob: Starte Jobs für {$totalSeries} Serien und {$totalEpisodes} Episoden");

            // Starte die spezialisierten Jobs
            RefreshAllSeriesJob::dispatch();
            RefreshAllEpisodesJob::dispatch();

            $this->logSuccess("Erfolgreich Jobs für {$totalSeries} Serien und {$totalEpisodes} Episoden initiiert");
        } catch (Throwable $e) {
            $this->logFailure($e);
            throw $e;
        }
    }
}


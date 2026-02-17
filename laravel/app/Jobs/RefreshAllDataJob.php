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
            $seriesCount = Series::query()->count();
            $episodesCount = Episode::query()->count();

            Log::info("RefreshAllDataJob: Starte Jobs für {$seriesCount} Serien und {$episodesCount} Episoden");

            // Dispatch SeriesDataJob für alle Serien
            Series::query()
                ->chunk(100, function ($series) {
                    foreach ($series as $singleSeries) {
                        SeriesDataJob::dispatch($singleSeries);
                    }
                });

            // Dispatch EpisodeDataJob für alle Episoden
            Episode::query()
                ->chunk(100, function ($episodes) {
                    foreach ($episodes as $episode) {
                        EpisodeDataJob::dispatch($episode);
                    }
                });

            $this->logSuccess("Erfolgreich Jobs für {$seriesCount} Serien und {$episodesCount} Episoden gestartet");
        } catch (Throwable $e) {
            $this->logFailure($e);
            throw $e;
        }
    }
}


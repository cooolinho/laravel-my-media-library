<?php

namespace App\Jobs;

use App\Jobs\AbstractBaseJob as Job;
use App\Jobs\Concerns\LogsJobActivity;
use App\Models\Episode;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Throwable;

class CheckEpisodesDataUpdatesJob extends Job implements ShouldQueue
{
    use Queueable;
    use LogsJobActivity;

    private int $maxAgeHours;

    /**
     * @param int $maxAgeHours Anzahl der Stunden, nach denen die Daten als veraltet gelten (Standard: 48)
     */
    public function __construct(int $maxAgeHours = 48)
    {
        $this->maxAgeHours = $maxAgeHours;
    }

    public function handle(): void
    {
        $this->logStart(null, 'Prüfe Episoden auf veraltete Daten', [
            'max_age_hours' => $this->maxAgeHours,
        ]);

        try {
            $episodesToUpdate = $this->findEpisodesToUpdate();
            $count = $episodesToUpdate->count();

            Log::info("Gefunden: {$count} Episoden benötigen Daten-Update", [
                'max_age_hours' => $this->maxAgeHours,
                'episodes_count' => $count,
            ]);

            if ($count === 0) {
                $this->logSuccess('Keine Episoden benötigen ein Update');
                return;
            }

            // Dispatch EpisodeDataJob für jede Episode
            $dispatched = 0;
            foreach ($episodesToUpdate as $episode) {
                try {
                    EpisodeDataJob::dispatch($episode);
                    $dispatched++;

                    Log::debug("EpisodeDataJob dispatched für Episode: {$episode->getIdentifier()}", [
                        'episode_id' => $episode->id,
                        'series_id' => $episode->series_id,
                        'last_updated' => $episode->data_last_updated_at?->toDateTimeString() ?? 'Noch nie',
                    ]);
                } catch (Throwable $e) {
                    Log::error("Fehler beim Dispatchen von EpisodeDataJob für Episode: {$episode->getIdentifier()}", [
                        'episode_id' => $episode->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            $this->logSuccess("EpisodeDataJob für {$dispatched} von {$count} Episoden dispatched");
        } catch (Throwable $e) {
            $this->logFailure($e);
            throw $e;
        }
    }

    /**
     * Findet alle Episoden, die aktualisiert werden müssen
     */
    private function findEpisodesToUpdate(): \Illuminate\Database\Eloquent\Collection
    {
        return Episode::all()->filter(function (Episode $episode) {
            return $episode->needsDataUpdate($this->maxAgeHours);
        });
    }
}



<?php

namespace App\Jobs;

use App\Jobs\AbstractBaseJob as Job;
use App\Jobs\Concerns\LogsJobActivity;
use App\Models\Series;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Throwable;

class CheckSeriesDataUpdatesJob extends Job implements ShouldQueue
{
    use Queueable;
    use LogsJobActivity;

    private int $maxAgeHours;

    /**
     * @param int $maxAgeHours Anzahl der Stunden, nach denen die Daten als veraltet gelten (Standard: 24)
     */
    public function __construct(int $maxAgeHours = 24)
    {
        $this->maxAgeHours = $maxAgeHours;
    }

    public function handle(): void
    {
        $this->logStart(null, 'Prüfe Serien auf veraltete Daten', [
            'max_age_hours' => $this->maxAgeHours,
        ]);

        try {
            $seriesToUpdate = $this->findSeriesToUpdate();
            $count = $seriesToUpdate->count();

            Log::info("Gefunden: {$count} Serien benötigen Daten-Update", [
                'max_age_hours' => $this->maxAgeHours,
                'series_count' => $count,
            ]);

            if ($count === 0) {
                $this->logSuccess('Keine Serien benötigen ein Update');
                return;
            }

            // Dispatch SeriesDataJob für jede Serie
            $dispatched = 0;
            foreach ($seriesToUpdate as $series) {
                try {
                    SeriesDataJob::dispatch($series);
                    $dispatched++;

                    Log::debug("SeriesDataJob dispatched für Serie: {$series->name}", [
                        'series_id' => $series->id,
                        'last_updated' => $series->data_last_updated_at?->toDateTimeString() ?? 'Noch nie',
                    ]);
                } catch (Throwable $e) {
                    Log::error("Fehler beim Dispatchen von SeriesDataJob für Serie: {$series->name}", [
                        'series_id' => $series->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            $this->logSuccess("SeriesDataJob für {$dispatched} von {$count} Serien dispatched");
        } catch (Throwable $e) {
            $this->logFailure($e);
            throw $e;
        }
    }

    /**
     * Findet alle Serien, die aktualisiert werden müssen
     */
    private function findSeriesToUpdate(): \Illuminate\Database\Eloquent\Collection
    {
        return Series::all()->filter(function (Series $series) {
            return $series->needsDataUpdate($this->maxAgeHours);
        });
    }
}



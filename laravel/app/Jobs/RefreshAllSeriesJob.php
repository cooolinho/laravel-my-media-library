<?php

namespace App\Jobs;

use App\Jobs\AbstractBaseJob as Job;
use App\Jobs\Concerns\LogsJobActivity;
use App\Models\Series;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Throwable;

class RefreshAllSeriesJob extends Job implements ShouldQueue
{
    use Queueable;
    use LogsJobActivity;

    private int $offset;
    private int $chunkSize = 100;

    public function __construct(int $offset = 0)
    {
        $this->offset = $offset;
    }

    public function handle(): void
    {
        if ($this->offset === 0) {
            $this->logStart(null, 'Starte Aktualisierung aller Serien', []);
        }

        try {
            $totalSeries = Series::query()->count();

            if ($this->offset < $totalSeries) {
                $series = Series::query()
                    ->skip($this->offset)
                    ->take($this->chunkSize)
                    ->get();

                Log::info("RefreshAllSeriesJob: Verarbeite Serien {$this->offset} bis " . ($this->offset + $series->count()) . " von {$totalSeries}");

                foreach ($series as $singleSeries) {
                    SeriesDataJob::dispatch($singleSeries);
                }

                // Nächsten Batch dispatchen
                self::dispatch($this->offset + $this->chunkSize);
            } else {
                // Alle fertig
                $this->logSuccess("Erfolgreich Jobs für {$totalSeries} Serien gestartet");
            }
        } catch (Throwable $e) {
            $this->logFailure($e);
            throw $e;
        }
    }
}


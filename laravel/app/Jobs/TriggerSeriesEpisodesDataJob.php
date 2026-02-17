<?php

namespace App\Jobs;

use App\Jobs\AbstractBaseJob as Job;
use App\Jobs\Concerns\LogsJobActivity;
use App\Models\Series;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

class TriggerSeriesEpisodesDataJob extends Job implements ShouldQueue
{
    use Queueable;
    use LogsJobActivity;

    private const int BATCH_SIZE = 100;

    private Series $series;
    private int $offset;

    public function __construct(Series $series, int $offset = 0)
    {
        $this->series = $series;
        $this->offset = $offset;
    }

    public function handle(): void
    {
        $isFirstBatch = $this->offset === 0;

        if ($isFirstBatch) {
            $this->logStart($this->series, sprintf('Starte Episoden-Import für Serie "%s".', $this->series->name), [
                'series_id' => $this->series->id,
                'series_name' => $this->series->name,
            ]);
        }

        try {
            $episodes = $this->series->episodes()
                ->skip($this->offset)
                ->take(self::BATCH_SIZE)
                ->get();

            $dispatchedCount = 0;
            foreach ($episodes as $episode) {
                EpisodeDataJob::dispatch($episode);
                $dispatchedCount++;
            }

            // Wenn wir BATCH_SIZE Episoden verarbeitet haben, gibt es möglicherweise mehr
            if ($dispatchedCount === self::BATCH_SIZE) {
                $nextOffset = $this->offset + self::BATCH_SIZE;
                self::dispatch($this->series, $nextOffset);

                if ($isFirstBatch) {
                    $this->logSuccess(sprintf(
                        'Episoden-Import für Serie "%s" gestartet. Batch 1 (%d Episoden) dispatched, weitere Batches folgen.',
                        $this->series->name,
                        $dispatchedCount
                    ));
                }
            } else if ($dispatchedCount > 0 && !$isFirstBatch) {
                $this->logSuccess(sprintf(
                    'Episoden-Import für Serie "%s" erfolgreich gestartet. %d Episoden dispatched.',
                    $this->series->name,
                    $dispatchedCount
                ));
            }
        } catch (Throwable $e) {
            if ($isFirstBatch) {
                $this->logFailure($e);
            }
            throw $e;
        }
    }
}

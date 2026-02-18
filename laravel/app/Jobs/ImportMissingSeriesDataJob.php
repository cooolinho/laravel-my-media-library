<?php

namespace App\Jobs;

use App\Models\Series;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;

class ImportMissingSeriesDataJob extends AbstractImportMissingDataJob implements ShouldQueue
{
    /**
     * @param int $batchSize Anzahl der Serien pro Durchlauf (Standard: 50)
     * @param int $delaySeconds Verzögerung in Sekunden vor dem nächsten Batch (Standard: 10)
     */
    public function __construct(int $batchSize = 50, int $delaySeconds = 10)
    {
        parent::__construct($batchSize, $delaySeconds);
    }

    protected function getModelClass(): string
    {
        return Series::class;
    }

    protected function dispatchDataJob(Model $entry): void
    {
        SeriesDataJob::dispatch($entry);
        SeriesEpisodesJob::dispatch($entry);
        SeriesArtworkJob::dispatch($entry);
    }

    protected function getDataJobName(): string
    {
        return 'SeriesDataJob';
    }

    protected function getEntityName(): string
    {
        return 'Serien';
    }

    protected function getEntryIdentifier(Model $entry): string
    {
        /** @var Series $entry */
        return $entry->name;
    }

    protected function getTimestampColumn(): string
    {
        return Series::data_last_updated_at;
    }
}

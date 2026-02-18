<?php

namespace App\Jobs;

use App\Models\Episode;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;

class ImportMissingEpisodesDataJob extends AbstractImportMissingDataJob implements ShouldQueue
{
    /**
     * @param int $batchSize Anzahl der Episoden pro Durchlauf (Standard: 100)
     * @param int $delaySeconds Verzögerung in Sekunden vor dem nächsten Batch (Standard: 10)
     */
    public function __construct(int $batchSize = 100, int $delaySeconds = 10)
    {
        parent::__construct($batchSize, $delaySeconds);
    }

    protected function getModelClass(): string
    {
        return Episode::class;
    }

    protected function dispatchDataJob(Model $entry): void
    {
        EpisodeDataJob::dispatch($entry);
    }

    protected function getDataJobName(): string
    {
        return 'EpisodeDataJob';
    }

    protected function getEntityName(): string
    {
        return 'Episoden';
    }

    protected function getEntryIdentifier(Model $entry): string
    {
        /** @var Episode $entry */
        return $entry->getIdentifier();
    }

    protected function getTimestampColumn(): string
    {
        return Episode::data_last_updated_at;
    }
}

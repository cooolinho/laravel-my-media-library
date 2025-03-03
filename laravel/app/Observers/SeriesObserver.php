<?php

namespace App\Observers;

use App\Jobs\SeriesDataJob;
use App\Jobs\SeriesEpisodesJob;
use App\Models\Series;
use Illuminate\Support\Facades\Log;

class SeriesObserver
{
    /**
     * Handle the Series "created" event.
     */
    public function created(Series $series): void
    {
        Log::info('Series "'. $series->name .'" created');
        SeriesDataJob::dispatch($series);
        SeriesEpisodesJob::dispatch($series);
    }

    /**
     * Handle the Series "updated" event.
     */
    public function updated(Series $series): void
    {
        //
    }

    /**
     * Handle the Series "deleted" event.
     */
    public function deleted(Series $series): void
    {
        //
    }

    /**
     * Handle the Series "restored" event.
     */
    public function restored(Series $series): void
    {
        //
    }

    /**
     * Handle the Series "force deleted" event.
     */
    public function forceDeleted(Series $series): void
    {
        //
    }
}

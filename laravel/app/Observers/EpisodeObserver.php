<?php

namespace App\Observers;

use App\Jobs\EpisodeDataJob;
use App\Models\Episode;
use Illuminate\Support\Facades\Log;

class EpisodeObserver
{
    /**
     * Handle the Series "created" event.
     */
    public function created(Episode $episode): void
    {
        Log::info('Episode created: ' . $episode->id);
        EpisodeDataJob::dispatch($episode);
    }

    /**
     * Handle the Series "updated" event.
     */
    public function updated(Episode $episode): void
    {
        //
    }

    /**
     * Handle the Series "deleted" event.
     */
    public function deleted(Episode $episode): void
    {
        //
    }

    /**
     * Handle the Series "restored" event.
     */
    public function restored(Episode $episode): void
    {
        //
    }

    /**
     * Handle the Series "force deleted" event.
     */
    public function forceDeleted(Episode $episode): void
    {
        //
    }
}

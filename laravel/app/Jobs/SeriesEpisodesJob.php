<?php

namespace App\Jobs;

use App\Models\Series;
use App\Services\TheTVDBApiService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SeriesEpisodesJob implements ShouldQueue
{
    use Queueable;

    private Series $series;

    public function __construct(Series $series)
    {
        $this->series = $series;
    }

    public function handle(TheTVDBApiService $theTVDBApiService): void
    {
        $episodes = $theTVDBApiService->importSeriesEpisodes($this->series);

        foreach ($episodes as $episode) {
            EpisodeDataJob::dispatch($episode);
        }
    }
}

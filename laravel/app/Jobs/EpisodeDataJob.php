<?php

namespace App\Jobs;

use App\Models\Episode;
use App\Services\TheTVDBApiService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class EpisodeDataJob implements ShouldQueue
{
    use Queueable;
    private Episode $episode;

    public function __construct(Episode $episode)
    {
        $this->episode = $episode;
    }

    public function handle(TheTVDBApiService $theTVDBApiService): void
    {
        $theTVDBApiService->login();
        $theTVDBApiService->importEpisodesData($this->episode);
    }
}

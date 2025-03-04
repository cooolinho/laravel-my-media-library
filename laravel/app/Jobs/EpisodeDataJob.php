<?php

namespace App\Jobs;

use App\Models\Episode;
use App\Services\TheTVDBApiService;
use App\Settings\JobSettings;
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

    public function handle(JobSettings $settings, TheTVDBApiService $theTVDBApiService): void
    {
        if (!$settings->episodeDataJob_enabled) {
            $this->fail(new JobNotActivatedException());
            return;
        }

        $theTVDBApiService->importEpisodesData($this->episode);
    }
}

<?php

namespace App\Jobs;

use App\Models\Episode;
use App\Services\ImportDataService;
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

    public function handle(JobSettings $settings, ImportDataService $service): void
    {
        if (!$settings->episodeDataJob_enabled) {
            $this->fail(new JobNotActivatedException());
            return;
        }

        $service->importEpisodesData($this->episode);
    }
}

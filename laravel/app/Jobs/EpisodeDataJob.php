<?php

namespace App\Jobs;

use App\Jobs\AbstractBaseJob as Job;
use App\Jobs\Exceptions\JobNotActivatedException;
use App\Models\Episode;
use App\Models\Job as JobModel;
use App\Services\ImportDataService;
use App\Settings\JobSettings;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Queue\Queueable;

class EpisodeDataJob extends Job implements ShouldQueue
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

    public static function findByEpisodeIds(array $ids): Collection
    {
        return JobModel::findByJobAndRecordIds(self::class, $ids);
    }
}

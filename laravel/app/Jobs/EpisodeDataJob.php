<?php

namespace App\Jobs;

use App\Models\Episode;
use App\Models\Job;
use App\Services\ImportDataService;
use App\Settings\JobSettings;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
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

    public static function findByIds(array $ids): Collection
    {
        $query = Job::query()
            ->scopes([
                'jobClass' => self::class,
            ])
        ;

        foreach ($ids as $id) {
            $query->orWhere(Job::payload, 'LIKE', '%"id%";i:' . $id . '%');
        }

        return $query->get();
    }
}

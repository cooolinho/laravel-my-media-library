<?php

namespace App\Jobs;

use App\Jobs\AbstractBaseJob as Job;
use App\Jobs\Concerns\LogsJobActivity;
use App\Jobs\Exceptions\JobNotActivatedException;
use App\Models\Episode;
use App\Models\Job as JobModel;
use App\Services\ImportDataService;
use App\Settings\JobSettings;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

class EpisodeDataJob extends Job implements ShouldQueue
{
    use Queueable;
    use LogsJobActivity;

    private Episode $episode;

    public function __construct(Episode $episode)
    {
        $this->episode = $episode;
    }

    public function handle(JobSettings $settings, ImportDataService $service): void
    {
        $this->logStart($this->episode, 'Aktualisiere Episode: ' . $this->episode->getIdentifier(), [
            'episode_id' => $this->episode->id,
            'series_id' => $this->episode->series_id,
            'identifier' => $this->episode->getIdentifier(),
            'theTvDbId' => $this->episode->theTvDbId,
        ]);

        try {
            if (!$settings->episodeDataJob_enabled) {
                $this->logSkipped('Job ist nicht aktiviert');
                $this->fail(new JobNotActivatedException());
                return;
            }

            $service->importEpisodesData($this->episode);

            $this->logSuccess('Episode erfolgreich aktualisiert');
        } catch (Throwable $e) {
            $this->logFailure($e);
            throw $e;
        }
    }

    public static function findByEpisodeIds(array $ids): Collection
    {
        return JobModel::findByJobAndRecordIds(self::class, $ids);
    }
}

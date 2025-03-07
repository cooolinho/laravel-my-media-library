<?php

namespace App\Jobs;

use App\Http\Client\TheTVDB\Api\UpdatesApi;
use App\Jobs\Exceptions\JobNotActivatedException;
use App\Models\Series;
use App\Settings\JobSettings;
use App\Settings\TheTVDBSettings;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class UpdatesJob extends AbstractBaseJob implements ShouldQueue
{
    use Queueable;

    private array $mySeriesIds = [];
    private array $seriesIdsToUpdate = [];
    private ?UpdatesApi $api = null;
    private ?int $sinceDaysTimestamp = null;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(UpdatesApi $api, JobSettings $jobSettings, TheTVDBSettings $theTVDBSettings): void
    {
        if (!$jobSettings->updatesJob_enabled) {
            $this->fail(new JobNotActivatedException());
            return;
        }

        $this->api = $api;
        $this->sinceDaysTimestamp = Carbon::now()->subDays($theTVDBSettings->updatesSinceXDays)->timestamp;;
        $this->mySeriesIds = Series::findNotEnded()
            ->pluck(Series::theTvDbId)
            ->toArray();

        if (!empty($this->mySeriesIds)) {
            $this->checkUpdates();
            $this->triggerSeriesDataJobs();
        }

        if ($theTVDBSettings->autoUpdates) {
            self::dispatch()
                ->delay(Carbon::now()->addDays($theTVDBSettings->updatesSinceXDays));
        }
    }

    public function checkUpdates(int $page = 0): void
    {
        $result = $this->api->updates($this->sinceDaysTimestamp, $page);
        $recordIds = array_map(function (array $update) {
            return $update['recordId'];
        }, $result->getData());

        $this->seriesIdsToUpdate = array_merge($this->seriesIdsToUpdate, array_intersect($this->mySeriesIds, $recordIds));

        if ($result->hasLinkNext()) {
            $this->checkUpdates($page + 1);
        }
    }

    private function triggerSeriesDataJobs(): void
    {
        if (empty($this->seriesIdsToUpdate)) {
            return;
        }

        Series::query()
            ->whereIn(Series::theTvDbId, $this->seriesIdsToUpdate)
            ->get()
            ->each(function (Series $series) {
                SeriesDataJob::dispatch($series);
                SeriesEpisodesJob::dispatch($series);
            });
    }
}

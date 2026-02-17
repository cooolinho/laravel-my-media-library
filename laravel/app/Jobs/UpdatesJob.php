<?php

namespace App\Jobs;

use App\Http\Client\TheTVDB\Api\UpdatesApi;
use App\Jobs\Concerns\LogsJobActivity;
use App\Jobs\Exceptions\JobNotActivatedException;
use App\Models\Series;
use App\Settings\JobSettings;
use App\Settings\TheTVDBSettings;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

class UpdatesJob extends AbstractBaseJob implements ShouldQueue
{
    use Queueable, LogsJobActivity;

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
        $this->logStart(null, 'Suche nach Serie-Updates', [
            'updatesSinceXDays' => $theTVDBSettings->updatesSinceXDays,
        ]);

        try {
            if (!$jobSettings->updatesJob_enabled) {
                $this->logSkipped('Job ist nicht aktiviert');
                $this->fail(new JobNotActivatedException());
                return;
            }

            $this->api = $api;
            $this->sinceDaysTimestamp = Carbon::now()->subDays($theTVDBSettings->updatesSinceXDays)->timestamp;
            $this->mySeriesIds = Series::findNotEnded()
                ->pluck(Series::theTvDbId)
                ->toArray();

            $this->logUpdate(['total_series_count' => count($this->mySeriesIds)]);

            if (!empty($this->mySeriesIds)) {
                $this->checkUpdates();

                $this->logUpdate(['series_to_update_count' => count($this->seriesIdsToUpdate)]);

                $this->triggerSeriesDataJobs();
            }

            if ($theTVDBSettings->autoUpdates) {
                self::dispatch()
                    ->delay(Carbon::now()->addDays($theTVDBSettings->updatesSinceXDays));
            }

            $this->logSuccess(sprintf(
                '%d von %d Serien benötigen Updates',
                count($this->seriesIdsToUpdate),
                count($this->mySeriesIds)
            ));
        } catch (Throwable $e) {
            $this->logFailure($e);
            throw $e;
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

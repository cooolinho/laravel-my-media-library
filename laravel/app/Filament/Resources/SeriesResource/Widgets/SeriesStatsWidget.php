<?php

namespace App\Filament\Resources\SeriesResource\Widgets;

use App\Jobs\EpisodeDataJob;
use App\Jobs\SeriesDataJob;
use App\Jobs\SeriesEpisodesJob;
use App\Jobs\SyncEpisodesOwnedFromFileJob;
use App\Models\Job;
use App\Models\Series;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class SeriesStatsWidget extends BaseWidget
{
    public ?Series $record = null;

    protected function getCards(): array
    {
        return [
            $this->getEpisodesCard(),
            $this->getJobsStats(),
        ];
    }

    private function getEpisodesCard()
    {
        $countAll = $this->record->episodes->count();
        $countOwned = $this->record->getEpisodesOwnedCount();
        $ownedPercentage = $this->record->getEpisodeOwnedPercentage();
        $iconColor = $ownedPercentage >= 100 ? 'success' : 'danger';

        return BaseWidget\Stat::make('Episodes owned', $countOwned . ' / ' . $countAll)
            ->description($this->record->getEpisodeOwnedPercentage() . '%')
            ->icon('heroicon-o-cog')
            ->color($iconColor);
    }

    private function getJobsStats()
    {
        $seriesDataJobs = Job::findByJobAndRecordId(SeriesDataJob::class, $this->record->id)
            ->count();
        $seriesEpisodesDataJobs = Job::findByJobAndRecordId(SeriesEpisodesJob::class, $this->record->id)
            ->count();
        $syncEpisodesOwnedJobs = Job::findByJobAndRecordId(SyncEpisodesOwnedFromFileJob::class, $this->record->id)
            ->count();

        $episodeIds = $this->record->episodes->pluck('id')->toArray();
        $episodesDataJobs = EpisodeDataJob::findByEpisodeIds($episodeIds)
            ->count();

        $total = array_sum([
            $seriesDataJobs,
            $seriesEpisodesDataJobs,
            $episodesDataJobs,
            $syncEpisodesOwnedJobs
        ]);

        return BaseWidget\Stat::make('Jobs', $total)
            ->description($total <= 0 ? 'complete' : 'running')
            ->icon('heroicon-o-cog')
            ->chart([0, $total])
            ->color($total <= 0 ? 'success' : 'warning');
    }
}

<?php

namespace App\Filament\Resources\JobResource\Widgets;

use App\Jobs\EpisodeDataJob;
use App\Jobs\SeriesDataJob;
use App\Jobs\SeriesEpisodesJob;
use App\Jobs\SyncAllEpisodesOwnedFromFileJob;
use App\Jobs\SyncEpisodesOwnedFromFileJob;
use App\Models\Job;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class JobsStatsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            $this->getTotalStat(),
            $this->getSeriesDataJobsStat(),
            $this->getSeriesEpisodesJobsStat(),
            $this->getEpisodeDataJobsStat(),
            $this->getSyncAllEpisodesOwnedFromFileJobsStat(),
            $this->getSyncEpisodesOwnedFromFileJobs(),
        ];
    }

    /**
     * @return BaseWidget\Stat
     */
    private function getTotalStat(): BaseWidget\Stat
    {
        $count = Job::all()->count();
        return BaseWidget\Stat::make('Total', $count)
            ->icon('heroicon-o-cog')
            ->chart([0, $count])
            ->color($this->getColor($count));
    }

    /**
     * @return BaseWidget\Stat
     */
    private function getSeriesDataJobsStat(): BaseWidget\Stat
    {
        $count = SeriesDataJob::all()->count();
        return BaseWidget\Stat::make('SeriesDataJob', $count)
            ->icon('heroicon-o-cog')
            ->chart([0, $count])
            ->color($this->getColor($count));
    }

    /**
     * @return BaseWidget\Stat
     */
    private function getSeriesEpisodesJobsStat(): BaseWidget\Stat
    {
        $count = SeriesEpisodesJob::all()->count();
        return BaseWidget\Stat::make('SeriesEpisodesJob', $count)
            ->icon('heroicon-o-cog')
            ->chart([0, $count])
            ->color($this->getColor($count));
    }

    /**
     * @return BaseWidget\Stat
     */
    private function getEpisodeDataJobsStat(): BaseWidget\Stat
    {
        $count = EpisodeDataJob::all()->count();
        return BaseWidget\Stat::make('EpisodeDataJob', $count)
            ->icon('heroicon-o-cog')
            ->chart([0, $count])
            ->color($this->getColor($count));
    }

    /**
     * @return BaseWidget\Stat
     */
    private function getSyncAllEpisodesOwnedFromFileJobsStat(): BaseWidget\Stat
    {
        $count = SyncAllEpisodesOwnedFromFileJob::all()->count();
        return BaseWidget\Stat::make('SyncAllEpisodesOwnedFromFileJob', $count)
            ->icon('heroicon-o-cog')
            ->chart([0, $count])
            ->color($this->getColor($count));
    }

    /**
     * @return BaseWidget\Stat
     */
    private function getSyncEpisodesOwnedFromFileJobs(): BaseWidget\Stat
    {
        $count = SyncEpisodesOwnedFromFileJob::all()->count();
        return BaseWidget\Stat::make('SyncEpisodesOwnedFromFileJob', $count)
            ->icon('heroicon-o-cog')
            ->chart([0, $count])
            ->color($this->getColor($count));
    }

    private function getColor(int $count): string
    {
        return $count <= 0 ? 'success': 'warning';
    }
}

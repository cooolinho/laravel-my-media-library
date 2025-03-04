<?php

namespace App\Filament\Resources\SeriesResource\Widgets;

use App\Models\Series;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class SeriesStatsWidget extends BaseWidget
{
    public ?Series $record = null;

    protected function getCards(): array
    {
        $countAll = $this->record->episodes->count();
        $countOwned = $this->record->getEpisodesOwnedCount();
        $ownedPercentage = $this->record->getEpisodeOwnedPercentage();
        $iconColor = $ownedPercentage >= 100 ? 'success'  : 'danger';

        return [
            BaseWidget\Stat::make('Episodes', $countOwned . ' / ' . $countAll)
                ->description($this->record->getEpisodeOwnedPercentage() . '%')
                ->icon('heroicon-o-cog')
                ->color($iconColor),
        ];
    }
}

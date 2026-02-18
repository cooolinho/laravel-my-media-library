<?php

namespace App\Filament\Widgets;

use App\Models\Episode;
use App\Models\Job;
use App\Models\Series;
use App\Models\TheTVDBApiLog;
use App\Settings\DashboardSettings;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        return app(DashboardSettings::class)->show_stats_overview;
    }

    protected function getStats(): array
    {
        $totalSeries = Series::count();
        $totalEpisodes = Episode::count();
        $ownedEpisodes = Episode::where('owned', true)->count();
        $missingEpisodes = $totalEpisodes - $ownedEpisodes;
        $ownedPercentage = $totalEpisodes > 0 ? round(($ownedEpisodes / $totalEpisodes) * 100, 1) : 0;

        $pendingJobs = Job::count();

        $apiCallsToday = TheTVDBApiLog::whereDate('created_at', today())->count();
        $apiErrorsToday = TheTVDBApiLog::whereDate('created_at', today())
            ->where('success', false)
            ->count();

        return [
            Stat::make('Serien', $totalSeries)
                ->description('Gesamtanzahl der Serien')
                ->descriptionIcon('heroicon-o-film')
                ->color('success'),

            Stat::make('Episoden', $totalEpisodes)
                ->description($ownedEpisodes . ' besessen (' . $ownedPercentage . '%)')
                ->descriptionIcon('heroicon-o-video-camera')
                ->color('info'),

            Stat::make('Fehlende Episoden', $missingEpisodes)
                ->description('Noch nicht heruntergeladen')
                ->descriptionIcon('heroicon-o-exclamation-triangle')
                ->color($missingEpisodes > 0 ? 'warning' : 'success'),

            Stat::make('Wartende Jobs', $pendingJobs)
                ->description('In der Warteschlange')
                ->descriptionIcon('heroicon-o-queue-list')
                ->color($pendingJobs > 50 ? 'warning' : 'primary'),

            Stat::make('API Aufrufe (Heute)', $apiCallsToday)
                ->description($apiErrorsToday > 0 ? $apiErrorsToday . ' Fehler' : 'Keine Fehler')
                ->descriptionIcon($apiErrorsToday > 0 ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                ->color($apiErrorsToday > 0 ? 'danger' : 'success'),

            Stat::make('Besitz-Rate', $ownedPercentage . '%')
                ->description('Episoden in Besitz')
                ->descriptionIcon('heroicon-o-chart-bar')
                ->color($ownedPercentage >= 80 ? 'success' : ($ownedPercentage >= 50 ? 'warning' : 'danger')),
        ];
    }

}


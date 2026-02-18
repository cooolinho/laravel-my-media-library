<?php

namespace App\Filament\Widgets;

use App\Models\Episode;
use App\Models\Job;
use App\Models\Series;
use App\Models\TheTVDBApiLog;
use App\Settings\DashboardSettings;
use Filament\Widgets\Widget;

class QuickInsightsWidget extends Widget
{
    protected static ?int $sort = 0; // Ganz oben
    protected string $view = 'filament.widgets.quick-insights-widget';
    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return app(DashboardSettings::class)->show_quick_insights;
    }

    public function getInsights(): array
    {
        $totalSeries = Series::count();
        $seriesWithAllEpisodes = Series::get()->filter(fn($s) => $s->episodesComplete())->count();

        $totalEpisodes = Episode::count();
        $ownedEpisodes = Episode::where('owned', true)->count();

        $jobsInQueue = Job::count();
        $failedJobs = Job::where('attempts', '>', 0)->count();

        $apiCallsLast24h = TheTVDBApiLog::where('created_at', '>=', now()->subDay())->count();
        $apiCacheRate = $apiCallsLast24h > 0
            ? round((TheTVDBApiLog::where('created_at', '>=', now()->subDay())->where('from_cache', true)->count() / $apiCallsLast24h) * 100, 1)
            : 0;

        $topSeries = Series::withCount(['episodes as total_episodes'])
            ->orderBy('total_episodes', 'desc')
            ->first();

        $mostMissingSeries = Series::withCount(['episodes as total_episodes'])
            ->withCount(['episodes as owned_episodes' => function ($query) {
                $query->where('owned', true);
            }])
            ->get()
            ->map(function ($series) {
                return [
                    'name' => $series->name,
                    'missing' => $series->total_episodes - $series->owned_episodes,
                ];
            })
            ->where('missing', '>', 0)
            ->sortByDesc('missing')
            ->first();

        return [
            'completeSeries' => $seriesWithAllEpisodes,
            'totalSeries' => $totalSeries,
            'completionRate' => $totalSeries > 0 ? round(($seriesWithAllEpisodes / $totalSeries) * 100, 1) : 0,
            'ownedPercentage' => $totalEpisodes > 0 ? round(($ownedEpisodes / $totalEpisodes) * 100, 1) : 0,
            'jobsInQueue' => $jobsInQueue,
            'failedJobsRate' => $jobsInQueue > 0 ? round(($failedJobs / $jobsInQueue) * 100, 1) : 0,
            'apiCacheRate' => $apiCacheRate,
            'apiCallsLast24h' => $apiCallsLast24h,
            'topSeriesName' => $topSeries?->name,
            'topSeriesEpisodes' => $topSeries?->total_episodes,
            'mostMissingSeriesName' => $mostMissingSeries['name'] ?? null,
            'mostMissingCount' => $mostMissingSeries['missing'] ?? 0,
        ];
    }
}


<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Series\SeriesResource;
use App\Models\Series;
use App\Settings\DashboardSettings;
use Filament\Widgets\Widget;

class DashboardSeriesWidget extends Widget
{
    protected static ?int $sort = 7;
    protected int|string|array $columnSpan = 'full';
    protected string $view = 'filament.widgets.dashboard-series-widget';

    public static function canView(): bool
    {
        return app(DashboardSettings::class)->show_top_series;
    }

    public function getTopSeries(): array
    {
        return Series::query()
            ->withCount([
                'episodes as total_episodes',
                'episodes as owned_episodes' => function ($query) {
                    $query->where('owned', true);
                }
            ])
            ->orderBy('total_episodes', 'desc')
            ->limit(6)
            ->get()
            ->map(function ($series) {
                $percentage = $series->total_episodes > 0
                    ? round(($series->owned_episodes / $series->total_episodes) * 100, 1)
                    : 0;

                return [
                    'id' => $series->id,
                    'name' => $series->name,
                    'total' => $series->total_episodes,
                    'owned' => $series->owned_episodes,
                    'missing' => $series->total_episodes - $series->owned_episodes,
                    'percentage' => $percentage,
                    'url' => SeriesResource::getUrl('view', ['record' => $series->id]),
                ];
            })
            ->toArray();
    }
}

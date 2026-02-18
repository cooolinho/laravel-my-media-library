<?php

namespace App\Filament\Widgets;

use App\Models\Series;
use App\Settings\DashboardSettings;
use Filament\Widgets\ChartWidget;

class SeriesChartWidget extends ChartWidget
{
    protected static ?int $sort = 2;

    public static function canView(): bool
    {
        return app(DashboardSettings::class)->show_series_chart;
    }

    public function getHeading(): ?string
    {
        return 'Episoden pro Serie';
    }

    protected function getData(): array
    {
        $seriesData = Series::select('series.id', 'series.name')
            ->withCount(['episodes as total_episodes' => function ($query) {
                // Alle Episoden
            }])
            ->withCount(['episodes as owned_episodes' => function ($query) {
                $query->where('owned', true);
            }])
            ->orderBy('total_episodes', 'desc')
            ->limit(10)
            ->get();

        $labels = $seriesData->pluck('name')->toArray();
        $totalData = $seriesData->pluck('total_episodes')->toArray();
        $ownedData = $seriesData->pluck('owned_episodes')->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Gesamt Episoden',
                    'data' => $totalData,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.5)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'borderWidth' => 2,
                ],
                [
                    'label' => 'Besessene Episoden',
                    'data' => $ownedData,
                    'backgroundColor' => 'rgba(34, 197, 94, 0.5)',
                    'borderColor' => 'rgb(34, 197, 94)',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0,
                    ],
                ],
            ],
        ];
    }
}


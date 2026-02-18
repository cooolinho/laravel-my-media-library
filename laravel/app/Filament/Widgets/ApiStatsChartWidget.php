<?php

namespace App\Filament\Widgets;

use App\Models\TheTVDBApiLog;
use App\Settings\DashboardSettings;
use Filament\Widgets\ChartWidget;

class ApiStatsChartWidget extends ChartWidget
{
    protected static ?int $sort = 6;

    public static function canView(): bool
    {
        return app(DashboardSettings::class)->show_api_stats_chart;
    }

    public function getHeading(): ?string
    {
        return 'API Aufrufe (Letzte 7 Tage)';
    }

    protected function getData(): array
    {
        $days = [];
        $successData = [];
        $failureData = [];
        $cacheData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = today()->subDays($i);
            $days[] = $date->format('d.m');

            $successCount = TheTVDBApiLog::whereDate('created_at', $date)
                ->where('success', true)
                ->where('from_cache', false)
                ->count();

            $failureCount = TheTVDBApiLog::whereDate('created_at', $date)
                ->where('success', false)
                ->count();

            $cacheCount = TheTVDBApiLog::whereDate('created_at', $date)
                ->where('from_cache', true)
                ->count();

            $successData[] = $successCount;
            $failureData[] = $failureCount;
            $cacheData[] = $cacheCount;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Erfolgreiche Aufrufe',
                    'data' => $successData,
                    'backgroundColor' => 'rgba(34, 197, 94, 0.5)',
                    'borderColor' => 'rgb(34, 197, 94)',
                    'borderWidth' => 2,
                    'fill' => true,
                ],
                [
                    'label' => 'Cache Treffer',
                    'data' => $cacheData,
                    'backgroundColor' => 'rgba(245, 158, 11, 0.5)',
                    'borderColor' => 'rgb(245, 158, 11)',
                    'borderWidth' => 2,
                    'fill' => true,
                ],
                [
                    'label' => 'Fehler',
                    'data' => $failureData,
                    'backgroundColor' => 'rgba(239, 68, 68, 0.5)',
                    'borderColor' => 'rgb(239, 68, 68)',
                    'borderWidth' => 2,
                    'fill' => true,
                ],
            ],
            'labels' => $days,
        ];
    }

    protected function getType(): string
    {
        return 'line';
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


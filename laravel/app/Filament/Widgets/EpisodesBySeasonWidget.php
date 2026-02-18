<?php

namespace App\Filament\Widgets;

use App\Models\Episode;
use App\Settings\DashboardSettings;
use Filament\Widgets\ChartWidget;

class EpisodesBySeasonWidget extends ChartWidget
{
    protected static ?int $sort = 3;

    public static function canView(): bool
    {
        return app(DashboardSettings::class)->show_episodes_by_season;
    }

    public function getHeading(): ?string
    {
        return 'Episoden nach Staffel';
    }

    protected function getData(): array
    {
        $seasonData = Episode::select('seasonNumber')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN owned = 1 THEN 1 ELSE 0 END) as owned')
            ->groupBy('seasonNumber')
            ->orderBy('seasonNumber')
            ->get();

        $labels = $seasonData->map(fn($item) => 'Staffel ' . $item->seasonNumber)->toArray();
        $totalData = $seasonData->pluck('total')->toArray();
        $ownedData = $seasonData->pluck('owned')->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Gesamt',
                    'data' => $totalData,
                    'backgroundColor' => 'rgba(245, 158, 11, 0.5)',
                    'borderColor' => 'rgb(245, 158, 11)',
                ],
                [
                    'label' => 'Besessen',
                    'data' => $ownedData,
                    'backgroundColor' => 'rgba(34, 197, 94, 0.5)',
                    'borderColor' => 'rgb(34, 197, 94)',
                ],
            ],
            'labels' => $labels,
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


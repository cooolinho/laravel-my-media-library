<?php

namespace App\Filament\Resources\JobResource\Widgets;

use App\Models\Job;
use Filament\Widgets\ChartWidget;

class JobsChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Chart';

    protected function getData(): array
    {
        $data = Job::all()->groupBy(Job::payload . '.' . Job::PAYLOAD_DISPLAY_NAME)->map(function ($item) {
            return count($item);
        })->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Jobs',
                    'data' => array_values($data),
                ],
            ],
            'labels' => array_keys($data),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}

<?php

namespace App\Filament\Resources\Series\Actions;

use App\Jobs\SeriesDataJob;
use App\Models\Series;
use Filament\Actions\Action;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;

class LoadSeriesDataAction
{
    public static function make(?string $name = null): Action
    {
        return Action::make($name ?? 'loadSeriesData')
            ->label('Serien-Daten laden')
            ->requiresConfirmation()
            ->icon(Heroicon::QueueList)
            ->color(Color::Indigo)
            ->action(fn(Series $record) => SeriesDataJob::dispatch($record));
    }
}


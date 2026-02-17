<?php

namespace App\Filament\Resources\Series\Actions;

use App\Jobs\TriggerSeriesEpisodesDataJob;
use App\Models\Series;
use Filament\Actions\Action;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;

class TriggerSeriesEpisodesDataJobAction
{
    public static function make(?string $name = null): Action
    {
        return Action::make($name ?? 'TriggerSeriesEpisodesDataJot')
            ->label('Episoden-Daten aktualisieren')
            ->requiresConfirmation()
            ->icon(Heroicon::QueueList)
            ->color(Color::Indigo)
            ->action(fn(Series $record) => TriggerSeriesEpisodesDataJob::dispatch($record));
    }
}


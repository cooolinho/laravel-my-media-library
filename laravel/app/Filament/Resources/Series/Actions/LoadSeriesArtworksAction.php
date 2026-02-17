<?php

namespace App\Filament\Resources\Series\Actions;

use App\Jobs\SeriesArtworkJob;
use App\Models\Series;
use Filament\Actions\Action;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;

class LoadSeriesArtworksAction
{
    public static function make(?string $name = null): Action
    {
        return Action::make($name ?? 'loadSeriesArtworks')
            ->label('Artworks laden')
            ->requiresConfirmation()
            ->icon(Heroicon::QueueList)
            ->color(Color::Indigo)
            ->action(fn(Series $record) => SeriesArtworkJob::dispatch($record));
    }
}


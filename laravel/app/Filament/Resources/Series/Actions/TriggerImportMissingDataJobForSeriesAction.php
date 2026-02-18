<?php

namespace App\Filament\Resources\Series\Actions;

use App\Jobs\ImportMissingEpisodesDataJob;
use App\Jobs\ImportMissingSeriesDataJob;
use App\Models\Episode;
use App\Models\Series;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;

class TriggerImportMissingDataJobForSeriesAction
{
    public static function make(?string $name = null): Action
    {
        return Action::make($name ?? 'triggerImportMissingDataJobForSeries')
            ->label('Alle Daten aktualisieren')
            ->requiresConfirmation()
            ->icon(Heroicon::QueueList)
            ->color(Color::Indigo)
            ->action(function (Series $series) {
                // set data_last_updated_at to null to trigger the job
                $series->data_last_updated_at = null;
                $series->save();

                // set every episode's data_last_updated_at to null to trigger the job
                Episode::query()
                    ->where(Episode::series_id, $series->id)
                    ->update([Episode::data_last_updated_at => null]);

                // dispatch the jobs
                ImportMissingSeriesDataJob::dispatch();
                ImportMissingEpisodesDataJob::dispatch();

                Notification::make()
                    ->title('Datenaktualisierung gestartet')
                    ->body('Die Datenaktualisierung für die Serie "' . $series->name . '" wurde gestartet. Alle Daten werden nun aktualisiert.')
                    ->success()
                    ->send();
            });
    }
}


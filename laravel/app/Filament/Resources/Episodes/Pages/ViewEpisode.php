<?php

namespace App\Filament\Resources\Episodes\Pages;

use App\Filament\Resources\Episodes\EpisodeResource;
use App\Jobs\EpisodeDataJob;
use App\Models\Episode;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewEpisode extends ViewRecord
{
    protected static string $resource = EpisodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            Action::make('loadEpisodeData')
                ->requiresConfirmation()
                ->action(fn(Episode $episode) => EpisodeDataJob::dispatch($episode))
        ];
    }
}

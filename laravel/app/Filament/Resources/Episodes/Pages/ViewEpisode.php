<?php

namespace App\Filament\Resources\Episodes\Pages;

use App\Filament\Resources\Episodes\EpisodeResource;
use App\Jobs\EpisodeDataJob;
use App\Models\Episode;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;

class ViewEpisode extends ViewRecord
{
    public Model|Episode|int|string|null $record = null;
    protected static string $resource = EpisodeResource::class;
    protected string $view = 'episodes.view-episode-detail';

    public function getTitle(): string|Htmlable
    {
        // series name + SxxExx
        $name = $this->record->series->name;
        $identifier = $this->record->getIdentifier();

        return "{$name} - {$identifier}";
    }

    public function getHeading(): string|Htmlable|null
    {
        // series name + SxxExx - episode name
        $name = $this->record->series->name;
        $identifier = $this->record->getIdentifier();
        $episodeName = $this->record->data?->getName();

        return "{$name} - {$identifier} - {$episodeName}";
    }

    protected function getHeaderActions(): array
    {
        return [
            ActionGroup::make([
                EditAction::make(),
                Action::make('loadEpisodeData')
                    ->requiresConfirmation()
                    ->action(fn(Episode $episode) => EpisodeDataJob::dispatch($episode))
            ])
                ->button()
                ->label('Aktionen')
                ->icon(Heroicon::Cog)
        ];
    }
}

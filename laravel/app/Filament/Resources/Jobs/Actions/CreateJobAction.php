<?php

namespace App\Filament\Resources\Jobs\Actions;

use App\Jobs\EpisodeDataJob;
use App\Jobs\SeriesArtworkJob;
use App\Jobs\SeriesDataJob;
use App\Jobs\SeriesEpisodesJob;
use App\Jobs\SyncEpisodesOwnedFromFileJob;
use App\Jobs\UpdatesJob;
use App\Models\Episode;
use App\Models\Series;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Log;

class CreateJobAction
{
    public static function make(): Action
    {
        return Action::make('createJob')
            ->label('Neuen Job starten')
            ->icon(Heroicon::OutlinedPlusCircle)
            ->color(Color::Green)
            ->schema([
                Select::make('job_type')
                    ->label('Job-Typ')
                    ->options([
                        // grouped options
                        'TV Shows' => [
                            SeriesDataJob::class => 'Lade Series Data',
                            SeriesArtworkJob::class => 'Lade Series Artwork',
                            SeriesEpisodesJob::class => 'Lade Series Episodes',
                        ],
                        'Episoden' => [
                            EpisodeDataJob::class => 'Lade Episode Data',
                        ],
                        'Sonstige Jobs' => [
                            UpdatesJob::class => 'Automatische Updates',
                        ],
                    ])
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(fn($state, callable $set) => [
                        $set('series_id', null),
                        $set('episode_id', null),
                        $set('file_path', null),
                    ]),

                Select::make('series_id')
                    ->label('Serie')
                    ->options(Series::query()->orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->visible(fn(callable $get) => in_array($get('job_type'), [
                        SeriesDataJob::class,
                        SeriesArtworkJob::class,
                        SeriesEpisodesJob::class,
                        SyncEpisodesOwnedFromFileJob::class,
                    ]))
                    ->required(fn(callable $get) => in_array($get('job_type'), [
                        SeriesDataJob::class,
                        SeriesArtworkJob::class,
                        SeriesEpisodesJob::class,
                        SyncEpisodesOwnedFromFileJob::class,
                    ])),

                Select::make('episode_id')
                    ->label('Episode')
                    ->options(Episode::query()
                        ->with(['series', 'data'])
                        ->get()
                        ->mapWithKeys(function ($episode) {
                            $label = $episode->series->name . ' - ' . $episode->getIdentifier();
                            if ($episode->data && $episode->data->name) {
                                $label .= ' - ' . $episode->data->name;
                            }
                            return [$episode->id => $label];
                        }))
                    ->searchable()
                    ->preload()
                    ->visible(fn(callable $get) => $get('job_type') === EpisodeDataJob::class)
                    ->required(fn(callable $get) => $get('job_type') === EpisodeDataJob::class),

                TextEntry::make('info')
                    ->label('Information')
                    ->state('Dieser Job benötigt keine zusätzlichen Parameter.')
                    ->visible(fn(callable $get) => $get('job_type') === UpdatesJob::class),
            ])
            ->action(function (array $data) {
                $jobType = $data['job_type'];

                try {
                    match ($jobType) {
                        SeriesDataJob::class => self::handleSeriesJob($jobType, $data),
                        SeriesArtworkJob::class => self::handleSeriesJob($jobType, $data),
                        SeriesEpisodesJob::class => self::handleSeriesJob($jobType, $data),
                        EpisodeDataJob::class => self::handleEpisodeJob($data),
                        UpdatesJob::class => self::handleUpdatesJob(),
                        default => throw new \Exception("Unbekannter Job-Typ: {$jobType}"),
                    };
                } catch (\Exception $e) {
                    Log::error('Fehler beim Starten des Jobs', [
                        'job_type' => $jobType,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);

                    Notification::make()
                        ->title('Fehler beim Starten des Jobs')
                        ->danger()
                        ->body($e->getMessage())
                        ->send();
                }
            })
            ->modalHeading('Neuen Job erstellen')
            ->modalSubmitActionLabel('Job starten')
            ->modalWidth('lg');
    }

    private static function handleSeriesJob(string $jobType, array $data): void
    {
        $series = Series::findOrFail($data['series_id']);

        match ($jobType) {
            SeriesDataJob::class => SeriesDataJob::dispatch($series),
            SeriesArtworkJob::class => SeriesArtworkJob::dispatch($series),
            SeriesEpisodesJob::class => SeriesEpisodesJob::dispatch($series),
            default => throw new \Exception("Unbekannter Series Job: {$jobType}"),
        };

        Notification::make()
            ->title('Job wurde gestartet')
            ->success()
            ->body("Job für Serie '{$series->name}' wurde in die Queue eingereiht.")
            ->send();
    }

    private static function handleEpisodeJob(array $data): void
    {
        $episode = Episode::with('series')->findOrFail($data['episode_id']);
        EpisodeDataJob::dispatch($episode);

        $episodeLabel = $episode->series->name . ' - ' . $episode->getIdentifier();

        Notification::make()
            ->title('Job wurde gestartet')
            ->success()
            ->body("Job für Episode '{$episodeLabel}' wurde in die Queue eingereiht.")
            ->send();
    }

    private static function handleUpdatesJob(): void
    {
        UpdatesJob::dispatch();

        Notification::make()
            ->title('Job wurde gestartet')
            ->success()
            ->body("Updates Job wurde in die Queue eingereiht.")
            ->send();
    }
}


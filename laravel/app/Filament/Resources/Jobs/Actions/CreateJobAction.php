<?php

namespace App\Filament\Resources\Jobs\Actions;

use App\Jobs\ImportMissingEpisodesDataJob;
use App\Jobs\ImportMissingSeriesDataJob;
use App\Jobs\UpdatesJob;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
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
                        'Import Jobs' => [
                            ImportMissingSeriesDataJob::class => 'Serien ohne Daten importieren',
                            ImportMissingEpisodesDataJob::class => 'Episoden ohne Daten importieren',
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
                        $set('batch_size', null),
                        $set('delay_seconds', null),
                    ]),

                TextInput::make('batch_size')
                    ->label('Batch-Größe')
                    ->helperText(fn(callable $get) => match ($get('job_type')) {
                        ImportMissingSeriesDataJob::class => 'Anzahl Serien pro Durchlauf (Standard: 50)',
                        ImportMissingEpisodesDataJob::class => 'Anzahl Episoden pro Durchlauf (Standard: 100)',
                        default => '',
                    })
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(500)
                    ->default(fn(callable $get) => match ($get('job_type')) {
                        ImportMissingSeriesDataJob::class => 50,
                        ImportMissingEpisodesDataJob::class => 100,
                        default => null,
                    })
                    ->visible(fn(callable $get) => in_array($get('job_type'), [
                        ImportMissingSeriesDataJob::class,
                        ImportMissingEpisodesDataJob::class,
                    ])),

                TextInput::make('delay_seconds')
                    ->label('Verzögerung (Sekunden)')
                    ->helperText('Verzögerung zwischen den Batches (Standard: 10 Sekunden)')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(300)
                    ->default(10)
                    ->visible(fn(callable $get) => in_array($get('job_type'), [
                        ImportMissingSeriesDataJob::class,
                        ImportMissingEpisodesDataJob::class,
                    ])),

                TextEntry::make('info')
                    ->label('Information')
                    ->state(fn(callable $get) => match ($get('job_type')) {
                        ImportMissingSeriesDataJob::class => 'Importiert Daten für alle Serien ohne data_last_updated_at. Der Job dispatched sich selbst automatisch bis alle verarbeitet sind.',
                        ImportMissingEpisodesDataJob::class => 'Importiert Daten für alle Episoden ohne data_last_updated_at. Der Job dispatched sich selbst automatisch bis alle verarbeitet sind.',
                        UpdatesJob::class => 'Dieser Job benötigt keine zusätzlichen Parameter.',
                        default => '',
                    })
                    ->visible(fn(callable $get) => in_array($get('job_type'), [
                        ImportMissingSeriesDataJob::class,
                        ImportMissingEpisodesDataJob::class,
                        UpdatesJob::class,
                    ])),
            ])
            ->action(function (array $data) {
                $jobType = $data['job_type'];

                try {
                    match ($jobType) {
                        ImportMissingSeriesDataJob::class => self::handleImportMissingSeriesDataJob($data),
                        ImportMissingEpisodesDataJob::class => self::handleImportMissingEpisodesDataJob($data),
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

    private static function handleImportMissingSeriesDataJob(array $data): void
    {
        $batchSize = $data['batch_size'] ?? 50;
        $delaySeconds = $data['delay_seconds'] ?? 10;

        ImportMissingSeriesDataJob::dispatch($batchSize, $delaySeconds);

        Notification::make()
            ->title('Job wurde gestartet')
            ->success()
            ->body("Import Missing Series Data Job wurde gestartet (Batch: {$batchSize}, Delay: {$delaySeconds}s). Der Job dispatched sich automatisch bis alle Serien importiert sind.")
            ->send();
    }

    private static function handleImportMissingEpisodesDataJob(array $data): void
    {
        $batchSize = $data['batch_size'] ?? 100;
        $delaySeconds = $data['delay_seconds'] ?? 10;

        ImportMissingEpisodesDataJob::dispatch($batchSize, $delaySeconds);

        Notification::make()
            ->title('Job wurde gestartet')
            ->success()
            ->body("Import Missing Episodes Data Job wurde gestartet (Batch: {$batchSize}, Delay: {$delaySeconds}s). Der Job dispatched sich automatisch bis alle Episoden importiert sind.")
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


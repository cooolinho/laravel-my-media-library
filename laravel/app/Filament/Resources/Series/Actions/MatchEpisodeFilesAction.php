<?php

namespace App\Filament\Resources\Series\Actions;

use App\Filament\Pages\EpisodeFileMatchResults;
use App\Models\Series;
use App\Services\EpisodeFileMatcher;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;

class MatchEpisodeFilesAction
{
    public static function make(): Action
    {
        return Action::make('matchEpisodeFiles')
            ->label('Dateiliste abgleichen')
            ->icon(Heroicon::DocumentText)
            ->color('info')
            ->schema([
                Textarea::make('file_list')
                    ->label('Dateiliste')
                    ->placeholder("Gib hier deine Dateinamen ein, eine pro Zeile:\n\nS01E001 - Mord in Serie.mkv\nS01E002 - Tödliches Gift.mkv\nS01E004 - Mord auf Raten.mkv\n...")
                    ->rows(10)
                    ->required()
                    ->helperText('Füge deine Dateinamen ein, einen pro Zeile. Das System extrahiert automatisch nur den Titel (nach S01E001 -) und vergleicht ihn mit den Episodentiteln aus TheTVDB.'),
            ])
            ->action(function (Series $record, array $data) {
                $fileNames = array_filter(
                    array_map('trim', explode("\n", $data['file_list'])),
                    fn($line) => !empty($line)
                );

                if (empty($fileNames)) {
                    Notification::make()
                        ->warning()
                        ->title('Keine Dateien angegeben')
                        ->body('Bitte gib mindestens einen Dateinamen ein.')
                        ->send();
                    return;
                }

                $matcher = new EpisodeFileMatcher();
                $matches = $matcher->matchFiles($record, $fileNames);
                $formattedMatches = $matcher->formatMatches($matches);

                // Speichere die Ergebnisse in der Session
                session([
                    'episode_file_matches' => [
                        'series_id' => $record->id,
                        'series_name' => $record->name,
                        'matches' => $formattedMatches,
                        'timestamp' => now()->toDateTimeString(),
                    ]
                ]);

                Notification::make()
                    ->success()
                    ->title('Abgleich erfolgreich')
                    ->body(count($fileNames) . ' Dateien wurden mit den Episoden abgeglichen.')
                    ->send();

                // Redirect zu einer Ergebnisseite
                redirect(EpisodeFileMatchResults::getUrl());
            })
            ->modalWidth('2xl')
            ->modalSubmitActionLabel('Abgleichen');
    }
}


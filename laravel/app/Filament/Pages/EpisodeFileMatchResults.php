<?php

namespace App\Filament\Pages;

use App\Filament\Resources\Series\SeriesResource;
use App\Models\Episode;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class EpisodeFileMatchResults extends Page
{

    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $title = 'Datei-Abgleich Ergebnisse';
    public array $matches = [];
    public ?string $seriesName = null;
    public ?int $seriesId = null;
    public ?string $timestamp = null;
    protected string $view = 'filament.pages.episode-file-match-results';

    public function mount(): void
    {
        $sessionData = session('episode_file_matches');

        if (!$sessionData) {
            $this->redirect(SeriesResource::getUrl('index'));
            return;
        }

        $this->matches = $sessionData['matches'];
        $this->seriesName = $sessionData['series_name'];
        $this->seriesId = $sessionData['series_id'];
        $this->timestamp = $sessionData['timestamp'];
    }

    public function getHeading(): string
    {
        return 'Datei-Abgleich: ' . ($this->seriesName ?? 'Unbekannte Serie');
    }

    /**
     * Dynamische Action für das Hinzufügen von Notizen
     */
    public function addNoteToEpisodeAction(): Action
    {
        return Action::make('addNoteToEpisode')
            ->label('Als Notiz speichern')
            ->icon(Heroicon::DocumentPlus)
            ->color('success')
            ->size('sm')
            ->schema([
                TextInput::make('file_name')
                    ->label('Dateiname')
                    ->required()
                    ->helperText('Der Dateiname, der zur Notiz hinzugefügt wird.'),

                Checkbox::make('mark_as_owned')
                    ->label('Episode als "Besitz" markieren')
                    ->default(true)
                    ->helperText('Markiert die Episode automatisch als im Besitz.'),

                Checkbox::make('append_to_existing')
                    ->label('An bestehende Notizen anhängen')
                    ->default(true)
                    ->helperText('Wenn deaktiviert, werden bestehende Notizen überschrieben.'),

                Textarea::make('additional_notes')
                    ->label('Zusätzliche Notizen (optional)')
                    ->rows(3)
                    ->placeholder('Z.B. Qualität, Sprache, Quelle...'),
            ])
            ->fillForm(function (array $arguments): array {
                return [
                    'file_name' => $arguments['fileName'] ?? '',
                ];
            })
            ->action(function (array $data, array $arguments) {
                $episodeId = $arguments['episodeId'];
                $episode = Episode::query()
                    ->findOrFail($episodeId);

                // Erstelle die Notiz
                $newNote = "Datei: {$data['file_name']}";

                if (!empty($data['additional_notes'])) {
                    $newNote .= "\n" . $data['additional_notes'];
                }

                $newNote .= "\nHinzugefügt am: " . now()->format('d.m.Y H:i');

                // Füge zur bestehenden Notiz hinzu oder überschreibe
                if ($data['append_to_existing'] && !empty($episode->notes)) {
                    $episode->notes = $episode->notes . "\n\n---\n\n" . $newNote;
                } else {
                    $episode->notes = $newNote;
                }

                // Markiere als Besitz, wenn gewünscht
                if ($data['mark_as_owned']) {
                    $episode->owned = true;
                }

                $episode->save();

                Notification::make()
                    ->success()
                    ->title('Notiz gespeichert')
                    ->body("Die Datei-Information wurde zur Episode {$episode->getIdentifier()} hinzugefügt.")
                    ->send();
            })
            ->modalHeading(fn(array $arguments): string => 'Notiz zur Episode ' . ($arguments['episodeTitle'] ?? '') . ' hinzufügen')
            ->modalDescription('Füge die Datei-Information als Notiz zur Episode hinzu.')
            ->modalSubmitActionLabel('Speichern')
            ->modalWidth('2xl');
    }

    protected function getViewData(): array
    {
        return [
            'matches' => $this->matches,
            'seriesName' => $this->seriesName,
            'seriesId' => $this->seriesId,
            'timestamp' => $this->timestamp,
        ];
    }
}


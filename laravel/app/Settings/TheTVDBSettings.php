<?php

namespace App\Settings;

use App\Jobs\UpdatesJob;
use App\Models\Language;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Spatie\LaravelSettings\Settings;

class TheTVDBSettings extends Settings implements FormSchemaInterface
{
    const string LANGUAGE_FALLBACK = Language::ENG;

    public array $languages = [self::LANGUAGE_FALLBACK];
    public string $languageDefault = self::LANGUAGE_FALLBACK;
    public int $updatesSinceXDays = 1;
    public bool $autoUpdates = true;

    const string autoUpdates = 'autoUpdates';

    /**
     * @return string
     */
    public static function group(): string
    {
        return 'theTVDB';
    }

    /**
     * @return array
     */
    public static function getFormSchema(): array
    {
        $options = self::getLanguageOptions();
        $days = range(1, 7);
        return [
            Select::make('languages')
                ->options($options)
                ->multiple()
                ->label('Sprachen in DB laden')
                ->helperText('Beim Laden von Serien oder Episoden Daten werden diese Translations in der Datenbank gespeichert.'),
            Select::make('languageDefault')
                ->options($options)
                ->label('Angezeigte Sprache')
                ->helperText('Beim Anzeigen von Serien oder Episoden Daten wird diese Sprache bevorzugt angezeigt. Wenn die Daten in dieser Sprache nicht verfügbar sind, wird auf die anderen ausgewählten Sprachen zurückgegriffen.'),
            Select::make('updatesSinceXDays')
                ->options(array_combine($days, $days))
                ->helperText('Gibt an, wie viele Tage zurück die Suche nach Updates für Serien- und Episodendaten gehen soll. Der availableAt Zeitpunkt beim Job UpdatesJob wird entsprechend angepasst, um nur Updates zu berücksichtigen, die seit diesem Zeitpunkt verfügbar sind.')
                ->label('Updates für die letzten X Tage suchen'),
            Toggle::make(self::autoUpdates)
                ->helperText('Wenn aktiviert wird der UpdatesJob automatisch nach beendeter Ausführung erneut gestartet, um kontinuierlich nach Updates zu suchen.')
                ->label('Automatische Updates'),
        ];
    }

    /**
     * @return array
     */
    private static function getLanguageOptions(): array
    {
        $options = [];
        $languages = Language::query()->orderBy(Language::name)->get();

        /** @var Language $language */
        foreach ($languages as $language) {
            $options[$language->id] = $language->getLabel();
        }

        return $options;
    }

    /**
     * @param array $data
     * @return void
     */
    public static function handleSavedData(array $data): void
    {
        if (array_key_exists(self::autoUpdates, $data)) {
            self::handleChangedAutoUpdates((bool)$data[self::autoUpdates]);
        }
    }

    /**
     * @param bool $autoUpdates
     * @return void
     */
    private static function handleChangedAutoUpdates(bool $autoUpdates): void
    {
        if ($autoUpdates && UpdatesJob::all()->count() <= 0) {
            UpdatesJob::dispatch();
        }
    }
}

<?php

namespace App\Settings;

use App\Jobs\UpdatesJob;
use App\Models\Language;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Spatie\LaravelSettings\Settings;

class TheTVDBSettings extends Settings implements FormSchemaInterface
{
    const LANGUAGE_FALLBACK = Language::ENG;

    public array $languages = [self::LANGUAGE_FALLBACK];
    public string $languageDefault = self::LANGUAGE_FALLBACK;
    public int $updatesSinceXDays = 1;
    public bool $autoUpdates = true;

    const autoUpdates = 'autoUpdates';

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
                ->label('Languages'),
            Select::make('languageDefault')
                ->options($options)
                ->label('languageDefault'),
            Select::make('updatesSinceXDays')
                ->options(array_combine($days, $days))
                ->label('updatesSinceXDays'),
            Toggle::make(self::autoUpdates)
                ->label('autoUpdates'),
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
            UpdatesJob::dispatch()
                ->onQueue('test');
        }
    }
}

<?php

namespace App\Settings;

use App\Models\Language;
use Filament\Forms\Components\Select;
use Spatie\LaravelSettings\Settings;

class TheTVDBSettings extends Settings implements FormSchemaInterface
{
    const LANGUAGE_FALLBACK = Language::ENG;

    public array $languages = [self::LANGUAGE_FALLBACK];
    public string $languageDefault = self::LANGUAGE_FALLBACK;

    public static function group(): string
    {
        return 'theTVDB';
    }

    public static function getFormSchema(): array
    {
        $options = self::getLanguageOptions();
        return [
            Select::make('languages')
                ->options($options)
                ->multiple()
                ->label('Languages'),
            Select::make('languageDefault')
                ->options($options)
                ->label('languageDefault'),
        ];
    }

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
}

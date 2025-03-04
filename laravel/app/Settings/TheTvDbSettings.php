<?php

namespace App\Settings;

use Filament\Forms\Components\Select;
use Spatie\LaravelSettings\Settings;

class TheTvDbSettings extends Settings implements FormSchemaInterface
{
    const LANGUAGE_ENG = 'eng';
    const LANGUAGE_FALLBACK = self::LANGUAGE_ENG;

    public array $languages = [self::LANGUAGE_ENG];
    public string $languageDefault = self::LANGUAGE_ENG;

    private static array $languageOptions = [
        'ces',
        'dan',
        'deu',
        self::LANGUAGE_ENG,
        'fin',
        'fra',
        'heb',
        'hrv',
        'hun',
        'ita',
        'kor',
        'nld',
        'pol',
        'por',
        'pt',
        'rus',
        'spa',
        'sqi',
        'swe',
        'tur',
        'zho',
    ];

    public static function group(): string
    {
        return 'theTVDB';
    }

    public static function getFormSchema(): array
    {
        return [
            Select::make('languages')
                ->options(array_combine(self::$languageOptions, self::$languageOptions))
                ->multiple()
                ->label('Languages'),
            Select::make('languageDefault')
                ->options(array_combine(self::$languageOptions, self::$languageOptions))
                ->label('languageDefault'),
        ];
    }
}

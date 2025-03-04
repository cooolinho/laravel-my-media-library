<?php

namespace App\Settings;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Spatie\LaravelSettings\Settings;

class TheTvDbSettings extends Settings implements FormSchemaInterface
{
    public array $languages = ['eng'];
    public int $loginRetries = 3;
    public int $tokenExpiration = 43200;

    private static array $languageOptions = [
        'ces',
        'dan',
        'deu',
        'eng',
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
            TextInput::make('loginRetries')
                ->label('loginRetries'),
            TextInput::make('tokenExpiration')
                ->label('tokenExpiration'),
        ];
    }
}

<?php

namespace App\Settings;

use Filament\Forms\Components\Toggle;
use Spatie\LaravelSettings\Settings;

class JobSettings extends Settings
{
    public bool $seriesDataJob_enabled = true;
    public bool $episodeDataJob_enabled = true;
    public bool $seriesEpisodesJob_enabled = true;
    public bool $syncAllEpisodesOwnedFromFileJob_enabled = true;
    public bool $syncEpisodesOwnedFromFileJob_enabled = true;

    public static function group(): string
    {
        return 'jobs';
    }

    public static function getFormSchema(string $inputName): array
    {
        $prefix = sprintf('%s.%s.', $inputName, self::group());

        return [
            Toggle::make($prefix . 'seriesDataJob_enabled')
                ->label('seriesDataJob_enabled'),
            Toggle::make($prefix . 'episodeDataJob_enabled')
                ->label('episodeDataJob_enabled'),
            Toggle::make($prefix . 'seriesEpisodesJob_enabled')
                ->label('seriesEpisodesJob_enabled'),
            Toggle::make($prefix . 'syncAllEpisodesOwnedFromFileJob_enabled')
                ->label('syncAllEpisodesOwnedFromFileJob_enabled'),
            Toggle::make($prefix . 'syncEpisodesOwnedFromFileJob_enabled')
                ->label('syncEpisodesOwnedFromFileJob_enabled'),
        ];
    }
}

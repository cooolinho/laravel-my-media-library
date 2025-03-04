<?php

namespace App\Settings;

use Filament\Forms\Components\Toggle;
use Spatie\LaravelSettings\Settings;

class JobSettings extends Settings implements FormSchemaInterface
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

    public static function getFormSchema(): array
    {
        return [
            Toggle::make('seriesDataJob_enabled')
                ->label('seriesDataJob_enabled'),
            Toggle::make('episodeDataJob_enabled')
                ->label('episodeDataJob_enabled'),
            Toggle::make('seriesEpisodesJob_enabled')
                ->label('seriesEpisodesJob_enabled'),
            Toggle::make('syncAllEpisodesOwnedFromFileJob_enabled')
                ->label('syncAllEpisodesOwnedFromFileJob_enabled'),
            Toggle::make('syncEpisodesOwnedFromFileJob_enabled')
                ->label('syncEpisodesOwnedFromFileJob_enabled'),
        ];
    }
}

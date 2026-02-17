<?php

namespace App\Settings;

use Filament\Forms\Components\Toggle;
use Spatie\LaravelSettings\Settings;

class JobSettings extends Settings implements FormSchemaInterface
{
    public bool $updatesJob_enabled = true;

    public static function group(): string
    {
        return 'jobs';
    }

    public static function getFormSchema(): array
    {
        return [
            Toggle::make('updatesJob_enabled')
                ->label('Job: App\\Jobs\\UpdatesJob')
                ->helperText('Aktiviere diese Option, um automatische Updates für Serien- und Episodendaten zu aktivieren.'),
        ];
    }
}

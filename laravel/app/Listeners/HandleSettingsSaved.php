<?php

namespace App\Listeners;


use App\Settings\TheTVDBSettings;
use Spatie\LaravelSettings\Events\SettingsSaved;

class HandleSettingsSaved
{
    /**
     * Handle the event.
     */
    public function handle(SettingsSaved $event): void
    {
        match ($event->settings::group()) {
            TheTVDBSettings::group() => TheTVDBSettings::handleSavedData($event->settings->toArray()),
            default => null,
        };
    }

}

<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('jobs.seriesDataJob_enabled', true);
        $this->migrator->add('jobs.seriesEpisodesJob_enabled', true);
        $this->migrator->add('jobs.seriesArtworksJob_enabled', true);
        $this->migrator->add('jobs.episodeDataJob_enabled', true);
        $this->migrator->add('jobs.syncAllEpisodesOwnedFromFileJob_enabled', true);
        $this->migrator->add('jobs.syncEpisodesOwnedFromFileJob_enabled', true);
        $this->migrator->add('jobs.updateSeriesJob_enabled', true);
    }
};

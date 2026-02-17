<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->delete('jobs.seriesDataJob_enabled');
        $this->migrator->delete('jobs.seriesArtworksJob_enabled');
        $this->migrator->delete('jobs.episodeDataJob_enabled');
        $this->migrator->delete('jobs.seriesEpisodesJob_enabled');
        $this->migrator->delete('jobs.syncAllEpisodesOwnedFromFileJob_enabled');
        $this->migrator->delete('jobs.syncEpisodesOwnedFromFileJob_enabled');
    }
};

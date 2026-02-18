<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->add('dashboard.show_quick_insights', true);
        $this->migrator->add('dashboard.show_stats_overview', true);
        $this->migrator->add('dashboard.show_series_chart', true);
        $this->migrator->add('dashboard.show_episodes_by_season', true);
        $this->migrator->add('dashboard.show_recent_jobs', true);
        $this->migrator->add('dashboard.show_api_logs', true);
        $this->migrator->add('dashboard.show_api_stats_chart', true);
        $this->migrator->add('dashboard.show_top_series', true);
    }
};

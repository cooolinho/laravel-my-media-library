<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('theTVDB.languages', ['eng']);
        $this->migrator->add('theTVDB.loginRetries', 3);
        $this->migrator->add('theTVDB.tokenExpiration', 43200);
    }
};

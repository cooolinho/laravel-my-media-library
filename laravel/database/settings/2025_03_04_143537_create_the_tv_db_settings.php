<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('theTVDB.languages', ['eng']);
        $this->migrator->add('theTVDB.languageDefault', 'eng');
        $this->migrator->add('theTVDB.updatesSinceXDays', 1);
        $this->migrator->add('theTVDB.autoUpdates', true);
    }
};

<?php

namespace App\Console\Commands;

use App\Services\TheTVDBApiLogger;
use Illuminate\Console\Command;

class CleanupTheTVDBApiLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'thetvdb:cleanup-logs {--days=30 : Number of days to keep logs}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup old TheTVDB API logs';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = (int)$this->option('days');

        $this->info("Cleaning up TheTVDB API logs older than {$days} days...");

        $deletedCount = TheTVDBApiLogger::cleanup($days);

        $this->info("Successfully deleted {$deletedCount} log entries.");

        return self::SUCCESS;
    }
}


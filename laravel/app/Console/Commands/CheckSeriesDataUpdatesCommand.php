<?php

namespace App\Console\Commands;

use App\Jobs\CheckSeriesDataUpdatesJob;
use Illuminate\Console\Command;

class CheckSeriesDataUpdatesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'series:check-data-updates 
                            {--hours=24 : Anzahl der Stunden, nach denen die Daten als veraltet gelten}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prüft alle Serien auf veraltete Daten und dispatched SeriesDataJob bei Bedarf';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $hours = (int)$this->option('hours');

        $this->info("Prüfe Serien auf Daten älter als {$hours} Stunden...");

        try {
            CheckSeriesDataUpdatesJob::dispatch($hours);
            $this->info('CheckSeriesDataUpdatesJob wurde erfolgreich dispatched.');
            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Fehler beim Dispatchen des Jobs: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}



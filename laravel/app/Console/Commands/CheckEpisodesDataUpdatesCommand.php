<?php

namespace App\Console\Commands;

use App\Jobs\CheckEpisodesDataUpdatesJob;
use Illuminate\Console\Command;

class CheckEpisodesDataUpdatesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'episodes:check-data-updates 
                            {--hours=48 : Anzahl der Stunden, nach denen die Daten als veraltet gelten}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prüft alle Episoden auf veraltete Daten und dispatched EpisodeDataJob bei Bedarf';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $hours = (int)$this->option('hours');

        $this->info("Prüfe Episoden auf Daten älter als {$hours} Stunden...");

        try {
            CheckEpisodesDataUpdatesJob::dispatch($hours);
            $this->info('CheckEpisodesDataUpdatesJob wurde erfolgreich dispatched.');
            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Fehler beim Dispatchen des Jobs: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}



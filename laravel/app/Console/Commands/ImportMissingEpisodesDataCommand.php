<?php

namespace App\Console\Commands;

use App\Jobs\ImportMissingEpisodesDataMissingDataJob;
use Illuminate\Console\Command;

class ImportMissingEpisodesDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:episodes:import-missing-data 
                            {--batch-size=100 : Anzahl der Episoden pro Batch}
                            {--delay=10 : Verzögerung in Sekunden zwischen den Batches}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importiert Daten für Episoden ohne Import (selbst-dispatchend in Batches)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $batchSize = (int)$this->option('batch-size');
        $delay = (int)$this->option('delay');

        $this->info("Starte Episoden-Import in Batches von {$batchSize} mit {$delay}s Verzögerung...");
        $this->info('Der Job dispatched sich automatisch bis alle Episoden importiert sind.');

        try {
            ImportMissingEpisodesDataMissingDataJob::dispatch($batchSize, $delay);
            $this->info('✓ ImportMissingEpisodesDataJob wurde erfolgreich gestartet.');
            $this->comment('Beobachte die Queue und Logs für den Fortschritt.');
            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Fehler beim Dispatchen des Jobs: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}



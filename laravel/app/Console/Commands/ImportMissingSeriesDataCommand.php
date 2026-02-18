<?php

namespace App\Console\Commands;

use App\Jobs\ImportMissingSeriesDataMissingDataJob;
use Illuminate\Console\Command;

class ImportMissingSeriesDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:series:import-missing-data 
                            {--batch-size=50 : Anzahl der Serien pro Batch}
                            {--delay=10 : Verzögerung in Sekunden zwischen den Batches}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importiert Daten für Serien ohne Import (selbst-dispatchend in Batches)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $batchSize = (int)$this->option('batch-size');
        $delay = (int)$this->option('delay');

        $this->info("Starte Serien-Import in Batches von {$batchSize} mit {$delay}s Verzögerung...");
        $this->info('Der Job dispatched sich automatisch bis alle Serien importiert sind.');

        try {
            ImportMissingSeriesDataMissingDataJob::dispatch($batchSize, $delay);
            $this->info('✓ ImportMissingSeriesDataJob wurde erfolgreich gestartet.');
            $this->comment('Beobachte die Queue und Logs für den Fortschritt.');
            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Fehler beim Dispatchen des Jobs: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}



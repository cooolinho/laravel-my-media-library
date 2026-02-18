<?php

namespace App\Jobs;

use App\Jobs\AbstractBaseJob as Job;
use App\Jobs\Concerns\LogsJobActivity;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Throwable;

abstract class AbstractImportMissingDataJob extends Job implements ShouldQueue
{
    use Queueable;
    use LogsJobActivity;

    protected int $batchSize;
    protected int $delaySeconds;

    /**
     * @param int $batchSize Anzahl der Einträge pro Durchlauf
     * @param int $delaySeconds Verzögerung in Sekunden vor dem nächsten Batch
     */
    public function __construct(int $batchSize, int $delaySeconds = 10)
    {
        $this->batchSize = $batchSize;
        $this->delaySeconds = $delaySeconds;
    }

    /**
     * Hauptlogik des Jobs
     */
    public function handle(): void
    {
        $entityName = $this->getEntityName();

        $this->logStart(null, "Prüfe {$entityName} ohne importierte Daten", [
            'batch_size' => $this->batchSize,
            'delay_seconds' => $this->delaySeconds,
            'entity' => $entityName,
        ]);

        try {
            $totalWithoutData = $this->countEntriesWithoutData();

            if ($this->shouldStopProcessing($totalWithoutData, $entityName)) {
                return;
            }

            $entriesToImport = $this->findEntriesWithoutData();
            $this->logBatchInfo($totalWithoutData, $entriesToImport->count(), $entityName);

            $dispatched = $this->dispatchBatch($entriesToImport, $entityName);
            $remainingCount = $totalWithoutData - $entriesToImport->count();

            $this->handleBatchCompletion($dispatched, $entriesToImport->count(), $remainingCount, $entityName);
        } catch (Throwable $e) {
            $this->logFailure($e);
            throw $e;
        }
    }

    /**
     * Gibt den Namen der Entity zurück (z.B. "Serien" oder "Episoden")
     */
    abstract protected function getEntityName(): string;

    /**
     * Zählt die Anzahl der Einträge ohne importierte Daten
     */
    protected function countEntriesWithoutData(): int
    {
        /** @var Model $model */
        $model = $this->getModelClass();

        return $model::query()
            ->whereNull($this->getTimestampColumn())
            ->count();
    }

    /**
     * Gibt die Model-Klasse zurück (z.B. Series::class)
     */
    abstract protected function getModelClass(): string;

    /**
     * Gibt den Namen der Zeitstempel-Spalte zurück
     */
    abstract protected function getTimestampColumn(): string;

    /**
     * Prüft, ob die Verarbeitung gestoppt werden soll (keine Daten zum Importieren)
     */
    protected function shouldStopProcessing(int $totalWithoutData, string $entityName): bool
    {
        if ($totalWithoutData === 0) {
            $this->logSuccess("Alle {$entityName} haben bereits importierte Daten");
            return true;
        }

        return false;
    }

    /**
     * Findet die ersten X Einträge, die noch nie importiert wurden
     */
    protected function findEntriesWithoutData(): Collection
    {
        /** @var Model $model */
        $model = $this->getModelClass();

        return $model::query()
            ->whereNull($this->getTimestampColumn())
            ->limit($this->batchSize)
            ->get();
    }

    /**
     * Loggt Informationen über den aktuellen Batch
     */
    protected function logBatchInfo(int $totalWithoutData, int $batchCount, string $entityName): void
    {
        Log::info("Gefunden: {$totalWithoutData} {$entityName} ohne importierte Daten, verarbeite {$batchCount} in diesem Batch", [
            'total_without_data' => $totalWithoutData,
            'batch_count' => $batchCount,
            'batch_size' => $this->batchSize,
            'entity' => $entityName,
        ]);
    }

    /**
     * Dispatched DataJobs für alle Einträge im Batch
     *
     * @return int Anzahl der erfolgreich dispatched Jobs
     */
    protected function dispatchBatch(Collection $entries, string $entityName): int
    {
        $dispatched = 0;

        foreach ($entries as $entry) {
            if ($this->dispatchSingleEntry($entry, $entityName)) {
                $dispatched++;
            }
        }

        return $dispatched;
    }

    /**
     * Dispatched DataJob für einen einzelnen Eintrag
     *
     * @return bool True bei Erfolg, false bei Fehler
     */
    protected function dispatchSingleEntry(Model $entry, string $entityName): bool
    {
        try {
            $this->dispatchDataJob($entry);

            Log::debug($this->getDataJobName() . " dispatched für {$this->getEntryIdentifier($entry)}", [
                'entry_id' => $entry->id,
                'entity' => $entityName,
            ]);

            return true;
        } catch (Throwable $e) {
            Log::error("Fehler beim Dispatchen von {$this->getDataJobName()} für {$this->getEntryIdentifier($entry)}", [
                'entry_id' => $entry->id,
                'error' => $e->getMessage(),
                'entity' => $entityName,
            ]);

            return false;
        }
    }

    /**
     * Dispatched den entsprechenden DataJob für den Eintrag
     */
    abstract protected function dispatchDataJob(Model $entry): void;

    /**
     * Gibt den Namen des DataJobs zurück (für Logging)
     */
    abstract protected function getDataJobName(): string;

    /**
     * Gibt einen Identifier für den Eintrag zurück (für Logging)
     */
    abstract protected function getEntryIdentifier(Model $entry): string;

    /**
     * Behandelt den Abschluss eines Batches (Logging und ggf. Selbst-Dispatch)
     */
    protected function handleBatchCompletion(int $dispatched, int $processed, int $remaining, string $entityName): void
    {
        $message = $this->buildCompletionMessage($dispatched, $processed, $remaining, $entityName);

        if ($this->shouldDispatchNextBatch($remaining)) {
            $this->dispatchNextBatch($remaining, $entityName);
        }

        $this->logSuccess($message, [
            'dispatched' => $dispatched,
            'processed' => $processed,
            'remaining' => $remaining,
            'continues' => $remaining > 0,
            'entity' => $entityName,
        ]);
    }

    /**
     * Erstellt die Abschluss-Nachricht für den Batch
     */
    protected function buildCompletionMessage(int $dispatched, int $processed, int $remaining, string $entityName): string
    {
        $message = "{$this->getDataJobName()} für {$dispatched} von {$processed} {$entityName} dispatched";

        if ($remaining > 0) {
            $message .= " ({$remaining} verbleiben) - dispatche nächsten Batch";
        } else {
            $message .= " - Import vollständig abgeschlossen!";
        }

        return $message;
    }

    /**
     * Prüft, ob ein weiterer Batch dispatched werden soll
     */
    protected function shouldDispatchNextBatch(int $remaining): bool
    {
        return $remaining > 0;
    }

    /**
     * Dispatched den nächsten Batch mit Verzögerung
     */
    protected function dispatchNextBatch(int $remaining, string $entityName): void
    {
        static::dispatch($this->batchSize, $this->delaySeconds)
            ->delay(now()->addSeconds($this->delaySeconds));

        Log::info("Nächster Batch wird in {$this->delaySeconds} Sekunden gestartet", [
            'remaining' => $remaining,
            'delay_seconds' => $this->delaySeconds,
            'entity' => $entityName,
        ]);
    }
}


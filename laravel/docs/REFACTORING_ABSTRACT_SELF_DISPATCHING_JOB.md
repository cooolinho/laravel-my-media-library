# Refactoring: AbstractSelfDispatchingImportJob

## Übersicht

Die `handle()` Methode wurde in kleinere, fokussierte Untermethoden refaktoriert, um die Lesbarkeit, Wartbarkeit und
Testbarkeit zu verbessern.

## Vorher vs. Nachher

### Vorher: Monolithische handle() Methode

```php
public function handle(): void
{
    // ~80 Zeilen Code
    // - Logging
    // - Zählen
    // - Laden
    // - Foreach mit try-catch
    // - Message building
    // - Selbst-dispatching
    // - Mehr Logging
}
```

### Nachher: Strukturierte handle() Methode

```php
public function handle(): void
{
    // ~28 Zeilen
    $entityName = $this->getEntityName();
    $this->logStart(...);
    
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
```

## Neue Untermethoden

### 1. `shouldStopProcessing(int $totalWithoutData, string $entityName): bool`

**Zweck:** Prüft Early-Exit-Bedingung

**Verantwortung:**

- Prüft, ob Einträge zu verarbeiten sind
- Loggt Erfolg, wenn keine Einträge vorhanden
- Gibt zurück, ob gestoppt werden soll

**Vorteile:**

- ✅ Guard Clause Pattern
- ✅ Klare Single Responsibility
- ✅ Leicht testbar

```php
protected function shouldStopProcessing(int $totalWithoutData, string $entityName): bool
{
    if ($totalWithoutData === 0) {
        $this->logSuccess("Alle {$entityName} haben bereits importierte Daten");
        return true;
    }
    return false;
}
```

### 2. `logBatchInfo(int $totalWithoutData, int $batchCount, string $entityName): void`

**Zweck:** Loggt Batch-Informationen

**Verantwortung:**

- Erstellt Log-Message
- Fügt strukturierte Daten hinzu
- Loggt mit Log::info()

**Vorteile:**

- ✅ Separation of Concerns
- ✅ Wiederverwendbar
- ✅ Konsistente Log-Struktur

```php
protected function logBatchInfo(int $totalWithoutData, int $batchCount, string $entityName): void
{
    Log::info("Gefunden: {$totalWithoutData} {$entityName} ohne importierte Daten, verarbeite {$batchCount} in diesem Batch", [
        'total_without_data' => $totalWithoutData,
        'batch_count' => $batchCount,
        'batch_size' => $this->batchSize,
        'entity' => $entityName,
    ]);
}
```

### 3. `dispatchBatch(Collection $entries, string $entityName): int`

**Zweck:** Dispatched alle Einträge im Batch

**Verantwortung:**

- Iteriert über Einträge
- Ruft `dispatchSingleEntry()` für jeden
- Zählt erfolgreiche Dispatches

**Rückgabe:** Anzahl erfolgreich dispatched Jobs

**Vorteile:**

- ✅ Klarer Scope
- ✅ Zählt nur Erfolge
- ✅ Delegiert an `dispatchSingleEntry()`

```php
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
```

### 4. `dispatchSingleEntry(Model $entry, string $entityName): bool`

**Zweck:** Dispatched einen einzelnen Eintrag

**Verantwortung:**

- Ruft `dispatchDataJob()` auf
- Loggt Erfolg (Debug-Level)
- Fängt und loggt Fehler
- Gibt Erfolg/Fehler zurück

**Rückgabe:** `true` bei Erfolg, `false` bei Fehler

**Vorteile:**

- ✅ Fehlerbehandlung pro Eintrag
- ✅ Einzelne Fehler stoppen nicht den Batch
- ✅ Detailliertes Logging
- ✅ Boolean Return für einfache Auswertung

```php
protected function dispatchSingleEntry(Model $entry, string $entityName): bool
{
    try {
        $this->dispatchDataJob($entry);
        Log::debug(...);
        return true;
    } catch (Throwable $e) {
        Log::error(...);
        return false;
    }
}
```

### 5. `handleBatchCompletion(int $dispatched, int $processed, int $remaining, string $entityName): void`

**Zweck:** Behandelt Batch-Abschluss

**Verantwortung:**

- Erstellt Completion-Message
- Entscheidet über nächsten Batch
- Dispatched ggf. nächsten Batch
- Loggt finalen Erfolg

**Vorteile:**

- ✅ High-Level-Orchestrierung
- ✅ Klare Sequenz
- ✅ Delegiert an Untermethoden

```php
protected function handleBatchCompletion(int $dispatched, int $processed, int $remaining, string $entityName): void
{
    $message = $this->buildCompletionMessage($dispatched, $processed, $remaining, $entityName);
    
    if ($this->shouldDispatchNextBatch($remaining)) {
        $this->dispatchNextBatch($remaining, $entityName);
    }
    
    $this->logSuccess($message, [...]);
}
```

### 6. `buildCompletionMessage(int $dispatched, int $processed, int $remaining, string $entityName): string`

**Zweck:** Erstellt Completion-Nachricht

**Verantwortung:**

- Erstellt Basis-Message
- Fügt Remaining-Info hinzu (bei > 0)
- Fügt Completion-Info hinzu (bei = 0)

**Rückgabe:** Fertige Message-String

**Vorteile:**

- ✅ Pure Function (keine Side Effects)
- ✅ Leicht testbar
- ✅ String-Building isoliert

```php
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
```

### 7. `shouldDispatchNextBatch(int $remaining): bool`

**Zweck:** Entscheidung über weiteren Batch

**Verantwortung:**

- Prüft, ob noch Einträge vorhanden
- Gibt Entscheidung zurück

**Rückgabe:** `true` wenn weiterer Batch nötig

**Vorteile:**

- ✅ Explizite Entscheidungslogik
- ✅ Kann erweitert werden (z.B. Max-Batches)
- ✅ Leicht testbar

```php
protected function shouldDispatchNextBatch(int $remaining): bool
{
    return $remaining > 0;
}
```

### 8. `dispatchNextBatch(int $remaining, string $entityName): void`

**Zweck:** Dispatched nächsten Batch

**Verantwortung:**

- Ruft `static::dispatch()` auf
- Setzt Delay
- Loggt Scheduling

**Vorteile:**

- ✅ Selbst-Dispatching isoliert
- ✅ Klare Verantwortung
- ✅ Logging zentralisiert

```php
protected function dispatchNextBatch(int $remaining, string $entityName): void
{
    static::dispatch($this->batchSize, $this->delaySeconds)
        ->delay(now()->addSeconds($this->delaySeconds));
    
    Log::info("Nächster Batch wird in {$this->delaySeconds} Sekunden gestartet", [...]);
}
```

## Methoden-Hierarchie

```
handle() [orchestriert]
├── countEntriesWithoutData()
├── shouldStopProcessing()
│   └── logSuccess()
├── findEntriesWithoutData()
├── logBatchInfo()
├── dispatchBatch()
│   └── dispatchSingleEntry() [für jeden Eintrag]
│       ├── dispatchDataJob() [abstrakt]
│       └── Log::debug() / Log::error()
└── handleBatchCompletion()
    ├── buildCompletionMessage()
    ├── shouldDispatchNextBatch()
    ├── dispatchNextBatch() [bei Bedarf]
    │   └── static::dispatch()
    └── logSuccess()
```

## Vorteile des Refactorings

### 1. **Lesbarkeit** ✅

- `handle()` Methode liest sich wie eine Geschichte
- Jede Methode hat einen klaren, beschreibenden Namen
- Kein tief verschachtelter Code mehr

### 2. **Single Responsibility Principle** ✅

- Jede Methode hat genau eine Aufgabe
- Änderungen betreffen nur relevante Methode
- Einfacher zu verstehen und zu warten

### 3. **Testbarkeit** ✅

```php
// Einzelne Methoden können isoliert getestet werden
public function test_shouldStopProcessing_returns_true_when_no_data()
{
    $job = new TestableJob(100, 10);
    $result = $job->shouldStopProcessing(0, 'Test');
    $this->assertTrue($result);
}

public function test_buildCompletionMessage_with_remaining()
{
    $job = new TestableJob(100, 10);
    $message = $job->buildCompletionMessage(100, 100, 50, 'Einträge');
    $this->assertStringContainsString('50 verbleiben', $message);
}
```

### 4. **Erweiterbarkeit** ✅

```php
// Überschreiben einzelner Methoden in Subklassen
protected function shouldDispatchNextBatch(int $remaining): bool
{
    // Eigene Logik: z.B. Max-Batches-Limit
    if ($this->batchCount >= $this->maxBatches) {
        return false;
    }
    
    return parent::shouldDispatchNextBatch($remaining);
}
```

### 5. **Fehlerbehandlung** ✅

- Fehler bei einzelnen Einträgen isoliert
- `dispatchSingleEntry()` gibt bool zurück
- Gesamter Batch läuft trotz einzelner Fehler weiter

### 6. **Logging-Struktur** ✅

- Konsistente Log-Messages
- Strukturierte Context-Daten
- Verschiedene Log-Level (info, debug, error)

## Code-Metriken

### Vorher

- `handle()`: ~80 Zeilen
- Cyclomatic Complexity: ~6
- Nesting Level: 3-4

### Nachher

- `handle()`: ~28 Zeilen
- Cyclomatic Complexity: ~2
- Nesting Level: 1-2
-
    + 8 neue fokussierte Methoden (~5-15 Zeilen)

## Best Practices implementiert

✅ **Guard Clauses** - Early Return bei `shouldStopProcessing()`
✅ **Single Responsibility** - Jede Methode eine Aufgabe
✅ **Pure Functions** - `buildCompletionMessage()` hat keine Side Effects
✅ **Boolean Returns** - `dispatchSingleEntry()`, `shouldStopProcessing()`
✅ **Dependency Injection** - Parameter statt Globals
✅ **Meaningful Names** - Selbsterklärende Methodennamen
✅ **Short Methods** - Keine Methode > 20 Zeilen
✅ **Consistent Abstraction** - Alle Methoden auf ähnlichem Level

## Migration Guide

### Keine Änderungen in Subklassen nötig!

Die konkreten Jobs (`ImportMissingSeriesDataJob`, `ImportMissingEpisodesDataJob`) müssen **nicht** angepasst werden. Sie
funktionieren weiterhin ohne Änderungen.

### Optional: Erweiterte Customization

Wenn du spezifisches Verhalten brauchst, kannst du jetzt einzelne Methoden überschreiben:

```php
class ImportMissingMovieDataJob extends AbstractSelfDispatchingImportJob
{
    // Eigene Batch-Logik (z.B. Priorisierung)
    protected function findEntriesWithoutData(): Collection
    {
        return Movie::query()
            ->whereNull($this->getTimestampColumn())
            ->orderBy('release_date', 'desc') // Neueste zuerst
            ->limit($this->batchSize)
            ->get();
    }
    
    // Eigene Stop-Logik
    protected function shouldDispatchNextBatch(int $remaining): bool
    {
        // Nur tagsüber weitermachen
        $hour = now()->hour;
        if ($hour < 6 || $hour > 22) {
            Log::info("Stoppe Batch-Verarbeitung außerhalb der Geschäftszeiten");
            return false;
        }
        
        return parent::shouldDispatchNextBatch($remaining);
    }
}
```

## Zusammenfassung

Das Refactoring hat die `handle()` Methode von einer monolithischen ~80-Zeilen-Methode in eine klar strukturierte
Orchestrierung mit 8 fokussierten Untermethoden verwandelt.

**Ergebnis:**

- ✅ Bessere Lesbarkeit
- ✅ Einfachere Wartung
- ✅ Höhere Testbarkeit
- ✅ Flexible Erweiterbarkeit
- ✅ Robustere Fehlerbehandlung

**Keine Breaking Changes** - Alle bestehenden Jobs funktionieren ohne Änderungen weiter!


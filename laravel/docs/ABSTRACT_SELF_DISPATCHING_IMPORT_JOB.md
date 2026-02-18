# Abstract Self-Dispatching Import Job

## Übersicht

Die `AbstractSelfDispatchingImportJob` Klasse ist eine abstrakte Basisklasse für alle selbst-dispatchenden Import-Jobs.
Sie implementiert die komplette Batch-Verarbeitung und Rekursionslogik, sodass konkrete Jobs nur noch die spezifischen
Details definieren müssen.

## Architektur

```
AbstractSelfDispatchingImportJob (abstrakt)
├── Implementiert: komplette Batch-Logik
├── Implementiert: Selbst-Dispatching mit Delay
├── Implementiert: Logging & Fehlerbehandlung
├── Definiert: abstrakte Methoden für spezifische Details
│
├─── ImportMissingSeriesDataJob (konkret)
│    └── Definiert: Series-spezifische Details
│
└─── ImportMissingEpisodesDataJob (konkret)
     └── Definiert: Episode-spezifische Details
```

## Abstrakte Basisklasse

### Eigenschaften

```php
protected int $batchSize;      // Anzahl Einträge pro Batch
protected int $delaySeconds;   // Verzögerung zwischen Batches
```

### Implementierte Methoden

#### `handle(): void`

Die Hauptlogik des Jobs:

1. Zählt Einträge ohne Daten
2. Lädt einen Batch
3. Dispatched Data-Jobs für jeden Eintrag
4. Dispatched sich selbst rekursiv, wenn noch Einträge übrig
5. Loggt alle Aktivitäten

#### `countEntriesWithoutData(): int`

Zählt die Anzahl der Einträge ohne importierte Daten.

#### `findEntriesWithoutData(): Collection`

Findet die ersten X Einträge ohne Daten (mit LIMIT).

#### `getTimestampColumn(): string`

Gibt den Namen der Zeitstempel-Spalte zurück (Standard: `data_last_updated_at`).

### Abstrakte Methoden (müssen implementiert werden)

#### `getModelClass(): string`

Gibt die Model-Klasse zurück (z.B. `Series::class` oder `Episode::class`).

**Beispiel:**

```php
protected function getModelClass(): string
{
    return Series::class;
}
```

#### `dispatchDataJob(Model $entry): void`

Dispatched den entsprechenden DataJob für den Eintrag.

**Beispiel:**

```php
protected function dispatchDataJob(Model $entry): void
{
    SeriesDataJob::dispatch($entry);
}
```

#### `getDataJobName(): string`

Gibt den Namen des DataJobs zurück (für Logging).

**Beispiel:**

```php
protected function getDataJobName(): string
{
    return 'SeriesDataJob';
}
```

#### `getEntityName(): string`

Gibt den Namen der Entity im Plural zurück (für Logging).

**Beispiel:**

```php
protected function getEntityName(): string
{
    return 'Serien'; // oder 'Episoden'
}
```

#### `getEntryIdentifier(Model $entry): string`

Gibt einen lesbaren Identifier für den Eintrag zurück (für Logging).

**Beispiel:**

```php
protected function getEntryIdentifier(Model $entry): string
{
    /** @var Series $entry */
    return $entry->name;
}
```

## Neue Jobs erstellen

Um einen neuen selbst-dispatchenden Import-Job zu erstellen:

### 1. Klasse erstellen

```php
<?php

namespace App\Jobs;

use App\Models\YourModel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;

class ImportMissingYourModelDataJob extends AbstractImportMissingDataJob implements ShouldQueue
{
    /**
     * @param int $batchSize Anzahl pro Durchlauf (Standard: 50)
     * @param int $delaySeconds Verzögerung vor nächstem Batch (Standard: 10)
     */
    public function __construct(int $batchSize = 50, int $delaySeconds = 10)
    {
        parent::__construct($batchSize, $delaySeconds);
    }

    protected function getModelClass(): string
    {
        return YourModel::class;
    }

    protected function dispatchDataJob(Model $entry): void
    {
        YourModelDataJob::dispatch($entry);
    }

    protected function getDataJobName(): string
    {
        return 'YourModelDataJob';
    }

    protected function getEntityName(): string
    {
        return 'YourModels'; // Plural
    }

    protected function getEntryIdentifier(Model $entry): string
    {
        /** @var YourModel $entry */
        return $entry->name; // oder $entry->id, $entry->title, etc.
    }
}
```

### 2. Command erstellen (optional)

```php
<?php

namespace App\Console\Commands;

use App\Jobs\ImportMissingYourModelDataJob;
use Illuminate\Console\Command;

class ImportMissingYourModelDataCommand extends Command
{
    protected $signature = 'yourmodel:import-missing-data 
                            {--batch-size=50 : Anzahl pro Batch}
                            {--delay=10 : Verzögerung zwischen Batches}';

    protected $description = 'Importiert Daten für YourModel ohne Import';

    public function handle(): int
    {
        $batchSize = (int) $this->option('batch-size');
        $delay = (int) $this->option('delay');

        $this->info("Starte Import in Batches von {$batchSize}...");

        try {
            ImportMissingYourModelDataJob::dispatch($batchSize, $delay);
            $this->info('✓ Job wurde erfolgreich gestartet.');
            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Fehler: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
```

### 3. Fertig!

Das ist alles! Die komplette Batch-Logik wird von der abstrakten Klasse übernommen.

## Code-Reduktion

### Vorher (ohne abstrakte Klasse)

**ImportMissingSeriesDataJob:** ~110 Zeilen Code
**ImportMissingEpisodesDataJob:** ~110 Zeilen Code
**Total:** ~220 Zeilen

### Nachher (mit abstrakte Klasse)

**AbstractSelfDispatchingImportJob:** ~165 Zeilen (einmalig)
**ImportMissingSeriesDataJob:** ~45 Zeilen
**ImportMissingEpisodesDataJob:** ~45 Zeilen
**Total:** ~255 Zeilen

**Aber:** Bei jedem weiteren Job nur noch +45 Zeilen statt +110 Zeilen!

## Vorteile

### 1. DRY (Don't Repeat Yourself)

- Batch-Logik nur einmal implementiert
- Änderungen an einem Ort
- Konsistentes Verhalten

### 2. Einfache Erweiterung

- Neue Jobs in wenigen Minuten
- Nur 5 Methoden implementieren
- Keine Duplikation

### 3. Wartbarkeit

- Bug-Fixes nur einmal
- Features nur einmal hinzufügen
- Klare Struktur

### 4. Testbarkeit

- Abstrakte Klasse kann einmal getestet werden
- Konkrete Jobs brauchen nur spezifische Tests

## Anpassungsmöglichkeiten

### Zeitstempel-Spalte ändern

Wenn dein Model eine andere Spalte verwendet:

```php
protected function getTimestampColumn(): string
{
    return 'last_import_date'; // statt 'data_last_updated_at'
}
```

### Zusätzliche Filterung

Wenn du zusätzliche Filter brauchst, überschreibe die Methoden:

```php
protected function findEntriesWithoutData(): Collection
{
    return $this->getModelClass()::query()
        ->whereNull($this->getTimestampColumn())
        ->where('active', true) // zusätzlicher Filter
        ->limit($this->batchSize)
        ->get();
}
```

### Eigene Logging-Logik

```php
public function handle(): void
{
    // Eigene Pre-Logik
    $this->customPreProcessing();
    
    // Standard-Logik
    parent::handle();
    
    // Eigene Post-Logik
    $this->customPostProcessing();
}
```

## Best Practices

### 1. Standard-Parameter dokumentieren

```php
/**
 * @param int $batchSize Anzahl pro Durchlauf (Standard: 50)
 * @param int $delaySeconds Verzögerung vor nächstem Batch (Standard: 10)
 */
public function __construct(int $batchSize = 50, int $delaySeconds = 10)
```

### 2. Type-Hints verwenden

```php
protected function getEntryIdentifier(Model $entry): string
{
    /** @var Series $entry */
    return $entry->name; // IDE-Support
}
```

### 3. Sinnvolle Defaults

- **Batch-Size:** 50-100 (abhängig von Datenmenge)
- **Delay:** 10 Sekunden (API-Schutz)

### 4. Entity-Namen im Plural

```php
protected function getEntityName(): string
{
    return 'Serien'; // Plural für bessere Log-Messages
}
```

## Beispiel: Vollständiger Job

```php
<?php

namespace App\Jobs;

use App\Models\Movie;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;

class ImportMissingMovieDataJob extends AbstractImportMissingDataJob implements ShouldQueue
{
    public function __construct(int $batchSize = 75, int $delaySeconds = 15)
    {
        parent::__construct($batchSize, $delaySeconds);
    }

    protected function getModelClass(): string
    {
        return Movie::class;
    }

    protected function dispatchDataJob(Model $entry): void
    {
        MovieDataJob::dispatch($entry);
    }

    protected function getDataJobName(): string
    {
        return 'MovieDataJob';
    }

    protected function getEntityName(): string
    {
        return 'Filme';
    }

    protected function getEntryIdentifier(Model $entry): string
    {
        /** @var Movie $entry */
        return "{$entry->title} ({$entry->year})";
    }
}
```

Verwendung:

```bash
php artisan movie:import-missing-data
```

## Zusammenfassung

Die `AbstractSelfDispatchingImportJob` Klasse:

✅ Reduziert Code-Duplikation drastisch
✅ Macht neue Jobs trivial einfach
✅ Zentralisiert Batch-Logik
✅ Verbessert Wartbarkeit
✅ Ermöglicht konsistentes Verhalten
✅ Vereinfacht Testing

**Jeder neue Job benötigt nur noch ~45 Zeilen Code und 5 Methoden!**


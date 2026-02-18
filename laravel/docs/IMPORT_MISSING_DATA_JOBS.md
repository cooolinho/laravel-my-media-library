# Import Missing Data Jobs

## Übersicht

Diese Jobs prüfen, ob Serien und Episoden **überhaupt schon einmal** Daten importiert haben. Im Gegensatz zu den "Check
Data Updates" Jobs, die nach **veralteten** Daten suchen, prüfen diese Jobs nur auf **fehlende Initial-Imports**.

**Wichtig:** Sobald Daten einmal importiert wurden (`data_last_updated_at` ist gesetzt), werden sie von diesen Jobs *
*nicht mehr** berücksichtigt.

## Use Case

Diese Jobs sind ideal für:

- **Erstmaliges Setup:** Wenn du bestehende Serien/Episoden hast, die noch keine Daten von der API haben
- **Neue Serien/Episoden:** Automatischer Initial-Import für neu angelegte Einträge
- **Recovery:** Nach Datenverlust oder Migrationsproblemen

## Jobs

### 1. ImportMissingSeriesDataJob

Findet alle Serien ohne importierte Daten und dispatched `SeriesDataJob` für jede.

**Kriterium:** `data_last_updated_at IS NULL`

**Features:**

- Verwendet effizientes `whereNull()` Query
- Dispatched nur für Serien ohne Zeitstempel
- Vollständiges Logging mit `LogsJobActivity`
- Fehlerbehandlung für einzelne Dispatches

### 2. ImportMissingEpisodesDataJob

Findet alle Episoden ohne importierte Daten und dispatched `EpisodeDataJob` für jede.

**Kriterium:** `data_last_updated_at IS NULL`

**Features:**

- Verwendet effizientes `whereNull()` Query
- Dispatched nur für Episoden ohne Zeitstempel
- Vollständiges Logging mit `LogsJobActivity`
- Fehlerbehandlung für einzelne Dispatches

## Unterschied zu CheckDataUpdatesJobs

| Feature          | ImportMissingDataJobs          | CheckDataUpdatesJobs                       |
|------------------|--------------------------------|--------------------------------------------|
| **Zweck**        | Initial-Import fehlender Daten | Aktualisierung veralteter Daten            |
| **Kriterium**    | `data_last_updated_at IS NULL` | `data_last_updated_at` älter als X Stunden |
| **Wiederholung** | Nur einmal pro Serie/Episode   | Regelmäßig wiederkehrend                   |
| **Zeitfenster**  | Keins                          | Konfigurierbar (z.B. 24h)                  |
| **Verwendung**   | Setup, neue Einträge           | Maintenance, Aktualität                    |

## Verwendung

### Artisan Commands

#### Serien ohne Daten importieren

```bash
php artisan series:import-missing-data
```

#### Episoden ohne Daten importieren

```bash
php artisan episodes:import-missing-data
```

### Programmatisch dispatchen

```php
use App\Jobs\ImportMissingSeriesDataMissingDataJob;
use App\Jobs\ImportMissingEpisodesDataMissingDataJob;

// Serien - alle ohne Limit
ImportMissingSeriesDataMissingDataJob::dispatch();

// Serien - mit Limit
ImportMissingSeriesDataMissingDataJob::dispatch(50);

// Episoden - Standard (100)
ImportMissingEpisodesDataMissingDataJob::dispatch();

// Episoden - eigenes Limit
ImportMissingEpisodesDataMissingDataJob::dispatch(200);
```

### Kombiniert mit CheckDataUpdatesJobs

Du kannst beide Job-Typen kombinieren für ein vollständiges System:

```php
// 1. Erst fehlende Daten importieren
ImportMissingSeriesDataJob::dispatch();

// 2. Dann veraltete Daten aktualisieren
CheckSeriesDataUpdatesJob::dispatch(24);
```

## Task Scheduler Integration

### Empfohlene Konfiguration

```php
use Illuminate\Support\Facades\Schedule;

// Import fehlender Daten: täglich prüfen (für neue Serien/Episoden)
Schedule::command('series:import-missing-data')
    ->daily()
    ->withoutOverlapping();

Schedule::command('episodes:import-missing-data')
    ->daily()
    ->withoutOverlapping();

// Veraltete Daten aktualisieren: wöchentlich oder bei Bedarf
Schedule::command('series:check-data-updates --hours=168')
    ->weekly()
    ->withoutOverlapping();
```

### Alternative: Nur bei Bedarf

Wenn du die Update-Jobs nicht brauchst, reichen die Import-Jobs:

```php
// Nur für neue Einträge, täglich prüfen
Schedule::command('series:import-missing-data')->dailyAt('02:00');
Schedule::command('episodes:import-missing-data')->dailyAt('03:00');
```

## Beispiel-Workflows

### Workflow 1: Erstmaliges Setup

```bash
# 1. Migrationen ausführen
php artisan migrate

# 2. Serien aus vorhandenen Daten erstellen (falls nötig)
# ... deine Logik ...

# 3. Fehlende Daten importieren
php artisan series:import-missing-data
php artisan episodes:import-missing-data

# 4. Queue-Worker starten (falls noch nicht läuft)
php artisan queue:work
```

### Workflow 2: Neue Serie hinzufügen

```php
// Serie erstellen
$series = Series::create([
    'name' => 'Breaking Bad',
    'theTvDbId' => 81189,
    // data_last_updated_at ist NULL
]);

// Automatisch beim nächsten Scheduler-Run importiert
// ODER manuell:
ImportMissingSeriesDataJob::dispatch();
```

### Workflow 3: Event-basiert (fortgeschritten)

Du kannst einen Event Listener erstellen, der automatisch dispatched:

```php
<?php

namespace App\Observers;

use App\Jobs\SeriesDataJob;
use App\Models\Series;

class SeriesObserver
{
    public function created(Series $series): void
    {
        // Automatisch Daten importieren bei neuer Serie
        SeriesDataJob::dispatch($series);
    }
}
```

Registriere den Observer in `AppServiceProvider`:

```php
use App\Models\Series;
use App\Observers\SeriesObserver;

public function boot(): void
{
    Series::observe(SeriesObserver::class);
}
```

## Query-Effizienz

Die Jobs verwenden effiziente Queries:

```php
// Sehr effizient - nutzt Index auf data_last_updated_at
Series::whereNull('data_last_updated_at')->get();

// Im Vergleich zu Check Jobs (weniger effizient bei großen Datenmengen):
Series::all()->filter(fn($s) => $s->needsDataUpdate(24));
```

## Batch-Verarbeitung mit Limits

### Warum Limits?

Bei **vielen Episoden** (z.B. tausende) kann es sinnvoll sein, den Import in mehreren Durchläufen zu machen:

1. **API-Rate-Limits vermeiden**
2. **Queue nicht überlasten**
3. **Überwachung und Kontrolle**
4. **Fehlertoleranz** (einzelne Batches können fehlschlagen)

### Episoden: Standard-Limit von 100

Der `ImportMissingEpisodesDataJob` hat standardmäßig ein Limit von **100 Episoden** pro Durchlauf:

```bash
# Importiert maximal 100 Episoden
php artisan episodes:import-missing-data

# Wenn 500 Episoden fehlen:
# - Durchlauf 1: 100 Episoden (400 verbleiben)
# - Durchlauf 2: 100 Episoden (300 verbleiben)
# - Durchlauf 3: 100 Episoden (200 verbleiben)
# - Durchlauf 4: 100 Episoden (100 verbleiben)
# - Durchlauf 5: 100 Episoden (0 verbleiben)
```

### Serien: Kein Standard-Limit

Der `ImportMissingSeriesDataJob` hat **kein Standard-Limit** (0 = alle), da es normalerweise weniger Serien gibt:

```bash
# Importiert alle Serien ohne Daten
php artisan series:import-missing-data

# Optional mit Limit
php artisan series:import-missing-data --limit=20
```

### Automatische Batch-Verarbeitung im Scheduler

Du kannst den Job mehrmals hintereinander ausführen, bis alle importiert sind:

```php
use Illuminate\Support\Facades\Schedule;

// Episoden: 3x täglich 100 Stück
Schedule::command('episodes:import-missing-data --limit=100')
    ->dailyAt('02:00')
    ->withoutOverlapping();

Schedule::command('episodes:import-missing-data --limit=100')
    ->dailyAt('10:00')
    ->withoutOverlapping();

Schedule::command('episodes:import-missing-data --limit=100')
    ->dailyAt('18:00')
    ->withoutOverlapping();
```

### Monitoring der verbleibenden Episoden

Die Jobs loggen die verbleibende Anzahl:

```json
{
  "message": "EpisodeDataJob für 100 von 100 Episoden dispatched (234 verbleiben)",
  "context": {
    "dispatched": 100,
    "processed": 100,
    "remaining": 234
  }
}
```

### Manuell mehrere Batches ausführen

```bash
# Batch 1
php artisan episodes:import-missing-data --limit=100
# Warte bis Queue abgearbeitet ist...

# Batch 2
php artisan episodes:import-missing-data --limit=100
# Warte bis Queue abgearbeitet ist...

# Batch 3
php artisan episodes:import-missing-data --limit=100
```

### Bei sehr vielen Einträgen: Chunking verwenden

```php
private function findSeriesWithoutData(): \Illuminate\Database\Eloquent\Collection
{
    $result = collect();
    
    Series::whereNull('data_last_updated_at')
        ->chunk(100, function ($series) use ($result) {
            $result->push(...$series);
        });
    
    return $result;
}
```

## Logging

Alle Jobs loggen in die `job_logs` Tabelle:

```json
{
    "job_class": "App\\Jobs\\ImportMissingSeriesDataJob",
    "status": "success",
    "message": "SeriesDataJob für 12 von 12 Serien dispatched",
    "context": {
        "series_count": 12
    },
    "duration_seconds": 0.156
}
```

## Monitoring in Filament

Du kannst eine Action in deiner Series Resource hinzufügen:

```php
use App\Jobs\ImportMissingSeriesDataMissingDataJob;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

protected function getHeaderActions(): array
{
    return [
        Action::make('importMissingData')
            ->label('Fehlende Daten importieren')
            ->icon('heroicon-o-arrow-down-tray')
            ->requiresConfirmation()
            ->modalDescription('Importiert Daten für alle Serien, die noch keine haben.')
            ->action(function () {
                ImportMissingSeriesDataMissingDataJob::dispatch();
                
                Notification::make()
                    ->title('Job gestartet')
                    ->body('Fehlende Serien-Daten werden importiert')
                    ->success()
                    ->send();
            }),
    ];
}
```

### Anzeige in Table Column

```php
use Filament\Tables\Columns\IconColumn;

IconColumn::make('data_last_updated_at')
    ->label('Daten importiert')
    ->boolean()
    ->trueIcon('heroicon-o-check-circle')
    ->falseIcon('heroicon-o-x-circle')
    ->trueColor('success')
    ->falseColor('danger')
    ->getStateUsing(fn ($record) => $record->data_last_updated_at !== null),
```

## Best Practices

### 1. Initial Setup

Führe diese Jobs einmal beim Setup aus, dann nur noch für neue Einträge:

```bash
# Einmalig beim Setup
php artisan series:import-missing-data
php artisan episodes:import-missing-data
```

### 2. Automatischer Import für neue Einträge

Nutze den Task Scheduler (täglich reicht):

```php
Schedule::command('series:import-missing-data')->daily();
```

### 3. Kombiniere mit Update-Jobs

Für ein vollständiges System:

```php
// Täglich: neue Einträge
Schedule::command('series:import-missing-data')->daily();

// Wöchentlich: Updates für bestehende Serien (nur wenn nötig)
Schedule::command('series:check-data-updates --hours=168')->weekly();
```

### 4. Observer für sofortigen Import

Bei kritischen Anwendungen: Observer für automatischen Import bei Erstellung

### 5. Monitoring

Überwache die `job_logs` Tabelle, um zu sehen, wie viele neue Einträge importiert werden

## Troubleshooting

### Keine Serien gefunden

```sql
-- Prüfe, ob Serien ohne Zeitstempel existieren
SELECT COUNT(*)
FROM series
WHERE data_last_updated_at IS NULL;
```

### Job wird nicht dispatched

```bash
# Queue-Status prüfen
php artisan queue:work --once

# Logs prüfen
tail -f storage/logs/laravel.log
```

### Performance-Probleme

Bei tausenden von Serien ohne Daten:

```php
// Limitiere die Anzahl pro Durchlauf
Series::whereNull('data_last_updated_at')
    ->limit(100)
    ->get();

// Oder dispatche in Batches
SeriesDataJob::dispatch($series)->onQueue('imports');
```

## Zusammenfassung

Die **ImportMissingDataJobs** sind perfekt für:

- ✅ Initial-Setup von bestehenden Serien/Episoden
- ✅ Automatischer Import für neue Einträge
- ✅ Recovery nach Problemen
- ✅ Einmalige oder seltene Ausführung

Die **CheckDataUpdatesJobs** sind perfekt für:

- ✅ Regelmäßige Aktualisierung bestehender Daten
- ✅ Aktualität der Informationen
- ✅ Laufende Serien mit neuen Episoden
- ✅ Häufige, wiederkehrende Ausführung

**Beide zusammen** bieten ein vollständiges Daten-Management-System!


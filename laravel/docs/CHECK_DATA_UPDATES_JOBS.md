# Check Data Updates Jobs

## Übersicht

Zwei neue Jobs wurden erstellt, um automatisch zu prüfen, ob Serien- und Episodendaten aktualisiert werden müssen. Diese
Jobs durchsuchen alle Serien/Episoden und dispatchen bei Bedarf die entsprechenden Update-Jobs.

## Jobs

### 1. CheckSeriesDataUpdatesJob

Prüft alle Serien auf veraltete Daten und dispatched `SeriesDataJob` für jede Serie, die aktualisiert werden muss.

**Standard-Zeitfenster:** 24 Stunden

**Features:**

- Durchsucht alle Serien in der Datenbank
- Prüft, ob `data_last_updated_at` null ist oder älter als das angegebene Zeitfenster
- Dispatched `SeriesDataJob` für jede betroffene Serie
- Loggt alle Aktivitäten mit `LogsJobActivity` Trait
- Fehlerbehandlung für individuelle Serie-Updates

### 2. CheckEpisodesDataUpdatesJob

Prüft alle Episoden auf veraltete Daten und dispatched `EpisodeDataJob` für jede Episode, die aktualisiert werden muss.

**Standard-Zeitfenster:** 48 Stunden (länger als Serien, da sich Episodendaten seltener ändern)

**Features:**

- Durchsucht alle Episoden in der Datenbank
- Prüft, ob `data_last_updated_at` null ist oder älter als das angegebene Zeitfenster
- Dispatched `EpisodeDataJob` für jede betroffene Episode
- Loggt alle Aktivitäten mit `LogsJobActivity` Trait
- Fehlerbehandlung für individuelle Episode-Updates

## Verwendung

### Manuell dispatchen

```php
use App\Jobs\CheckSeriesDataUpdatesJob;
use App\Jobs\CheckEpisodesDataUpdatesJob;

// Serien mit Standard-Zeitfenster (24 Stunden)
CheckSeriesDataUpdatesJob::dispatch();

// Serien mit eigenem Zeitfenster (z.B. 12 Stunden)
CheckSeriesDataUpdatesJob::dispatch(12);

// Episoden mit Standard-Zeitfenster (48 Stunden)
CheckEpisodesDataUpdatesJob::dispatch();

// Episoden mit eigenem Zeitfenster (z.B. 72 Stunden)
CheckEpisodesDataUpdatesJob::dispatch(72);
```

### Artisan Commands

Zwei Artisan Commands wurden erstellt, um die Jobs bequem von der Kommandozeile auszuführen:

#### Serien prüfen

```bash
# Mit Standard-Zeitfenster (24 Stunden)
php artisan series:check-data-updates

# Mit eigenem Zeitfenster
php artisan series:check-data-updates --hours=12
```

#### Episoden prüfen

```bash
# Mit Standard-Zeitfenster (48 Stunden)
php artisan episodes:check-data-updates

# Mit eigenem Zeitfenster
php artisan episodes:check-data-updates --hours=72
```

### Task Scheduler (Automatische Ausführung)

Füge folgende Zeilen zu `app/Console/Kernel.php` hinzu, um die Jobs regelmäßig automatisch auszuführen:

```php
<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // Serien täglich um 2 Uhr morgens prüfen
        $schedule->command('series:check-data-updates')
            ->dailyAt('02:00')
            ->withoutOverlapping();

        // Episoden täglich um 3 Uhr morgens prüfen
        $schedule->command('episodes:check-data-updates')
            ->dailyAt('03:00')
            ->withoutOverlapping();
    }
}
```

**Alternative:** Laravel 11+ mit `routes/console.php`:

```php
<?php

use Illuminate\Support\Facades\Schedule;

// Serien täglich um 2 Uhr morgens prüfen
Schedule::command('series:check-data-updates')
    ->dailyAt('02:00')
    ->withoutOverlapping();

// Episoden täglich um 3 Uhr morgens prüfen
Schedule::command('episodes:check-data-updates')
    ->dailyAt('03:00')
    ->withoutOverlapping();
```

### Filament Actions

Du kannst auch Filament Actions erstellen, um die Jobs manuell zu triggern:

```php
use App\Jobs\CheckSeriesDataUpdatesJob;
use Filament\Actions\Action;

// In deiner Filament Resource oder Page
protected function getHeaderActions(): array
{
    return [
        Action::make('checkDataUpdates')
            ->label('Daten-Updates prüfen')
            ->icon('heroicon-o-arrow-path')
            ->requiresConfirmation()
            ->action(function () {
                CheckSeriesDataUpdatesJob::dispatch(24);
                
                Notification::make()
                    ->title('Job gestartet')
                    ->body('Serien werden auf veraltete Daten geprüft')
                    ->success()
                    ->send();
            }),
    ];
}
```

## Änderungen an bestehenden Jobs

### SeriesDataJob

Der `SeriesDataJob` wurde erweitert, um nach erfolgreichem Datenimport den Zeitstempel zu aktualisieren:

```php
try {
    $service->importSeriesData($this->series);
    
    // Zeitstempel aktualisieren nach erfolgreichem Import
    $this->series->touchDataLastUpdatedAt();
    
    $this->logSuccess('Serie erfolgreich aktualisiert');
} catch (Throwable $e) {
    $this->logFailure($e);
    throw $e;
}
```

### EpisodeDataJob

Der `EpisodeDataJob` wurde ebenfalls erweitert:

```php
try {
    $service->importEpisodesData($this->episode);
    
    // Zeitstempel aktualisieren nach erfolgreichem Import
    $this->episode->touchDataLastUpdatedAt();
    
    $this->logSuccess('Episode erfolgreich aktualisiert');
} catch (Throwable $e) {
    $this->logFailure($e);
    throw $e;
}
```

## Logging

Alle Jobs nutzen das `LogsJobActivity` Trait und erstellen Einträge in der `job_logs` Tabelle:

- **Status:** STARTED → SUCCESS/FAILED
- **Context:** Enthält Informationen wie `max_age_hours`, Anzahl gefundener Updates, etc.
- **Dauer:** Wird automatisch berechnet
- **Exceptions:** Werden vollständig geloggt bei Fehlern

### Log-Beispiel

```json
{
    "job_class": "App\\Jobs\\CheckSeriesDataUpdatesJob",
    "status": "success",
    "message": "SeriesDataJob für 5 von 5 Serien dispatched",
    "context": {
        "max_age_hours": 24,
        "series_count": 5
    },
    "duration_seconds": 0.234
}
```

## Performance-Optimierung

### Problem: Große Datenmengen

Bei vielen Serien/Episoden kann `Series::all()` oder `Episode::all()` zu Speicherproblemen führen.

### Lösung: Chunking

Für Produktionsumgebungen mit vielen Datensätzen solltest du die Jobs optimieren:

```php
// In findSeriesToUpdate() verwenden:
private function findSeriesToUpdate(): \Illuminate\Database\Eloquent\Collection
{
    $result = collect();
    
    Series::chunk(100, function ($series) use ($result) {
        foreach ($series as $s) {
            if ($s->needsDataUpdate($this->maxAgeHours)) {
                $result->push($s);
            }
        }
    });
    
    return $result;
}
```

### Rate Limiting

Um die externe API nicht zu überlasten, kannst du Rate Limiting hinzufügen:

```php
use Illuminate\Support\Facades\RateLimiter;

foreach ($seriesToUpdate as $series) {
    RateLimiter::attempt(
        'api-calls',
        $perMinute = 60,
        function() use ($series) {
            SeriesDataJob::dispatch($series);
        }
    );
}
```

## Best Practices

1. **Zeitfenster anpassen:** Nutze unterschiedliche Zeitfenster je nach Datentyp
    - Aktive/laufende Serien: 12-24 Stunden
    - Beendete Serien: 48-72 Stunden oder länger
    - Episoden: 48-72 Stunden

2. **Queue-Konfiguration:** Stelle sicher, dass dein Queue-Worker läuft:
   ```bash
   php artisan queue:work --tries=3 --timeout=300
   ```

3. **Monitoring:** Überwache die `job_logs` Tabelle auf Fehler

4. **Staffelung:** Führe Series- und Episode-Updates zeitversetzt aus, um Last zu verteilen

5. **Fehlerbehandlung:** Die Jobs werfen Exceptions nicht weiter, einzelne Fehler stoppen nicht den gesamten Batch

## Beispiel-Workflow

```
1. Task Scheduler triggert Command
   ↓
2. Command dispatched CheckSeriesDataUpdatesJob
   ↓
3. Job prüft alle Serien
   ↓
4. Für jede veraltete Serie: SeriesDataJob dispatchen
   ↓
5. SeriesDataJob lädt Daten von API
   ↓
6. Bei Erfolg: touchDataLastUpdatedAt()
   ↓
7. Logging in job_logs Tabelle
```

## Troubleshooting

### Job wird nicht ausgeführt

```bash
# Queue-Worker Status prüfen
php artisan queue:work --once

# Failed Jobs anzeigen
php artisan queue:failed

# Failed Job erneut versuchen
php artisan queue:retry <job-id>
```

### Zu viele API-Calls

- Erhöhe das Zeitfenster (z.B. `--hours=48`)
- Implementiere Rate Limiting
- Nutze Queue-Delays: `SeriesDataJob::dispatch($series)->delay(now()->addSeconds(5))`

### Speicherprobleme

- Nutze Chunking (siehe Performance-Optimierung)
- Reduziere die Batch-Größe
- Erhöhe PHP Memory Limit in `php.ini`

## Zusammenfassung

Die neuen Check-Jobs bieten eine automatisierte Lösung, um:

- Veraltete Daten zu identifizieren
- Update-Jobs zu dispatchen
- Den Prozess zu loggen
- Fehler abzufangen ohne den gesamten Batch zu stoppen

Mit den Artisan Commands und dem Task Scheduler kannst du das System vollständig automatisieren.


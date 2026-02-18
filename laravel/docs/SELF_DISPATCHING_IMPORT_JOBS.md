# Self-Dispatching Import Jobs - Rekursive Batch-Verarbeitung

## Übersicht

Die **ImportMissingDataJobs** verwenden ein **selbst-dispatchendes** System für die Batch-Verarbeitung. Das bedeutet:

1. Du startest den Job **einmal**
2. Der Job verarbeitet einen **Batch** (z.B. 100 Episoden)
3. Wenn noch Einträge übrig sind, **dispatched er sich selbst** erneut
4. Dies wiederholt sich automatisch, bis **alle** Einträge importiert sind

## Wie es funktioniert

```
Start: ImportMissingEpisodesDataJob::dispatch(100, 10)
  ↓
Batch 1: Verarbeite 100 Episoden (400 verbleiben)
  ↓ (10 Sekunden Verzögerung)
Batch 2: Verarbeite 100 Episoden (300 verbleiben)  ← automatisch dispatched
  ↓ (10 Sekunden Verzögerung)
Batch 3: Verarbeite 100 Episoden (200 verbleiben)  ← automatisch dispatched
  ↓ (10 Sekunden Verzögerung)
Batch 4: Verarbeite 100 Episoden (100 verbleiben)  ← automatisch dispatched
  ↓ (10 Sekunden Verzögerung)
Batch 5: Verarbeite 100 Episoden (0 verbleiben)    ← automatisch dispatched
  ↓
Fertig! ✓
```

## Jobs

### ImportMissingSeriesDataJob

**Parameter:**

- `$batchSize` (int, Standard: 50): Anzahl Serien pro Batch
- `$delaySeconds` (int, Standard: 10): Verzögerung in Sekunden zwischen Batches

**Automatisches Verhalten:**

- Prüft, wie viele Serien ohne Daten existieren
- Verarbeitet einen Batch
- Wenn noch Serien übrig: dispatched sich selbst mit `delay()`
- Wenn keine mehr übrig: beendet sich

### ImportMissingEpisodesDataJob

**Parameter:**

- `$batchSize` (int, Standard: 100): Anzahl Episoden pro Batch
- `$delaySeconds` (int, Standard: 10): Verzögerung in Sekunden zwischen Batches

**Automatisches Verhalten:**

- Prüft, wie viele Episoden ohne Daten existieren
- Verarbeitet einen Batch
- Wenn noch Episoden übrig: dispatched sich selbst mit `delay()`
- Wenn keine mehr übrig: beendet sich

## Verwendung

### Artisan Commands

#### Episoden (Standard: 100 pro Batch, 10s Verzögerung)

```bash
# Mit Standardwerten
php artisan episodes:import-missing-data

# Eigene Batch-Größe
php artisan episodes:import-missing-data --batch-size=200

# Eigene Verzögerung
php artisan episodes:import-missing-data --delay=5

# Beides anpassen
php artisan episodes:import-missing-data --batch-size=50 --delay=20
```

#### Serien (Standard: 50 pro Batch, 10s Verzögerung)

```bash
# Mit Standardwerten
php artisan series:import-missing-data

# Eigene Batch-Größe
php artisan series:import-missing-data --batch-size=100

# Eigene Verzögerung
php artisan series:import-missing-data --delay=15

# Beides anpassen
php artisan series:import-missing-data --batch-size=25 --delay=30
```

### Programmatisch

```php
use App\Jobs\ImportMissingEpisodesDataMissingDataJob;
use App\Jobs\ImportMissingSeriesDataMissingDataJob;

// Episoden: 100 pro Batch, 10s Verzögerung
ImportMissingEpisodesDataMissingDataJob::dispatch(100, 10);

// Episoden: 200 pro Batch, 5s Verzögerung
ImportMissingEpisodesDataMissingDataJob::dispatch(200, 5);

// Serien: 50 pro Batch, 10s Verzögerung
ImportMissingSeriesDataMissingDataJob::dispatch(50, 10);

// Serien: 25 pro Batch, 30s Verzögerung
ImportMissingSeriesDataMissingDataJob::dispatch(25, 30);
```

## Vorteile des selbst-dispatchenden Systems

### 1. Einfache Verwendung

```bash
# Nur einmal starten - der Rest läuft automatisch
php artisan episodes:import-missing-data
```

Statt:

```bash
# Mehrfach manuell ausführen (altes System)
php artisan episodes:import-missing-data
php artisan episodes:import-missing-data
php artisan episodes:import-missing-data
# ...
```

### 2. Automatische Vollständigkeit

- Der Job läuft **garantiert**, bis alle Einträge importiert sind
- Keine manuellen Wiederholungen nötig
- Keine vergessenen Einträge

### 3. API-Rate-Limit-Schutz

- Verzögerung zwischen Batches (`$delaySeconds`)
- Verhindert Überlastung der externen API
- Anpassbar je nach API-Limits

### 4. Queue-freundlich

- Jobs werden mit `delay()` geplant
- Andere Jobs können zwischendurch laufen
- Keine Blockierung der Queue

### 5. Monitoring & Logging

- Jeder Batch wird einzeln geloggt
- Klare Fortschrittsanzeige
- Verbleibende Anzahl wird angezeigt

## Logging

Jeder Batch loggt seinen Fortschritt:

### Batch-Start

```json
{
    "message": "Prüfe Episoden ohne importierte Daten",
    "context": {
        "batch_size": 100,
        "delay_seconds": 10
    }
}
```

### Batch-Info

```
"Gefunden: 534 Episoden ohne importierte Daten, verarbeite 100 in diesem Batch"
```

### Batch-Erfolg (mit Fortsetzung)

```json
{
    "message": "EpisodeDataJob für 100 von 100 Episoden dispatched (434 verbleiben) - dispatche nächsten Batch",
    "context": {
        "dispatched": 100,
        "processed": 100,
        "remaining": 434,
        "continues": true
    }
}
```

### Letzter Batch (Abschluss)

```json
{
    "message": "EpisodeDataJob für 34 von 34 Episoden dispatched - Import vollständig abgeschlossen!",
    "context": {
        "dispatched": 34,
        "processed": 34,
        "remaining": 0,
        "continues": false
    }
}
```

## Performance-Tuning

### Batch-Größe anpassen

**Zu klein (z.B. 10):**

- ❌ Viele Job-Dispatches
- ❌ Overhead durch viele kleine Batches
- ✅ Sehr geringe Last pro Batch

**Optimal (50-100):**

- ✅ Gutes Gleichgewicht
- ✅ Moderate Last
- ✅ Überschaubare Anzahl Batches

**Zu groß (z.B. 1000):**

- ✅ Wenige Batches
- ❌ Hohe Last pro Batch
- ❌ Lange Laufzeit pro Batch
- ❌ Probleme bei Fehlern

### Verzögerung anpassen

**Keine Verzögerung (0s):**

- ❌ Kann API-Limits überschreiten
- ❌ Queue-Überlastung möglich
- ✅ Schnellster Import

**Kurze Verzögerung (5-10s):**

- ✅ API-Schutz
- ✅ Queue bleibt reaktiv
- ✅ Gute Balance

**Lange Verzögerung (30-60s):**

- ✅ Maximaler API-Schutz
- ✅ Sehr sanft zur Infrastruktur
- ❌ Sehr langer Gesamtimport

## Empfehlungen

### Episoden

```bash
# Standard - gut für die meisten Fälle
php artisan episodes:import-missing-data
# → 100 pro Batch, 10s Verzögerung

# Bei vielen Episoden & strenger API
php artisan episodes:import-missing-data --batch-size=50 --delay=20

# Schneller Import (bei lockerer API)
php artisan episodes:import-missing-data --batch-size=200 --delay=5
```

### Serien

```bash
# Standard - gut für die meisten Fälle
php artisan series:import-missing-data
# → 50 pro Batch, 10s Verzögerung

# Bei sehr vielen Serien
php artisan series:import-missing-data --batch-size=100 --delay=5

# Vorsichtiger Import
php artisan series:import-missing-data --batch-size=25 --delay=30
```

## Scheduler-Integration

Da sich die Jobs selbst dispatchen, brauchst du sie nur **einmal** zu starten:

```php
use Illuminate\Support\Facades\Schedule;

// Einmal täglich prüfen, ob neue Einträge da sind
// Der Job läuft dann automatisch bis alle importiert sind
Schedule::command('series:import-missing-data')
    ->daily()
    ->withoutOverlapping();

Schedule::command('episodes:import-missing-data')
    ->daily()
    ->withoutOverlapping();
```

**Wichtig:** `withoutOverlapping()` verhindert, dass ein neuer Durchlauf startet, während noch einer läuft.

## Monitoring

### Queue beobachten

```bash
# Queue-Worker mit Output
php artisan queue:work --verbose

# Einzelnen Job ausführen (zum Testen)
php artisan queue:work --once
```

### Logs prüfen

```bash
# Laravel-Logs
tail -f storage/logs/laravel.log

# Job-Logs in Datenbank
SELECT * FROM job_logs 
WHERE job_class = 'App\\Jobs\\ImportMissingEpisodesDataJob' 
ORDER BY created_at DESC;
```

### Fortschritt verfolgen

```bash
# Verbleibende Episoden ohne Daten
SELECT COUNT(*) FROM episodes WHERE data_last_updated_at IS NULL;

# Verbleibende Serien ohne Daten
SELECT COUNT(*) FROM series WHERE data_last_updated_at IS NULL;
```

## Fehlerbehandlung

### Was passiert bei Fehlern?

**Einzelner EpisodeDataJob schlägt fehl:**

- ❌ Diese Episode wird nicht importiert
- ✅ Andere Episoden im Batch werden weiter verarbeitet
- ✅ Job läuft normal weiter
- 📝 Fehler wird geloggt

**Ganzer Batch schlägt fehl:**

- ❌ Dieser Batch wird nicht verarbeitet
- ✅ Laravel Queue Retry-Mechanismus greift
- ✅ Job wird erneut versucht (falls konfiguriert)

### Best Practice

```php
// In .env oder Queue-Konfiguration
QUEUE_RETRY_AFTER=600  // 10 Minuten
```

```bash
# Queue-Worker mit Retries
php artisan queue:work --tries=3 --timeout=300
```

## Abbruch & Neustart

### Job manuell stoppen

```bash
# Queue-Worker stoppen
# Laufende Jobs werden beendet
pkill -f "queue:work"
```

### Neustart

```bash
# Einfach erneut starten - er beginnt von vorn
php artisan episodes:import-missing-data

# Prüft automatisch, wie viele noch fehlen
# Und verarbeitet nur die verbleibenden
```

## Unterschied zu vorherigen Versionen

### Alt: Manuelles Limit-System

```bash
# Musste mehrfach manuell ausgeführt werden
php artisan episodes:import-missing-data --limit=100
# → 100 verarbeitet, 400 übrig

php artisan episodes:import-missing-data --limit=100
# → 100 verarbeitet, 300 übrig

# ... usw. (manuell wiederholen)
```

### Neu: Selbst-dispatchendes System

```bash
# Nur einmal starten
php artisan episodes:import-missing-data
# → Job läuft automatisch bis alle fertig sind
```

## Zusammenfassung

✅ **Einmal starten** - automatische Vervollständigung
✅ **API-Schutz** durch konfigurierbare Verzögerung
✅ **Monitoring** durch detailliertes Logging
✅ **Flexibel** - Batch-Größe und Verzögerung anpassbar
✅ **Queue-freundlich** - mit `delay()` geplant
✅ **Fehlertoleranz** - einzelne Fehler stoppen nicht den Gesamtprozess

**Perfekt für große Datenmengen!** 🚀


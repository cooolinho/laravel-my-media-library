# Daten-Management Jobs - Übersicht

## Überblick

Dein Laravel-Projekt verfügt über ein vollständiges System zur Verwaltung von Serien- und Episodendaten von externen
APIs. Es gibt drei Haupttypen von Jobs:

## Job-Typen

### 1. Data Jobs (Import/Update)

**Zweck:** Laden tatsächlich Daten von der API

- `SeriesDataJob` - Lädt Daten für eine einzelne Serie
- `EpisodeDataJob` - Lädt Daten für eine einzelne Episode

**Automatisch:** Setzen `data_last_updated_at` nach erfolgreichem Import

### 2. Import Missing Data Jobs (Initial-Import)

**Zweck:** Finden Serien/Episoden **ohne** importierte Daten

- `ImportMissingSeriesDataJob` - Dispatched SeriesDataJob für Serien ohne Zeitstempel
- `ImportMissingEpisodesDataJob` - Dispatched EpisodeDataJob für Episoden ohne Zeitstempel

**Kriterium:** `data_last_updated_at IS NULL`

**Use Case:**

- Erstmaliges Setup
- Neue Serien/Episoden
- Einmalige Ausführung

### 3. Check Data Updates Jobs (Aktualisierung)

**Zweck:** Finden Serien/Episoden mit **veralteten** Daten

- `CheckSeriesDataUpdatesJob` - Dispatched SeriesDataJob für Serien mit alten Daten
- `CheckEpisodesDataUpdatesJob` - Dispatched EpisodeDataJob für Episoden mit alten Daten

**Kriterium:** `data_last_updated_at` älter als X Stunden (konfigurierbar)

**Use Case:**

- Regelmäßige Updates
- Aktualität der Daten
- Wiederkehrende Ausführung

## Vergleichstabelle

| Feature                      | ImportMissingData        | CheckDataUpdates         | Data Jobs    |
|------------------------------|--------------------------|--------------------------|--------------|
| **Dispatched von**           | Manuell/Scheduler        | Manuell/Scheduler        | Anderen Jobs |
| **Prüft**                    | Fehlende Daten           | Veraltete Daten          | N/A          |
| **Kriterium**                | `IS NULL`                | Älter als X Stunden      | N/A          |
| **Zeitfenster**              | Nein                     | Ja (konfigurierbar)      | N/A          |
| **Häufigkeit**               | Selten/einmalig          | Regelmäßig               | Nach Bedarf  |
| **Dispatched**               | Data Jobs                | Data Jobs                | Nichts       |
| **Aktualisiert Zeitstempel** | Nein (Data Jobs tun das) | Nein (Data Jobs tun das) | Ja           |

## Artisan Commands

### Import Missing Data

```bash
# Serien ohne Daten importieren
php artisan series:import-missing-data

# Episoden ohne Daten importieren
php artisan episodes:import-missing-data
```

### Check Data Updates

```bash
# Serien auf veraltete Daten prüfen (Standard: 24h)
php artisan series:check-data-updates
php artisan series:check-data-updates --hours=12

# Episoden auf veraltete Daten prüfen (Standard: 48h)
php artisan episodes:check-data-updates
php artisan episodes:check-data-updates --hours=72
```

## Empfohlene Task Scheduler Konfiguration

### Option 1: Nur Initial-Import (keine regelmäßigen Updates)

Wenn Daten nach dem Import nicht veralten:

```php
use Illuminate\Support\Facades\Schedule;

// Täglich prüfen auf neue Serien/Episoden
Schedule::command('series:import-missing-data')
    ->daily()
    ->withoutOverlapping();

Schedule::command('episodes:import-missing-data')
    ->daily()
    ->withoutOverlapping();
```

### Option 2: Initial-Import + Regelmäßige Updates

Wenn Daten sich ändern und aktualisiert werden müssen:

```php
use Illuminate\Support\Facades\Schedule;

// Täglich: neue Serien/Episoden
Schedule::command('series:import-missing-data')
    ->dailyAt('02:00')
    ->withoutOverlapping();

Schedule::command('episodes:import-missing-data')
    ->dailyAt('03:00')
    ->withoutOverlapping();

// Wöchentlich: Updates für laufende Serien
Schedule::command('series:check-data-updates --hours=168')
    ->weeklyOn(1, '04:00') // Montags um 4 Uhr
    ->withoutOverlapping();

// Monatlich: Updates für alle Episoden
Schedule::command('episodes:check-data-updates --hours=720')
    ->monthlyOn(1, '05:00') // Jeden 1. des Monats
    ->withoutOverlapping();
```

### Option 3: Aggressives Update-Schema

Für häufig sich ändernde Daten:

```php
// Täglich: neue Einträge
Schedule::command('series:import-missing-data')->daily();
Schedule::command('episodes:import-missing-data')->daily();

// Täglich: Updates für Serien (24h)
Schedule::command('series:check-data-updates --hours=24')
    ->dailyAt('04:00');

// Alle 2 Tage: Updates für Episoden (48h)
Schedule::command('episodes:check-data-updates --hours=48')
    ->twiceDaily(1, 13);
```

## Workflow-Beispiele

### Workflow 1: Erstmaliges Setup

```bash
# 1. Migrationen ausführen
php artisan migrate

# 2. Serien in DB eintragen (ohne Daten)
# ... deine Logik ...

# 3. Fehlende Daten importieren
php artisan series:import-missing-data

# 4. Queue-Worker starten
php artisan queue:work
```

### Workflow 2: Neue Serie hinzufügen

```php
// Serie erstellen (data_last_updated_at ist NULL)
$series = Series::create([
    'name' => 'The Wire',
    'theTvDbId' => 79126,
]);

// Option A: Manuell dispatchen
SeriesDataJob::dispatch($series);

// Option B: Warten auf Scheduler
// ImportMissingSeriesDataJob wird beim nächsten Run ausgeführt
```

### Workflow 3: Daten aktualisieren

```php
// Automatisch via Scheduler
// ODER manuell:
php artisan series:check-data-updates --hours=24
```

## Programmatische Verwendung

### Initial-Import dispatchen

```php
use App\Jobs\ImportMissingSeriesDataMissingDataJob;

ImportMissingSeriesDataMissingDataJob::dispatch();
```

### Update-Check dispatchen

```php
use App\Jobs\CheckSeriesDataUpdatesJob;

// Mit Standard-Zeitfenster (24h)
CheckSeriesDataUpdatesJob::dispatch();

// Mit eigenem Zeitfenster
CheckSeriesDataUpdatesJob::dispatch(12);
```

### Direkt Data Job dispatchen

```php
use App\Jobs\SeriesDataJob;

$series = Series::find(1);
SeriesDataJob::dispatch($series);
```

## Entscheidungshilfe

### Wann welchen Job verwenden?

```
┌─────────────────────────────────────┐
│ Neue Serie/Episode erstellt?       │
└─────────────────┬───────────────────┘
                  │
                  ├─→ Sofort importieren?
                  │   └─→ SeriesDataJob::dispatch($series)
                  │
                  └─→ Automatisch später?
                      └─→ Scheduler: series:import-missing-data
```

```
┌─────────────────────────────────────┐
│ Daten müssen aktualisiert werden?  │
└─────────────────┬───────────────────┘
                  │
                  ├─→ Alle prüfen?
                  │   └─→ series:check-data-updates --hours=X
                  │
                  └─→ Einzelne Serie?
                      └─→ SeriesDataJob::dispatch($series)
```

```
┌─────────────────────────────────────┐
│ Setup: Viele Serien ohne Daten?    │
└─────────────────┬───────────────────┘
                  │
                  └─→ series:import-missing-data
                      (einmalig ausführen)
```

## Monitoring

### JobLog anzeigen in Filament

Alle Jobs loggen ihre Aktivitäten. Du kannst ein Filament Resource erstellen:

```php
// In deiner Filament Resource
Tables\Columns\TextColumn::make('job_class')
    ->searchable()
    ->sortable(),

Tables\Columns\BadgeColumn::make('status')
    ->colors([
        'warning' => 'started',
        'success' => 'success',
        'danger' => 'failed',
    ]),

Tables\Columns\TextColumn::make('message')
    ->limit(50),

Tables\Columns\TextColumn::make('duration_seconds')
    ->label('Dauer (s)')
    ->sortable(),
```

### Statistiken abfragen

```php
use App\Models\JobLog;

// Anzahl heute importierter Serien
$imported = JobLog::where('job_class', 'App\Jobs\ImportMissingSeriesDataMissingDataJob')
    ->where('status', 'success')
    ->whereDate('created_at', today())
    ->count();

// Durchschnittliche Job-Dauer
$avgDuration = JobLog::where('job_class', 'App\Jobs\SeriesDataJob')
    ->where('status', 'success')
    ->avg('duration_seconds');
```

## Troubleshooting

### Jobs werden nicht ausgeführt

```bash
# Queue-Worker läuft?
php artisan queue:work

# Failed Jobs prüfen
php artisan queue:failed
```

### Keine Serien gefunden

```sql
-- Serien ohne Zeitstempel
SELECT COUNT(*)
FROM series
WHERE data_last_updated_at IS NULL;

-- Serien mit altem Zeitstempel (älter als 24h)
SELECT COUNT(*)
FROM series
WHERE data_last_updated_at < NOW() - INTERVAL 24 HOUR;
```

### Zu viele API-Calls

- Erhöhe Zeitfenster für Check-Jobs
- Reduziere Scheduler-Häufigkeit
- Implementiere Rate Limiting

## Dokumentation

Detaillierte Dokumentation für jeden Job-Typ:

- **[DATA_UPDATE_TIMESTAMPS.md](./DATA_UPDATE_TIMESTAMPS.md)** - Zeitstempel-System & Model-Methoden
- **[IMPORT_MISSING_DATA_JOBS.md](./IMPORT_MISSING_DATA_JOBS.md)** - Import Missing Data Jobs
- **[CHECK_DATA_UPDATES_JOBS.md](./CHECK_DATA_UPDATES_JOBS.md)** - Check Data Updates Jobs

## Zusammenfassung

Du hast jetzt ein **drei-stufiges System**:

1. **ImportMissingDataJobs** → Initial-Import für neue Einträge
2. **CheckDataUpdatesJobs** → Regelmäßige Aktualisierung bestehender Daten
3. **DataJobs** → Tatsächlicher API-Import (von den anderen dispatched)

Beide Check-Job-Typen arbeiten mit dem `data_last_updated_at` Zeitstempel:

- NULL = noch nie importiert → ImportMissingDataJobs
- Älter als X Stunden = veraltet → CheckDataUpdatesJobs
- Vorhanden und aktuell = nichts zu tun

**Queue-Worker nicht vergessen:**

```bash
php artisan queue:work
```


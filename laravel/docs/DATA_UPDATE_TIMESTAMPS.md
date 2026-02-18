# Data Update Timestamp System

## Übersicht

Dieses System ermöglicht es, den Zeitpunkt der letzten Aktualisierung von Serien- und Episodendaten zu verfolgen. So
kannst du entscheiden, ob Daten von der externen API neu geladen werden müssen oder ob die vorhandenen Daten noch
aktuell sind.

## Migrationen

Zwei neue Migrationen wurden erstellt:

1. **2026_02_18_000000_add_data_last_updated_at_to_series_table.php**  
   Fügt die Spalte `data_last_updated_at` zur `series` Tabelle hinzu

2. **2026_02_18_000001_add_data_last_updated_at_to_episodes_table.php**  
   Fügt die Spalte `data_last_updated_at` zur `episodes` Tabelle hinzu

### Migration ausführen

```bash
php artisan migrate
```

### Migration rückgängig machen

```bash
php artisan migrate:rollback
```

## Modell-Änderungen

Beide Modelle (`Series` und `Episode`) wurden um folgende Funktionen erweitert:

### Verfügbare Methoden

#### 1. `touchDataLastUpdatedAt(): bool`

Aktualisiert den Zeitstempel auf den aktuellen Zeitpunkt.

**Beispiel:**

```php
$series = Series::find(1);
$series->touchDataLastUpdatedAt();

$episode = Episode::find(1);
$episode->touchDataLastUpdatedAt();
```

#### 2. `needsDataUpdate(int $hours = 24): bool`

Prüft, ob die Daten aktualisiert werden müssen. Standardmäßig werden Daten als veraltet betrachtet, wenn sie älter als
24 Stunden sind.

**Beispiel:**

```php
$series = Series::find(1);

// Prüfen mit Standard-Wert (24 Stunden)
if ($series->needsDataUpdate()) {
    // Daten von API laden
}

// Prüfen mit eigenem Wert (z.B. 12 Stunden)
if ($series->needsDataUpdate(12)) {
    // Daten von API laden
}

// Für Episoden
$episode = Episode::find(1);
if ($episode->needsDataUpdate(48)) {
    // Daten von API laden (48 Stunden)
}
```

#### 3. `getDataAgeInHours(): ?float`

Gibt zurück, wie viele Stunden seit der letzten Aktualisierung vergangen sind. Gibt `null` zurück, wenn noch nie
aktualisiert wurde.

**Beispiel:**

```php
$series = Series::find(1);
$age = $series->getDataAgeInHours();

if ($age === null) {
    echo "Daten wurden noch nie aktualisiert";
} else {
    echo "Daten sind {$age} Stunden alt";
}
```

## Verwendungsbeispiele

### Beispiel 1: Daten von API laden und Zeitstempel setzen

```php
use App\Models\Series;
use App\Services\TheTvDbService;

$series = Series::find(1);

// Prüfen, ob Daten aktualisiert werden müssen
if ($series->needsDataUpdate()) {
    // Daten von API laden
    $apiService = app(TheTvDbService::class);
    $data = $apiService->getSeriesData($series->theTvDbId);
    
    // Daten in Datenbank speichern
    // ... Deine Logik hier ...
    
    // Zeitstempel aktualisieren
    $series->touchDataLastUpdatedAt();
}
```

### Beispiel 2: Batch-Aktualisierung mit eigenem Zeitfenster

```php
use App\Models\Series;

// Alle Serien finden, die in den letzten 6 Stunden nicht aktualisiert wurden
$series = Series::all()->filter(function ($series) {
    return $series->needsDataUpdate(6);
});

foreach ($series as $s) {
    // API-Daten laden
    // ... Deine Logik hier ...
    
    // Zeitstempel aktualisieren
    $s->touchDataLastUpdatedAt();
}
```

### Beispiel 3: Episoden einer Serie aktualisieren

```php
use App\Models\Series;

$series = Series::with('episodes')->find(1);

foreach ($series->episodes as $episode) {
    if ($episode->needsDataUpdate(24)) {
        // API-Daten für Episode laden
        // ... Deine Logik hier ...
        
        $episode->touchDataLastUpdatedAt();
    }
}

// Series-Zeitstempel auch aktualisieren
$series->touchDataLastUpdatedAt();
```

### Beispiel 4: Job für automatische Aktualisierung

```php
<?php

namespace App\Jobs;

use App\Models\Series;
use App\Services\TheTvDbService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateSeriesDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private int $seriesId,
        private int $maxAgeHours = 24
    ) {}

    public function handle(TheTvDbService $apiService): void
    {
        $series = Series::find($this->seriesId);
        
        if (!$series || !$series->needsDataUpdate($this->maxAgeHours)) {
            return;
        }

        // API-Daten laden
        $data = $apiService->getSeriesData($series->theTvDbId);
        
        // Daten verarbeiten und speichern
        // ... Deine Logik hier ...
        
        // Zeitstempel aktualisieren
        $series->touchDataLastUpdatedAt();
    }
}
```

### Beispiel 5: Filament Resource mit Update-Status

```php
<?php

namespace App\Filament\Resources\SeriesResource\Pages;

use Filament\Tables;
use Filament\Tables\Columns\TextColumn;

// In deiner Table-Definition:
TextColumn::make('data_last_updated_at')
    ->label('Zuletzt aktualisiert')
    ->dateTime('d.m.Y H:i')
    ->sortable()
    ->tooltip(fn ($record) => $record->getDataAgeInHours() 
        ? round($record->getDataAgeInHours(), 1) . ' Stunden alt' 
        : 'Noch nie aktualisiert'),

Tables\Columns\BadgeColumn::make('needs_update')
    ->label('Status')
    ->getStateUsing(fn ($record) => $record->needsDataUpdate() ? 'Veraltet' : 'Aktuell')
    ->colors([
        'success' => 'Aktuell',
        'warning' => 'Veraltet',
    ]),
```

## Best Practices

1. **Individuelles Zeitfenster**: Nutze unterschiedliche Zeitfenster je nach Datentyp:
    - Serien-Grunddaten: 24-48 Stunden
    - Episoden-Daten: 48-72 Stunden
    - Laufende Serien: kürzere Intervalle (z.B. 12 Stunden)

2. **Batch-Verarbeitung**: Vermeide es, alle Daten auf einmal zu aktualisieren. Nutze Jobs und Queues.

3. **Fehlerbehandlung**: Aktualisiere den Zeitstempel nur bei erfolgreicher API-Antwort.

4. **Null-Checks**: Prüfe immer, ob `data_last_updated_at` null ist, bevor du berechnungen durchführst.

5. **Logging**: Logge Aktualisierungen, um Probleme nachvollziehen zu können.

## Datenbankfelder

### Series Tabelle

- `data_last_updated_at` (timestamp, nullable): Zeitpunkt der letzten Datenaktualisierung

### Episodes Tabelle

- `data_last_updated_at` (timestamp, nullable): Zeitpunkt der letzten Datenaktualisierung

## Technische Details

- **Cast**: Beide Felder werden automatisch als Carbon-Instanzen gecasted
- **Nullable**: Die Felder sind nullable, da bei der ersten Erstellung noch keine Aktualisierung stattgefunden hat
- **Timestamps**: Die Modelle verwenden NICHT die Laravel-Standard-Timestamps (`created_at`, `updated_at`)


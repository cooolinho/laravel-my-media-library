# Translation System Migration

## Überblick

Dieses Dokument beschreibt die Migration des Übersetzungssystems von einem JSON-Feld zu separaten Übersetzungstabellen
für `episode_data` und `series_data`.

## Neue Struktur

### Tabellen

#### `episode_translations`

- `id`: Primary Key
- `episode_data_id`: Foreign Key zu `episode_data`
- `lang`: Sprachcode (z.B. 'de', 'en', 'deu', 'eng')
- `name`: Übersetzter Name/Titel
- `overview`: Übersetzter Überblick/Beschreibung
- `created_at`, `updated_at`: Timestamps
- Unique Index auf `(episode_data_id, lang)`

#### `series_translations`

- `id`: Primary Key
- `series_data_id`: Foreign Key zu `series_data`
- `lang`: Sprachcode
- `name`: Übersetzter Name/Titel
- `overview`: Übersetzter Überblick/Beschreibung
- `created_at`, `updated_at`: Timestamps
- Unique Index auf `(series_data_id, lang)`

### Models

#### `EpisodeTranslation`

```php
use App\Models\TheTvDB\EpisodeTranslation;

// Zugriff auf Übersetzung
$translation = EpisodeTranslation::where('episode_data_id', 1)
    ->where('lang', 'de')
    ->first();
```

#### `SeriesTranslation`

```php
use App\Models\TheTvDB\SeriesTranslation;

// Zugriff auf Übersetzung
$translation = SeriesTranslation::where('series_data_id', 1)
    ->where('lang', 'de')
    ->first();
```

### Beziehungen

```php
// Von EpisodeData zu Übersetzungen
$episodeData->episodeTranslations; // Collection von EpisodeTranslation

// Von SeriesData zu Übersetzungen
$seriesData->seriesTranslations; // Collection von SeriesTranslation

// Von Translation zurück zu Data
$episodeTranslation->episodeData;
$seriesTranslation->seriesData;
```

## Migration durchführen

### Schritt 1: Migrationen ausführen

```bash
cd laravel
php artisan migrate
```

Dies erstellt die neuen Tabellen:

- `episode_translations`
- `series_translations`

### Schritt 2: Daten migrieren

```bash
# Dry-run (nur anzeigen, nicht speichern)
php artisan thetvdb:migrate-translations --dry-run

# Alle Übersetzungen migrieren
php artisan thetvdb:migrate-translations

# Nur Episode-Übersetzungen
php artisan thetvdb:migrate-translations --type=episodes

# Nur Series-Übersetzungen
php artisan thetvdb:migrate-translations --type=series
```

### Schritt 3: Altes JSON-Feld entfernen (optional)

**WICHTIG:** Führe diesen Schritt erst aus, nachdem du sichergestellt hast, dass alle Daten korrekt migriert wurden!

```bash
php artisan migrate
```

Die Migration `2026_02_16_100002_remove_translations_json_columns.php` entfernt das `translations` JSON-Feld aus beiden
Tabellen.

Um dies rückgängig zu machen:

```bash
php artisan migrate:rollback --step=1
```

## Verwendung im Code

### Bestehende API bleibt gleich

Der `TranslatableTrait` wurde so angepasst, dass er automatisch die neue Tabellenstruktur verwendet, falls vorhanden,
und sonst auf das JSON-Feld zurückfällt.

```php
// Diese Methoden funktionieren weiterhin wie zuvor:
$episodeData->name;      // Gibt übersetzten Namen zurück
$episodeData->overview;  // Gibt übersetzten Überblick zurück
$episodeData->getName();
$episodeData->getOverview();
```

### Neue Übersetzungen hinzufügen

```php
use App\Models\TheTvDB\EpisodeTranslation;

EpisodeTranslation::create([
    'episode_data_id' => $episodeData->id,
    'lang' => 'de',
    'name' => 'Deutscher Titel',
    'overview' => 'Deutsche Beschreibung',
]);
```

### Übersetzungen aktualisieren

```php
EpisodeTranslation::updateOrCreate(
    [
        'episode_data_id' => $episodeData->id,
        'lang' => 'de',
    ],
    [
        'name' => 'Neuer deutscher Titel',
        'overview' => 'Neue deutsche Beschreibung',
    ]
);
```

### Eager Loading

```php
// Übersetzungen mit laden
$episodeData = EpisodeData::with('episodeTranslations')->find(1);
$seriesData = SeriesData::with('seriesTranslations')->find(1);
```

## TranslatableTrait Upgrade (optional)

Wenn du auf die neue Version des TranslatableTrait upgraden möchtest, die automatisch zwischen JSON und Tabelle
wechselt:

1. Backup der alten Datei erstellen:

```bash
cp app/Models/TheTvDB/TranslatableTrait.php app/Models/TheTvDB/TranslatableTrait.php.backup
```

2. Neue Version verwenden:

```bash
mv app/Models/TheTvDB/TranslatableTraitNew.php app/Models/TheTvDB/TranslatableTrait.php
```

## Vorteile des neuen Systems

1. **Bessere Performance**: Direkte Abfragen auf Übersetzungen möglich
2. **Einfachere Wartung**: Klare Tabellenstruktur statt JSON-Manipulation
3. **Bessere Indizierung**: Optimale Performance durch Indexes
4. **Einfachere Abfragen**: SQL-Joins statt JSON-Parsing
5. **Skalierbarkeit**: Einfacher erweiterbar für zusätzliche Felder

## Rollback

Falls Probleme auftreten:

```bash
# Schritt 3 rückgängig machen (JSON-Felder wiederherstellen)
php artisan migrate:rollback --step=1

# Schritt 1 rückgängig machen (Tabellen löschen)
php artisan migrate:rollback --step=3
```

Die Daten im alten JSON-Format bleiben erhalten, solange du Schritt 3 nicht ausgeführt hast.

## Notizen

- Das alte `translations` JSON-Feld bleibt zunächst bestehen für Rückwärtskompatibilität
- Der `TranslatableTrait` funktioniert mit beiden Systemen
- Nach erfolgreicher Migration kann das JSON-Feld entfernt werden
- Alle Übersetzungen werden mit `updateOrCreate()` migriert, sodass keine Duplikate entstehen


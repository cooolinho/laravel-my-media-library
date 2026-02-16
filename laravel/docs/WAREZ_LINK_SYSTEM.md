# Warez Link System

## Übersicht

Das Warez Link System ermöglicht es, externe Links zu Websites zu definieren, auf denen nach Serien gesucht werden kann.
Diese Links werden auf der ViewSeries-Seite in einem Tab-Widget angezeigt.

## Features

- **Flexible Platzhalter**: Unterstützt zwei verschiedene Platzhalter-Typen:
    - `<SERIES_NAME>`: Wird durch den Namen der Serie ersetzt (URL-encoded)
    - `<TVDB_ID>`: Wird durch die TheTVDB-ID der Serie ersetzt

- **Verwaltung über Filament**: Warez Links können über die Filament-Admin-Oberfläche verwaltet werden

- **Tab-basierte Anzeige**: Links werden als Tabs auf der ViewSeries-Seite angezeigt, jeder Tab zeigt die entsprechende
  Website in einem iFrame

## Verwendung

### 1. Warez Link erstellen

Navigieren Sie zu "Warez Links" in der Admin-Oberfläche und erstellen Sie einen neuen Link:

- **Titel**: Name der Website (z.B. "Serienjunkies.org")
- **URL**: URL mit Platzhalter (z.B. `https://serienjunkies.org/serie/search?q=<SERIES_NAME>`)
- **Platzhalter-Typ**: Wählen Sie zwischen:
    - "Seriename (<SERIES_NAME>)" - Für Suchen nach Serienname
    - "TheTVDB ID (<TVDB_ID>)" - Für direkte Links mit TVDB-ID

### 2. Anzeige auf ViewSeries-Seite

Die konfigurierten Links werden automatisch auf der Serien-Detailseite angezeigt:

- Jeder Link erhält einen eigenen Tab
- Beim Klick auf einen Tab wird die Website mit den ersetzten Platzhaltern geladen
- Der erste Tab zeigt immer TheTVDB

## Beispiele

### Beispiel 1: Suche nach Serienname

```
Titel: Serienjunkies.org
URL: https://serienjunkies.org/serie/search?q=<SERIES_NAME>
Platzhalter-Typ: series_name
```

### Beispiel 2: Direkte TVDB-ID

```
Titel: TheTVDB (by ID)
URL: https://thetvdb.com/dereferrer/series/<TVDB_ID>
Platzhalter-Typ: tvdb_id
```

### Beispiel 3: IMDb Suche

```
Titel: IMDb Suche
URL: https://www.imdb.com/find?q=<SERIES_NAME>&s=tt&ttype=tv
Platzhalter-Typ: series_name
```

## Technische Details

### Datenbank-Schema

```sql
CREATE TABLE warez_links
(
    id               BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title            VARCHAR(255) NOT NULL,
    url              VARCHAR(255) NOT NULL,
    placeholder_type VARCHAR(255) DEFAULT 'series_name',
    created_at       TIMESTAMP NULL,
    updated_at       TIMESTAMP NULL
);
```

### Model-Klasse

Die `WarezLink`-Model-Klasse enthält:

- Konstanten für Platzhalter-Typen: `PLACEHOLDER_SERIES_NAME` und `PLACEHOLDER_TVDB_ID`
- Methode `getIframeUrl(Series $series)`: Erstellt die finale URL mit ersetzten Platzhaltern
- Methode `getPlaceholderTypes()`: Liefert verfügbare Platzhalter-Typen für das Formular

### Widget

Das `WarezLinkWidget` zeigt alle Warez Links auf der ViewSeries-Seite an:

- Holt alle `WarezLink`-Einträge aus der Datenbank
- Übergibt sie zusammen mit der aktuellen Serie an die Blade-View
- Die View erstellt Tabs mit iFrames für jeden Link

## Migration

Um das System zu aktivieren, führen Sie folgende Befehle aus:

```bash
php artisan migrate
php artisan db:seed --class=WarezLinkSeeder
```

Dies erstellt die Datenbank-Tabelle und fügt Beispiel-Links hinzu.


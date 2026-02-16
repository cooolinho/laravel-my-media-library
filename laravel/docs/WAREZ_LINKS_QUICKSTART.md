# Warez Links - Quick Start Guide

## Installation

### Schritt 1: Migration ausführen

```bash
cd laravel
php artisan migrate
```

Dies erstellt die `warez_links` Tabelle mit dem neuen `placeholder_type` Feld.

### Schritt 2: Beispiel-Daten einfügen

```bash
php artisan db:seed --class=WarezLinkSeeder
```

Dies fügt folgende Beispiel-Links hinzu:

- **Serienjunkies.org** (mit SERIES_NAME)
- **TheTVDB** (mit TVDB_ID)
- **IMDb Suche** (mit SERIES_NAME)

### Alternative: Alles auf einmal

```bash
cd laravel
bash setup-warez-links.sh
```

## Verwendung

1. **Öffnen Sie eine Serien-Detailseite** in Ihrer Filament-Admin-Oberfläche
2. **Scrollen Sie zum Warez Links Widget** - hier sehen Sie Tabs für jeden konfigurierten Link
3. **Klicken Sie auf einen Tab** - die entsprechende Website wird geladen mit automatisch ersetzten Platzhaltern

## Eigene Links hinzufügen

1. Navigieren Sie zu **"Warez Links"** im Admin-Menü
2. Klicken Sie auf **"Neu erstellen"**
3. Füllen Sie das Formular aus:
    - **Titel**: Name der Website (wird als Tab-Beschriftung angezeigt)
    - **URL**: Die URL mit Platzhalter
    - **Platzhalter-Typ**: Wählen Sie zwischen:
        - `Seriename (<SERIES_NAME>)` - Der Serienname wird URL-encoded eingefügt
        - `TheTVDB ID (<TVDB_ID>)` - Die numerische TVDB-ID wird eingefügt

## Beispiele für URLs

### Mit Serienname

```
https://serienjunkies.org/serie/search?q=<SERIES_NAME>
https://www.imdb.com/find?q=<SERIES_NAME>&s=tt&ttype=tv
https://google.com/search?q=<SERIES_NAME>+stream
```

### Mit TVDB-ID

```
https://thetvdb.com/dereferrer/series/<TVDB_ID>
https://api.thetvdb.com/series/<TVDB_ID>
```

## Fehlerbehebung

### "Column not found: placeholder_type"

➡️ Führen Sie die Migration aus: `php artisan migrate`

### Keine Tabs werden angezeigt

➡️ Stellen Sie sicher, dass Warez Links in der Datenbank existieren: `php artisan db:seed --class=WarezLinkSeeder`

### URLs werden nicht korrekt ersetzt

➡️ Überprüfen Sie, dass:

- Der Platzhalter korrekt geschrieben ist (`<SERIES_NAME>` oder `<TVDB_ID>`)
- Der richtige Platzhalter-Typ ausgewählt ist
- Die Serie eine gültige TVDB-ID hat (für TVDB_ID Platzhalter)


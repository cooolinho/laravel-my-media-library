# Dashboard Dokumentation

## Übersicht

Das Admin Dashboard bietet einen umfassenden Überblick über Ihre Serien-Medienbibliothek, inklusive Episoden, Jobs und
API-Logs.

## Widgets

### 1. StatsOverviewWidget

**Sortierung:** 1 (ganz oben)

Zeigt die wichtigsten Statistiken auf einen Blick:

- **Serien:** Gesamtanzahl der verwalteten Serien
- **Episoden:** Gesamtanzahl mit Besitz-Status
- **Fehlende Episoden:** Anzahl der noch nicht heruntergeladenen Episoden
- **Wartende Jobs:** Anzahl der Jobs in der Warteschlange
- **API Aufrufe (Heute):** Tägliche API-Aufrufe mit Fehlerstatistik
- **Besitz-Rate:** Prozentsatz der besessenen Episoden

**Farbcodierung:**

- Grün: Erfolg/Gut
- Gelb/Orange: Warnung
- Rot: Fehler/Kritisch

### 2. SeriesChartWidget

**Sortierung:** 2

Balkendiagramm der Top 10 Serien mit:

- Gesamtanzahl der Episoden (blau)
- Besessene Episoden (grün)

### 3. EpisodesBySeasonWidget

**Sortierung:** 3

Liniendiagramm der Episoden nach Staffeln:

- Zeigt Verteilung über alle Staffeln
- Vergleicht Gesamt vs. Besessen

### 4. RecentJobsWidget

**Sortierung:** 4

Tabelle der letzten Jobs in der Warteschlange:

- Job ID
- Queue-Name
- Job-Name (gekürzt, Tooltip zeigt vollen Namen)
- Versuche (mit Farbcodierung)
- Erstellungszeitpunkt (relativ und absolut)
- Verfügbar ab

**Features:**

- Paginierung (5, 10, 25 Einträge)
- Sortierbar nach allen Spalten
- Suchfunktion

### 5. ApiLogsWidget

**Sortierung:** 5

Tabelle der letzten TheTVDB API-Aufrufe:

- Erfolgs-Status (grün/rot Icon)
- HTTP-Methode (farbcodiert)
- Endpoint (gekürzt, Tooltip zeigt vollen Endpoint)
- Cache-Status (Blitz für Cache, Cloud für Live)
- HTTP-Status-Code (farbcodiert)
- Response-Zeit in ms (farbcodiert: < 500ms grün, < 1000ms gelb, > 1000ms rot)
- Zeitpunkt (relativ und absolut)

**Features:**

- Paginierung (5, 10, 25 Einträge)
- Filter für Erfolg/Fehler
- Filter für Cache/Live
- Suchfunktion

### 6. ApiStatsChartWidget

**Sortierung:** 6

Liniendiagramm der API-Aufrufe der letzten 7 Tage:

- Erfolgreiche Aufrufe (grün)
- Cache Treffer (orange)
- Fehler (rot)

### 7. DashboardSeriesWidget

**Sortierung:** 7 (ganz unten)

Grid-Ansicht der Top 6 Serien nach Episodenanzahl:

- Serienname
- Besessene Episoden (grüner Badge)
- Fehlende Episoden (gelber Badge)
- Fortschrittsbalken mit Prozentanzeige
- Hover-Effekte

## Design-Features

### Animationen

- **Fade-In Animation:** Alle Widgets erscheinen mit zeitversetzter Fade-In-Animation
- **Shimmer-Effekt:** Fortschrittsbalken haben einen animierten Shimmer-Effekt
- **Hover-Effekte:** Cards und Statistik-Karten heben sich beim Hover ab

### Responsive Design

- **Desktop:** Grid-Layout mit optimaler Platznutzung
- **Tablet:** Angepasstes Grid mit weniger Spalten
- **Mobile:** Single-Column-Layout für optimale Lesbarkeit

### Dark Mode

- Vollständige Dark Mode Unterstützung
- Angepasste Farben und Kontraste
- Optimierte Lesbarkeit in beiden Modi

### Farbschema

- **Primary:** Lila-Gradient (Amber von Filament angepasst)
- **Success:** Grün
- **Warning:** Orange
- **Danger:** Rot
- **Info:** Blau

## CSS-Struktur

Die Dashboard-Styles befinden sich in:

```
laravel/resources/css/filament/admin/pages/_dashboard.scss
```

### Hauptsektionen:

1. **Dashboard Grid Layout** - Grundlegendes Grid
2. **Stats Overview Widget** - Statistik-Karten-Styles
3. **Chart Widgets** - Chart-Container-Styles
4. **Series Widget** - Grid und Card-Styles für Serien
5. **Table Widgets** - Tabellen-Styles für Jobs und Logs
6. **Responsive Improvements** - Mobile Anpassungen
7. **Dark Mode Enhancements** - Dark Mode spezifische Styles
8. **Custom Animations** - Animationen
9. **Print Styles** - Druck-optimierte Styles

## Verwendung

### Assets kompilieren

Nach Änderungen an SCSS-Dateien:

```bash
docker-compose exec -it -u sail laravel bash -c "cd /var/www/html && npm run build"
```

### Development mit Hot Reload

```bash
docker-compose exec -it -u sail laravel bash -c "cd /var/www/html && npm run dev"
```

### Cache leeren

Falls Änderungen nicht sichtbar sind:

```bash
docker-compose exec -it -u sail laravel bash -c "cd /var/www/html && php artisan filament:clear-cache && php artisan view:clear"
```

## Anpassungen

### Widget-Reihenfolge ändern

In jedem Widget die `$sort` Property ändern:

```php
protected static ?int $sort = 1; // Niedrigere Nummer = weiter oben
```

### Anzahl der angezeigten Items ändern

In Table Widgets:

```php
->paginated([5, 10, 25]); // Ändere die Werte
```

In Chart Widgets:

```php
->limit(10); // Ändere die Anzahl
```

### Farben anpassen

In `_dashboard.scss` die Gradient-Werte ändern:

```scss
background:

linear-gradient
(
135
deg, #667eea

0
%, #764ba2

100
%)

;
```

## Performance-Tipps

1. **Caching:** API-Logs werden automatisch gecacht
2. **Limits:** Queries haben Limits um Performance zu gewährleisten
3. **Eager Loading:** Relationships werden mit `withCount()` optimiert
4. **Pagination:** Große Tabellen sind paginiert

## Fehlerbehebung

### Widgets werden nicht angezeigt

1. Assets neu kompilieren: `npm run build`
2. Cache leeren: `php artisan filament:clear-cache`
3. Browser-Cache leeren

### Styles werden nicht angewendet

1. Prüfen ob `_dashboard.scss` in `theme.css` importiert ist
2. Assets neu kompilieren
3. Vite-Server neu starten

### Keine Daten in Widgets

1. Prüfen ob Datenbank-Tabellen Daten enthalten
2. Prüfen ob Relationships korrekt sind
3. Laravel-Logs prüfen: `storage/logs/laravel.log`

## Erweiterungsmöglichkeiten

### Neues Widget hinzufügen

1. Widget-Klasse erstellen: `php artisan make:filament-widget MyWidget`
2. Widget konfigurieren (Type, Sort, etc.)
3. Styles in `_dashboard.scss` hinzufügen
4. Assets kompilieren

### Zusätzliche Statistiken

Erweitere `StatsOverviewWidget::getStats()` um weitere Stat-Objekte.

### Zusätzliche Charts

Erstelle neue Chart-Widgets basierend auf `SeriesChartWidget` oder `EpisodesBySeasonWidget`.

## Best Practices

1. **Performance:** Verwende immer Limits bei großen Datensätzen
2. **Lesbarkeit:** Nutze aussagekräftige Labels und Beschreibungen
3. **Accessibility:** Stelle sicher, dass Farben ausreichend Kontrast haben
4. **Mobile:** Teste alle Widgets auf mobilen Geräten
5. **Dark Mode:** Teste alle Änderungen in beiden Modi

## Support

Bei Fragen oder Problemen:

1. Laravel Logs prüfen
2. Browser Console prüfen
3. Filament Dokumentation: https://filamentphp.com/docs


# TheTVDB API Statistics - Performance Optimization

## Problem

Bei mehr als 1000 API-Endpoints wurde die Statistikseite extrem lang und langsam beim Laden. Alle Endpoints wurden auf
einmal gerendert, was zu Performance-Problemen führte.

## Lösung

Implementierung eines Pagination- und Sortierungssystems für die Endpoint-Statistiken.

## Implementierte Features

### 1. Clear All Logs Action

- **Rote "Clear All Logs" Button** in der Header-Leiste
- **Bestätigungsdialog** mit Warnung vor irreversibler Aktion
- **Erfolgsmeldung** mit Anzahl der gelöschten Einträge
- **Fehlerbehandlung** mit Notification bei Problemen
- **Auto-Reload** der Statistiken nach dem Löschen

### 2. Pagination

- **25 Endpoints pro Seite** (Standard)
- Vollständige Pagination mit Navigation
- Desktop: Erweiterte Navigation mit Seitenzahlen
- Mobile: Simple Previous/Next Navigation
- Anzeige der aktuellen Position (z.B. "Showing 1 to 25 of 1234 endpoints")

### 3. Sortierung

- **Sortierbare Spalten**:
    - Total Requests
    - Successful Requests
    - Failed Requests
    - Average Response Time
- Klick auf Spaltenüberschrift zum Sortieren
- Toggle zwischen aufsteigend (asc) und absteigend (desc)
- Visuelles Feedback durch Pfeile (↑ ↓)
- Standard: Sortierung nach "Total" absteigend (meiste Requests zuerst)

### 4. Performance-Optimierungen

- **Lazy Loading**: Nur 25 Endpoints werden gleichzeitig gerendert
- **Effizientes Sorting**: Sortierung im PHP-Backend vor der Paginierung
- **State Management**: Pagination wird beim Neuladen der Statistiken zurückgesetzt
- **Responsive Design**: Optimierte Navigation für Mobile und Desktop

## Technische Details

### Backend (TheTVDBApiStatistics.php)

#### Neue Properties

```php
public int $endpointsPerPage = 25;  // Anzahl Endpoints pro Seite
public int $currentPage = 1;         // Aktuelle Seite
public string $sortBy = 'total';     // Sortierfeld
public string $sortDirection = 'desc'; // Sortierrichtung
```

#### Neue Methoden

**getPaginatedEndpoints()**

- Sortiert die Endpoints basierend auf gewählter Spalte
- Gibt nur die Endpoints für die aktuelle Seite zurück
- Preserviert die Array-Keys für die Endpoint-Namen

**getTotalPages()**

- Berechnet die Gesamtanzahl der Seiten
- Berücksichtigt die Anzahl der Endpoints und Items pro Seite

**Navigation-Methoden**

- `nextPage()`: Zur nächsten Seite
- `previousPage()`: Zur vorherigen Seite
- `goToPage(int $page)`: Zu einer bestimmten Seite springen

**sortEndpoints(string $column)**

- Wechselt die Sortierrichtung bei erneutem Klick auf die gleiche Spalte
- Setzt die Pagination auf Seite 1 zurück

### Frontend (statistics.blade.php)

#### UI-Verbesserungen

- Anklickbare Spaltenüberschriften mit Hover-Effekt
- Sortier-Indikatoren (Chevron Icons)
- Responsive Pagination-Navigation
- Endpoint-Zähler im Header ("1,234 total endpoints")

#### Wire-Bindings

- `wire:click="sortEndpoints('column')"` für Spalten-Sortierung
- `wire:click="previousPage"` / `nextPage` für Navigation
- `wire:click="goToPage({{ $i }})"` für direkte Seitenauswahl

## Verwendung

### Clear All Logs

1. Klicke auf den roten **"Clear All Logs"** Button in der Header-Leiste
2. Ein Bestätigungsdialog erscheint mit Warnung
3. Bestätige mit **"Yes, delete all logs"**
4. Alle API-Logs werden aus der Datenbank entfernt
5. Eine Erfolgsmeldung zeigt die Anzahl der gelöschten Einträge
6. Die Statistiken werden automatisch neu geladen

### Standard-Ansicht

Beim Laden der Statistikseite werden automatisch:

1. Die Top 25 Endpoints angezeigt (sortiert nach Total Requests)
2. Pagination angezeigt (wenn mehr als 25 Endpoints vorhanden)

### Sortierung

1. Klicke auf eine Spaltenüberschrift (Total, Success, Failed, Avg Response Time)
2. Die Tabelle wird entsprechend sortiert
3. Ein weiterer Klick auf die gleiche Spalte wechselt zwischen aufsteigend/absteigend

### Navigation

- **Desktop**: Klicke auf Seitenzahlen, Previous/Next oder Pfeile
- **Mobile**: Nutze die Previous/Next Buttons
- Die Pagination wird automatisch zurückgesetzt beim:
    - Neuladen der Statistiken (Refresh)
    - Ändern des Zeitraums (7/30/90 Tage)
    - Ändern der Sortierung

## Performance-Verbesserungen

### Vorher

- ❌ Alle Endpoints werden gerendert (bei 1000+ sehr langsam)
- ❌ Keine Sortierung möglich
- ❌ Langes Scrollen erforderlich
- ❌ Hohe Speicherbelastung im Browser

### Nachher

- ✅ Nur 25 Endpoints werden gleichzeitig gerendert
- ✅ Schnelles Laden auch bei 1000+ Endpoints
- ✅ Sortierung nach allen Spalten möglich
- ✅ Einfache Navigation zwischen Seiten
- ✅ Minimale Speicherbelastung

## Anpassungsmöglichkeiten

### Anzahl Endpoints pro Seite ändern

In `TheTVDBApiStatistics.php`:

```php
public int $endpointsPerPage = 50; // z.B. 50 statt 25
```

### Standard-Sortierung ändern

In `TheTVDBApiStatistics.php`:

```php
public string $sortBy = 'avg_response_time'; // z.B. nach Response Time
public string $sortDirection = 'asc';         // aufsteigend
```

### Pagination-Design anpassen

Die Pagination verwendet Filament- und Tailwind-CSS-Klassen und kann in `statistics.blade.php` angepasst werden.

## Kompatibilität

- ✅ Laravel 11.x
- ✅ Filament 3.x
- ✅ Livewire 3.x
- ✅ Responsive (Desktop & Mobile)
- ✅ Dark Mode Support

## Zukünftige Erweiterungen

- Export-Funktion für sortierte/gefilterte Daten
- Suchfunktion für Endpoints
- Anpassbare Items pro Seite (25/50/100)
- CSV-Export der Statistiken


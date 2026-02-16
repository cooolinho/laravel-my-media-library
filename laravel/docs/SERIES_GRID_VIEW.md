# Serien Kachelansicht

## Übersicht

Die Serien-Liste wurde mit einer modernen Kachelansicht erweitert, die eine bessere visuelle Darstellung der Serien mit
ihren Covern bietet.

## Features

### Kachelansicht

- **Seriencover**: Anzeige der Artwork-Thumbnails (Typ 14)
- **Serientitel**: Lesbare Anzeige mit Textbegrenzung auf 2 Zeilen
- **Fortschrittsanzeige**:
    - Prozentuale Anzeige der besessenen Episoden
    - Farbige Fortschrittsleiste am unteren Rand des Covers
    - Grünes Badge für vollständige Serien (100%)
- **Episodenanzahl**: Gesamtanzahl der Episoden pro Serie
- **Hover-Effekte**:
    - Vergrößerung des Covers beim Überfahren
    - Quick-Action-Overlay mit Ansehen/Bearbeiten-Buttons

### Ansichtsmodi

- **Kachelansicht** (Standard): Grid-Layout mit Seriencovern
- **Tabellenansicht**: Klassische Tabellenansicht mit allen Details
- **Toggle-Button**: Einfacher Wechsel zwischen den Ansichten

### Funktionalität

- ✅ **Suche**: Volltextsuche über Seriennamen und TheTVDB-ID
- ✅ **Filter**: Alle Filament-Tabellenfilter funktionieren
- ✅ **Sortierung**: Sortierung nach allen definierten Spalten
- ✅ **Responsive**: Optimiert für verschiedene Bildschirmgrößen
    - 2 Spalten: Mobile
    - 3 Spalten: Small
    - 4 Spalten: Medium
    - 5 Spalten: Large
    - 6 Spalten: XL
    - 7 Spalten: 2XL

## Technische Details

### Dateien

1. **ListSeries.php** (`app/Filament/Resources/Series/Pages/ListSeries.php`)
    - Custom View: `series.list-series-grid`
    - Lädt Beziehungen vor: `artworks`, `episodes`

2. **list-series-grid.blade.php** (`resources/views/series/list-series-grid.blade.php`)
    - Haupt-Layout mit Toggle-Funktion
    - Alpine.js für Ansichtswechsel

3. **series-card.blade.php** (`resources/views/components/series/series-card.blade.php`)
    - Komponente für einzelne Serienkachel
    - Zeigt Cover, Titel, Fortschritt und Actions

### Artwork-Typ

Die Kachelansicht verwendet Artwork vom Typ `14` (Cover/Poster) von TheTVDB.

### Styling

- Tailwind CSS für responsives Design
- Filament-Theme-Kompatibilität (Light/Dark Mode)
- Smooth Transitions und Hover-Effekte

## Verwendung

Die Kachelansicht ist standardmäßig aktiv. Nutzer können:

1. Zwischen Kachel- und Tabellenansicht wechseln
2. Serien über die Suchleiste filtern
3. Auf eine Kachel klicken, um Details anzuzeigen
4. Hover-Actions für schnellen Zugriff nutzen

## Zukünftige Erweiterungen

Mögliche Verbesserungen:

- Sortieroptionen im Grid-Modus
- Filterchips unter der Suchleiste
- Pagination für große Seriensammlungen
- Lazy Loading für bessere Performance
- Bulk-Actions im Grid-Modus


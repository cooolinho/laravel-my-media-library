# Serien-Kachelansicht - Eigenes CSS-Design

## Übersicht

Die Serien-Kachelansicht verwendet nun ein vollständig eigenständiges CSS-Design ohne Tailwind-Klassen. Das Design ist
modern, ansprechend und vollständig anpassbar.

## 🎨 Design-Features

### Visuelle Highlights

1. **Moderne Karten-Animationen**
    - Smooth Hover-Effekte mit `translateY(-4px)`
    - Cover-Zoom beim Hover (Scale 1.08)
    - Staggered Fade-In Animation für Karten
    - Sanfte Transitions mit `cubic-bezier(0.4, 0, 0.2, 1)`

2. **Fortschrittsvisualisierung**
    - 6px hoher Fortschrittsbalken am unteren Rand
    - Animierte Breiten-Transition (0.5s ease)
    - Glow-Effekt mit `box-shadow`
    - Farbcodierte Anzeige (Blau/Grün)

3. **Completion Badge**
    - Grünes, rundes Badge mit Häkchen-Icon
    - Positioned: Top-Right mit Shadow
    - Nur bei 100% Fortschritt sichtbar

4. **Hover-Overlay**
    - Dunkles Overlay (65% Transparenz)
    - Zwei Action-Buttons (Ansehen/Bearbeiten)
    - Scale-Animation bei Button-Hover
    - Smooth Opacity-Transition

5. **Responsive Grid**
   ```
   Mobile:    2 Spalten
   Small:     3 Spalten
   Medium:    4 Spalten
   Large:     5 Spalten
   XL:        6 Spalten
   2XL:       7 Spalten
   ```

## 📁 Dateistruktur

```
resources/css/filament/admin/
├── theme.css                          # Haupt-Theme mit Imports
└── components/
    └── _series.scss                   # Serien-Design (BEM-Struktur)

resources/views/
├── series/
│   └── list-series-grid.blade.php    # Grid-Layout ohne Tailwind
└── components/series/
    └── series-card.blade.php          # Karten-Komponente ohne Tailwind
```

## 🎯 CSS-Klassen (BEM-Struktur)

### View Toggle

```scss
.series-view-toggle // Container
__buttons // Button-Gruppe
__button // Einzelner Button
--active // Aktiver Zustand
__button-text

// Button-Text (responsive)
```

### Grid Layout

```scss
.series-grid // Grid-Container
__empty // Empty-State Container
-icon // Icon-Container
-title // Titel
-description

// Beschreibung
```

### Series Card

```scss
.series-card // Hauptcontainer
__link // Link-Wrapper
__cover // Cover-Container
__cover-image // Cover-Bild
__placeholder // Platzhalter (kein Cover)
-text // Platzhalter-Text
__badge // Completion Badge
__progress // Progress-Container
-fill // Progress-Fill
__info // Info-Section
__title // Serientitel
__meta // Meta-Info Container
__episodes // Episoden-Anzahl
__percentage // Prozent-Anzeige
--complete // 100% Modifier
__overlay // Hover-Overlay
__action

// Action-Button
```

## 🌈 CSS-Variablen

### Anpassbare Werte

```scss
--series-card-radius:

12
px

; // Kartenrundung
--series-card-shadow:

0
2
px

8
px

rgba
(
...)

; // Standard-Schatten
--series-card-shadow-hover:

0
8
px

24
px ...; // Hover-Schatten
--series-transition: all

0.3
s cubic-bezier...

// Transition-Timing
```

### Farben (Light Mode)

```scss
--series-bg: #ffffff

; // Kartenhintergrund
--series-text-primary: #1f2937

; // Haupttext
--series-text-secondary: #6b7280

; // Sekundärtext
--series-border: #e5e7eb

; // Rahmen
--series-progress-fill: #3b82f6

; // Fortschritt
--series-completion-bg: #10b981

; // Completion-Farbe
```

### Farben (Dark Mode)

```scss
--series-bg: #1f2937

; // Dunkler Hintergrund
--series-text-primary: #f9fafb

; // Heller Text
--series-text-secondary: #9ca3af

; // Gedämpfter Text
--series-progress-fill: #60a5fa

; // Hellerer Fortschritt
```

## ✨ Spezielle Features

### 1. Staggered Animation

Jede Karte erscheint mit leichter Verzögerung:

```scss
.series-card:nth-child(n) {
    animation-delay: calc(n * 0.03s);
}
```

### 2. Loading State

Shimmer-Effekt für Ladezustand:

```scss
.series-card--loading {
    animation: shimmer 1.5s infinite;
}
```

### 3. Line Clamp für Titel

Titel werden auf 2 Zeilen begrenzt:

```scss
-webkit-line-clamp:

2
;
-webkit-box-orient: vertical

;
```

### 4. Focus States

Accessibility-optimierte Focus-Indikatoren:

```scss
outline:

2
px solid

var
(
--series-progress-fill

)
;
outline-offset:

2
px

;
```

## 🖨️ Print Styles

Optimiert für Druck:

```scss
@media print {
    .series-card__overlay { display: none; }
    .series-card { 
        break-inside: avoid;
        border: 1px solid #e5e7eb;
    }
}
```

## 🎨 Anpassung des Designs

### Farben ändern

Passe die CSS-Variablen in `_series.scss` an:

```scss
:root {
    --series-progress-fill: #your-color;
    --series-completion-bg: #your-color;
}
```

### Animationen anpassen

```scss
--series-transition: all

0.5
s ease

; // Langsamer
```

### Grid-Spalten ändern

```scss
.series-grid {
    @media (min-width: 1280px) {
        grid-template-columns: repeat(8, 1fr);  // 8 Spalten
    }
}
```

### Kartenrundung ändern

```scss
--series-card-radius:

16
px

; // Mehr Rundung
```

## 🚀 Kompilierung

Nach Änderungen an `_series.scss`:

```bash
cd laravel
npm run build
```

Für Development mit Hot-Reload:

```bash
npm run dev
```

## 📊 Performance-Optimierungen

1. **Lazy Loading** für Bilder
2. **Will-Change** für animierte Properties (automatisch)
3. **Transform** statt Position für Animationen
4. **CSS Containment** für Grid-Elemente

## 🎯 Browser-Kompatibilität

- ✅ Chrome/Edge (Latest)
- ✅ Firefox (Latest)
- ✅ Safari (Latest)
- ✅ Mobile Browser (iOS/Android)

## 📝 Hinweise

- Alle Klassen folgen der BEM-Methodik
- CSS-Variablen ermöglichen einfache Theme-Anpassungen
- Dark Mode wird automatisch unterstützt
- Keine Tailwind-Dependencies mehr nötig
- Vollständig wartbar und erweiterbar

## 🎨 Design-Inspiration

Das Design orientiert sich an:

- Netflix/Prime Video (Kartenansicht)
- Moderne Media-Libraries
- Material Design Prinzipien
- Glassmorphism Trends

---

**Viel Spaß mit deinem individuellen Serien-Design!** 🎉


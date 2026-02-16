# Warez Links - Design Integration

## Übersicht

Die Warez Links wurden erfolgreich in das ViewSeries-Design integriert und befinden sich nun zwischen den Statistiken
und der Episodenliste.

## Design-Features

### 🎨 Visuelles Design

- **Moderne Tab-Navigation**: Horizontale Tabs mit Hover- und Active-States
- **Gradient-Hintergründe**: Konsistent mit dem restlichen Series-Design
- **Icons für jeden Tab**: Unterschiedliche Icons je nach Platzhalter-Typ
    - TheTVDB: Checkmark-Icon
    - TVDB_ID Links: Document-Icon
    - SERIES_NAME Links: Link-Icon

### 📐 Layout

```
┌─────────────────────────────────────────┐
│          Hero Section (Cover)           │
└─────────────────────────────────────────┘
┌─────────────────────────────────────────┐
│         Statistiken (4 Karten)          │
└─────────────────────────────────────────┘
┌─────────────────────────────────────────┐
│        🆕 Externe Links Section         │
│  ┌───────────────────────────────────┐  │
│  │ [TheTVDB] [Link1] [Link2] ...    │  │ ← Tabs
│  └───────────────────────────────────┘  │
│  ┌───────────────────────────────────┐  │
│  │                                   │  │
│  │         iFrame Content            │  │ ← Content
│  │                                   │  │
│  └───────────────────────────────────┘  │
└─────────────────────────────────────────┘
┌─────────────────────────────────────────┐
│         Episoden (nach Staffeln)        │
└─────────────────────────────────────────┘
```

## CSS-Struktur

### Hauptcontainer

```scss
.warez-links-section
└── .warez-tabs
├── .warez-tabs-header
│ └── .warez-tab-button

(
mehrere

)
└── .warez-tabs-content
└── .warez-tab-panel

(
mehrere

)
├── .iframe-info

(
optional

)
└── .warez-iframe
```

### Styling-Eigenschaften

#### Tab-Buttons

- **Normal**: Halbtransparenter Hintergrund, graue Farbe
- **Hover**: Leicht angehoben, hellerer Hintergrund, blaue Border
- **Active**: Gradient-Hintergrund (Lila-Blau), weißer Text, Box-Shadow

#### iFrame

- **Größe**: 100% Breite, 600px Höhe (400px auf Mobile)
- **Style**: Abgerundete Ecken (12px), dunkler Hintergrund
- **Shadow**: Inset-Shadow für Tiefe

#### Info-Bar

- Zeigt Titel und URL des Links
- Icon links, URL rechts (mit Ellipsis bei Overflow)
- Dunkler Hintergrund mit subtiler Border

## Responsive Verhalten

### Desktop (> 768px)

- Tabs horizontal scrollbar
- iFrame 600px hoch
- Info-Bar in einer Zeile

### Mobile (≤ 768px)

- Tabs horizontal scrollbar (kleinere Buttons)
- iFrame 400px hoch
- Info-Bar mehrzeilig
- URL nimmt volle Breite ein

## Alpine.js Integration

Die Tabs nutzen Alpine.js für die Interaktivität:

```html

<div x-data="{ activeTab: 'tvdb' }">
    <!-- Tab wird aktiv wenn activeTab === 'tvdb' -->
    <button @click="activeTab = 'tvdb'" :class="{ 'active': activeTab === 'tvdb' }">

        <!-- Content wird angezeigt wenn activeTab === 'tvdb' -->
        <div x-show="activeTab === 'tvdb'">
```

- **State**: `activeTab` speichert den aktuell aktiven Tab
- **Default**: `'tvdb'` (TheTVDB ist standardmäßig aktiv)
- **Click**: Wechselt den `activeTab` State
- **Class Binding**: Fügt `.active` Klasse hinzu wenn Tab aktiv
- **Show/Hide**: Zeigt nur den Content des aktiven Tabs

## Dateien

### Geänderte Dateien

- ✅ `resources/views/series/view-series-detail.blade.php` - Warez Links Section hinzugefügt
- ✅ `resources/css/filament/admin/pages/_viewSeries.scss` - Styling hinzugefügt

### CSS-Zeilen hinzugefügt

- Warez Links Section: ~150 Zeilen (inkl. Responsive)
- Position: Vor dem "Responsive Design" Block

## Features im Detail

### 1. TheTVDB Tab (Standard)

- Immer als erster Tab vorhanden
- Nutzt Series Slug oder Name
- Standardmäßig aktiv beim Laden

### 2. Dynamische Warez Links

- Werden aus Datenbank geladen
- Tabs werden automatisch generiert
- URLs mit Platzhaltern werden ersetzt

### 3. Icon-Unterscheidung

```blade
@if($link->placeholderType === \App\Models\WarezLink::PLACEHOLDER_TVDB_ID)
    {{-- Document Icon für TVDB_ID --}}
@else
    {{-- Link Icon für SERIES_NAME --}}
@endif
```

### 4. URL-Anzeige

Jeder Tab zeigt die generierte URL in der Info-Bar:

- Titel des Links
- Vollständige URL mit ersetzten Platzhaltern
- Icon zur visuellen Kennzeichnung

## Anpassungen

### Tab-Farben ändern

```scss
.warez-tab-button {
    &.active {
        background: linear-gradient(135deg, #IHRE_FARBE_1 0%, #IHRE_FARBE_2 100%);
    }
}
```

### iFrame-Höhe anpassen

```scss
.warez-iframe {
    height: 800px; // Desktop
}

@media (max-width: 768px) {
    .warez-iframe {
        height: 500px; // Mobile
    }
}
```

### Tab-Layout ändern

```scss
.warez-tabs-header {
    flex-wrap: wrap; // Tabs umbrechen statt scrollen
}
```

## Troubleshooting

### Tabs werden nicht angezeigt

➡️ Überprüfen Sie, dass Alpine.js geladen ist und `x-data` funktioniert

### iFrames laden nicht

➡️ Manche Websites blockieren das Einbetten in iFrames (X-Frame-Options)
➡️ Lösung: Link in neuem Tab öffnen statt iFrame

### Styling funktioniert nicht

➡️ SCSS kompilieren: `npm run build` oder `npm run dev`
➡️ Browser-Cache leeren

## Erweiterungen

### Link in neuem Tab öffnen (optional)

Statt iFrame einen Button hinzufügen:

```blade
<a href="{{ $link->getIframeUrl($record) }}" target="_blank" class="external-link-button">
    In neuem Tab öffnen
</a>
```

### Tab-Icons anpassen

Ersetzen Sie die SVG-Paths mit eigenen Icons oder nutzen Sie Heroicons/FontAwesome

### Lazy Loading für iFrames

```blade
<iframe loading="lazy" src="..." class="warez-iframe"></iframe>
```


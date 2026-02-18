# Warez-Links Logo-Upload Feature

## Übersicht

Die Warez-Links wurden um ein optionales Logo-Upload-Feature erweitert. Jeder Warez-Link kann nun ein Logo haben, das in
der Blade-Komponente angezeigt wird.

## Änderungen

### 1. Storage-Disk

Eine neue Storage-Disk `warez_logos` wurde erstellt:

- **Pfad**: `storage/app/public/warez-logos`
- **URL**: `/storage/warez-logos`
- **Sichtbarkeit**: public

### 2. Datenbank

- Neue Spalte `logo` (nullable) in der Tabelle `warez_links`
- Migration: `2026_02_18_210247_add_logo_to_warez_links_table.php`

### 3. Model

Das `WarezLink` Model wurde erweitert:

- Logo-Konstante hinzugefügt
- Logo zum `$fillable` Array hinzugefügt
- Neue Methode `getLogoUrl()` zum Abrufen der Logo-URL

### 4. Blade-Komponenten

#### Warez-Link-Button

Neue Komponente: `resources/views/components/warez-link-button.blade.php`

**Verwendung:**

```blade
<x-warez-link-button :warezLink="$warezLink" :series="$series" />
```

**Props:**

- `warezLink`: WarezLink Model-Instanz
- `series`: Series Model-Instanz

#### Warez-Links-Section

Neue Komponente: `resources/views/components/warez-links-section.blade.php`

**Verwendung:**

```blade
<x-warez-links-section :series="$series" />
```

**Props:**

- `series`: Series Model-Instanz

Diese Komponente zeigt automatisch alle aktiven Warez-Links in einem ansprechenden Section-Layout an.

### 5. SCSS-Styling

Datei: `resources/css/filament/admin/components/_warez_links.scss`

**CSS-Klassen:**

- `.warez-link-button`: Haupt-Button-Stil
- `.warez-link-button__logo`: Logo-Stil
- `.warez-link-button__title`: Titel-Stil
- `.warez-links-container`: Container für mehrere Links
- `.warez-links-section`: Section-Container mit Hintergrund
- `.warez-links-section__title`: Section-Titel

**Farbvarianten:**

- `.warez-link-button--primary` (Standard)
- `.warez-link-button--success` (Grün)
- `.warez-link-button--danger` (Rot)
- `.warez-link-button--info` (Blau)

### 6. Widget-Integration

Das Warez-Link-Widget (`WarezLinkWidget`) zeigt nun auch die Logos in den Tab-Titeln an.
Die Logos werden automatisch angezeigt, wenn sie vorhanden sind.

## Verwendungsbeispiele

### Section-Komponente (Empfohlen)

Die einfachste Methode, um alle aktiven Warez-Links anzuzeigen:

```blade
<x-warez-links-section :series="$series" />
```

### Einzelner Button

```blade
<x-warez-link-button :warezLink="$warezLink" :series="$series" />
```

### Mehrere Buttons in Container

```blade
<div class="warez-links-container">
    @foreach($warezLinks as $warezLink)
        <x-warez-link-button :warezLink="$warezLink" :series="$series" />
    @endforeach
</div>
```

### Benutzerdefinierte Section

```blade
<section class="warez-links-section">
    <h2 class="warez-links-section__title">Meine Custom Links</h2>
    
    <div class="warez-links-container">
        @foreach($warezLinks as $warezLink)
            <x-warez-link-button :warezLink="$warezLink" :series="$series" />
        @endforeach
    </div>
</section>
```

### Mit Farbvariante

```blade
<a class="warez-link-button warez-link-button--success" href="...">
    ...
</a>
```

## Logo-Upload im Filament Admin

Im Filament Admin Panel kann beim Erstellen/Bearbeiten eines Warez-Links ein Logo hochgeladen werden:

- Das Logo wird im Verzeichnis `storage/app/public/warez-logos` gespeichert
- Die Methode `getLogoUrl()` gibt die vollständige URL zurück
- Wenn kein Logo vorhanden ist, wird nur der Titel angezeigt

## Design-Features

- Gradient-Hintergrund mit Hover-Effekt
- Sanfte Animationen beim Hover
- Responsive Design für mobile Geräte
- Flexibles Layout für Logo + Text
- Unterstützung für Text-Overflow bei langen Titeln


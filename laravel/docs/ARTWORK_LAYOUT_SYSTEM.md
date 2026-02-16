# Artwork-Layout-System - Dokumentation

## Übersicht

Das Artwork-System wurde komplett überarbeitet, um verschiedene Bildformate optimal darzustellen. Jeder Artwork-Typ hat
nun sein eigenes Layout und Aspect Ratio. Das System verwendet das `ArtworkTypeEnum` aus TheTVDB API für type-sichere
Typdefinitionen.

## 🎨 ArtworkHelper-Klasse

Die `App\Helpers\ArtworkHelper` Klasse verwaltet alle Artwork-Typ-Konfigurationen zentral und verwendet
`ArtworkTypeEnum` für die Typdefinition.

### ArtworkTypeEnum Integration

Das System verwendet das `ArtworkTypeEnum` aus der TheTVDB API:

```php
use App\Http\Client\TheTVDB\Api\Enum\ArtworkTypeEnum;

enum ArtworkTypeEnum: int
{
    case SERIES_BANNER = 1;
    case SERIES_POSTER = 2;
    case SERIES_BACKGROUND = 3;
    case SERIES_ICON = 5;
    case SERIES_CINEMAGRAPH = 20;
    case SERIES_CLEARART = 22;
    case SERIES_CLEARLOGO = 23;
    case EPISODE_SCREENCAP_16_9 = 11;
    case EPISODE_SCREENCAP_4_3 = 12;
    case SEASON_BANNER = 6;
    case SEASON_POSTER = 7;
    case SEASON_BACKGROUND = 8;
    case SEASON_ICON = 10;
}
```

### Verfügbare Artwork-Typen und ihre Konfiguration:

#### 1. **Serien-Poster** (ArtworkTypeEnum::SERIES_POSTER)

- **ID**: 2
- **Layout**: Grid (max. 3 Spalten nebeneinander)
- **Aspect Ratio**: 2:3
- **Min. Breite**: 200px
- **Padding-Bottom**: 150%
- **Type Name**: `poster`

#### 2. **Hintergründe** (ArtworkTypeEnum::SERIES_BACKGROUND)

- **ID**: 3
- **Layout**: Stack (untereinander)
- **Aspect Ratio**: 16:9
- **Min. Breite**: 600px
- **Padding-Bottom**: 56.25%
- **Type Name**: `background`

#### 3. **Banner** (ArtworkTypeEnum::SERIES_BANNER)

- **ID**: 1
- **Layout**: Stack (untereinander)
- **Aspect Ratio**: ~7.6:1
- **Min. Breite**: 758px
- **Padding-Bottom**: 13.16%
- **Type Name**: `banner`

#### 4. **Clear Logos** (ArtworkTypeEnum::SERIES_CLEARLOGO)

- **ID**: 23
- **Layout**: Grid (max. 4 Spalten)
- **Aspect Ratio**: Frei (transparent)
- **Min. Breite**: 200px
- **Padding-Bottom**: 50%
- **Type Name**: `clearlogo`

#### 5. **Clear Art** (ArtworkTypeEnum::SERIES_CLEARART)

- **ID**: 22
- **Layout**: Grid (max. 3 Spalten)
- **Aspect Ratio**: 4:3
- **Min. Breite**: 250px
- **Padding-Bottom**: 75%
- **Type Name**: `clearart`

#### 6. **Icons** (ArtworkTypeEnum::SERIES_ICON)

- **ID**: 5
- **Layout**: Grid (max. 4 Spalten)
- **Aspect Ratio**: 1:1
- **Min. Breite**: 150px
- **Padding-Bottom**: 100%
- **Type Name**: `icon`

#### 7. **Cinemagraphs** (ArtworkTypeEnum::SERIES_CINEMAGRAPH)

- **ID**: 20
- **Layout**: Stack (untereinander)
- **Aspect Ratio**: 16:9
- **Min. Breite**: 600px
- **Padding-Bottom**: 56.25%
- **Type Name**: `cinemagraph`

#### 8. **Staffel-Poster** (ArtworkTypeEnum::SEASON_POSTER)

- **ID**: 7
- **Layout**: Grid (max. 3 Spalten)
- **Aspect Ratio**: 2:3
- **Min. Breite**: 200px
- **Padding-Bottom**: 150%
- **Type Name**: `season_poster`

#### 9. **Staffel-Hintergründe** (ArtworkTypeEnum::SEASON_BACKGROUND)

- **ID**: 8
- **Layout**: Stack (untereinander)
- **Aspect Ratio**: 16:9
- **Min. Breite**: 600px
- **Padding-Bottom**: 56.25%
- **Type Name**: `season_background`

#### 10. **Staffel-Banner** (ArtworkTypeEnum::SEASON_BANNER)

- **ID**: 6
- **Layout**: Stack (untereinander)
- **Aspect Ratio**: 16:9
- **Min. Breite**: 758px
- **Padding-Bottom**: 13.16%
- **Type Name**: `season_banner`

#### 11. **Staffel-Icons** (ArtworkTypeEnum::SEASON_ICON)

- **ID**: 10
- **Layout**: Grid (max. 4 Spalten)
- **Aspect Ratio**: 1:1
- **Min. Breite**: 150px
- **Padding-Bottom**: 100%
- **Type Name**: `season_icon`

#### 12. **Episoden-Screenshots 16:9** (ArtworkTypeEnum::EPISODE_SCREENCAP_16_9)

- **ID**: 11
- **Layout**: Grid (max. 3 Spalten)
- **Aspect Ratio**: 16:9
- **Min. Breite**: 300px
- **Padding-Bottom**: 56.25%
- **Type Name**: `episode_16_9`

#### 13. **Episoden-Screenshots 4:3** (ArtworkTypeEnum::EPISODE_SCREENCAP_4_3)

- **ID**: 12
- **Layout**: Grid (max. 3 Spalten)
- **Aspect Ratio**: 4:3
- **Min. Breite**: 300px
- **Padding-Bottom**: 75%
- **Type Name**: `episode_4_3`

## 📁 Template-Struktur

### Zwei Layout-Typen:

#### 1. Grid-Layout (`grid-layout.blade.php`)

Für Artworks, die **nebeneinander** dargestellt werden:

- Poster (max. 3 Spalten)
- ClearLogo (max. 4 Spalten)
- ClearArt (max. 3 Spalten)
- Season (max. 3 Spalten)
- Series (max. 3 Spalten)

**Features:**

- Responsive Grid mit `minmax()`
- Hover-Overlay mit Vollbild-Button
- Dynamisches Padding-Bottom für korrektes Aspect Ratio
- ID-Anzeige unter jedem Bild

#### 2. Stack-Layout (`stack-layout.blade.php`)

Für Artworks, die **untereinander** dargestellt werden:

- Background (16:9)
- Banner (sehr breit)

**Features:**

- Volle Breite pro Bild
- Seitlicher Overlay-Effekt
- Größerer Vollbild-Button
- ID-Anzeige im Overlay

## 🎯 ArtworkHelper Methoden

### Konfiguration abrufen:

```php
use App\Http\Client\TheTVDB\Api\Enum\ArtworkTypeEnum;
use App\Helpers\ArtworkHelper;

// Mit Enum (empfohlen - type-safe)
$config = ArtworkHelper::getTypeConfig(ArtworkTypeEnum::SERIES_POSTER->value);

// Mit Enum direkt
$config = ArtworkHelper::getTypeConfig(2); // SERIES_POSTER hat ID 2

// Mit type_name String (Legacy-Support)
$config = ArtworkHelper::getTypeConfig('poster');

// Einzelne Eigenschaften abrufen
$aspectRatio = ArtworkHelper::getAspectRatio(ArtworkTypeEnum::SERIES_POSTER->value);     // "2:3"
$layout = ArtworkHelper::getLayout(ArtworkTypeEnum::SERIES_BANNER->value);                // "stack"
$maxColumns = ArtworkHelper::getMaxColumns(ArtworkTypeEnum::SERIES_POSTER->value);        // 3
$minWidth = ArtworkHelper::getMinWidth(ArtworkTypeEnum::SERIES_BACKGROUND->value);        // 600
$paddingBottom = ArtworkHelper::getPaddingBottom(ArtworkTypeEnum::SERIES_POSTER->value);  // 150
$displayName = ArtworkHelper::getDisplayName(ArtworkTypeEnum::SERIES_CLEARLOGO->value);   // "Clear Logos"
$typeName = ArtworkHelper::getTypeName(ArtworkTypeEnum::SERIES_POSTER->value);            // "poster"

// Enum zurückbekommen
$enum = ArtworkHelper::getEnum(ArtworkTypeEnum::SERIES_POSTER->value);  
// Returns: ArtworkTypeEnum::SERIES_POSTER
```

### Layout-Prüfung:

```php
// Prüfen ob Stack-Layout (mit Enum value)
$isStack = ArtworkHelper::isStackLayout(ArtworkTypeEnum::SERIES_BANNER->value);  // true
$isStack = ArtworkHelper::isStackLayout(ArtworkTypeEnum::SERIES_POSTER->value);  // false

// Prüfen ob Grid-Layout
$isGrid = ArtworkHelper::isGridLayout(ArtworkTypeEnum::SERIES_POSTER->value);    // true
$isGrid = ArtworkHelper::isGridLayout(ArtworkTypeEnum::SERIES_BANNER->value);    // false

// Mit type_name String
$isStack = ArtworkHelper::isStackLayout('banner');  // true
$isGrid = ArtworkHelper::isGridLayout('poster');    // true
```

### CSS-Klassen generieren:

```php
// Grid-CSS-Klasse generieren
$gridClass = ArtworkHelper::getGridClass(ArtworkTypeEnum::SERIES_POSTER->value);  
// Returns: "artwork-grid-columns-3"

$stackClass = ArtworkHelper::getGridClass(ArtworkTypeEnum::SERIES_BANNER->value); 
// Returns: "artwork-grid-stack"
```

### Konvertierungen:

```php
// Type Name zu Enum Value
$enumValue = ArtworkHelper::typeNameToEnumValue('poster');  
// Returns: 2 (ArtworkTypeEnum::SERIES_POSTER->value)

// Enum Value zu Type Name
$typeName = ArtworkHelper::enumValueToTypeName(2);  
// Returns: "poster"

// Alle verfügbaren Typen
$allTypes = ArtworkHelper::getAllTypes();
// Returns: [2, 3, 1, 23, 22, 5, 20, 7, 8, 6, 10, 11, 12]

// Alle verfügbaren Enums
$allEnums = ArtworkHelper::getAllEnums();
// Returns: [ArtworkTypeEnum::SERIES_POSTER, ArtworkTypeEnum::SERIES_BACKGROUND, ...]
```

## 🎨 CSS-Klassen

### Grid-Layout:

- `.artworks-grid` - Container
- `.artwork-grid-columns-3` - 3 Spalten maximal
- `.artwork-grid-columns-4` - 4 Spalten maximal
- `.artwork-item` - Einzelnes Item
- `.artwork-image-container` - Bild-Container mit Aspect Ratio
- `.artwork-thumbnail` - Das Bild selbst
- `.artwork-overlay` - Hover-Overlay
- `.artwork-view-button` - Vollbild-Button

### Stack-Layout:

- `.artworks-stack` - Container
- `.artwork-item-stack` - Einzelnes Item
- `.artwork-image-container-stack` - Bild-Container
- `.artwork-thumbnail-stack` - Das Bild selbst
- `.artwork-overlay-stack` - Seitlicher Overlay
- `.artwork-stack-info` - Info-Bereich im Overlay
- `.artwork-view-button-stack` - Größerer Vollbild-Button

## 📱 Responsive Verhalten

### Desktop (1200px+):

- Poster: 3 Spalten nebeneinander
- ClearLogo: 4 Spalten nebeneinander
- ClearArt: 3 Spalten nebeneinander
- Banner/Background: Volle Breite untereinander

### Tablet (768px - 1199px):

- Poster: 2-3 Spalten (flexibel)
- ClearLogo: 2-3 Spalten (flexibel)
- ClearArt: 2 Spalten (flexibel)
- Banner/Background: Volle Breite untereinander

### Mobile (<768px):

- Alle Grid-Layouts: 1 Spalte
- Stack-Layouts: Volle Breite untereinander
- Kleinere Buttons und Paddings

## 🔧 Verwendung im Template

```blade
{{-- In artworks-section.blade.php --}}
@foreach($artworksByType as $type => $artworks)
    {{-- $type ist hier der Enum value (int) --}}
    @if(\App\Helpers\ArtworkHelper::isStackLayout($type))
        @include('series.components.artworks.stack-layout', [
            'type' => $type, 
            'artworks' => $artworks
        ])
    @else
        @include('series.components.artworks.grid-layout', [
            'type' => $type, 
            'artworks' => $artworks
        ])
    @endif
@endforeach
```

### In den Layout-Templates:

```blade
{{-- grid-layout.blade.php --}}
@php
    use App\Helpers\ArtworkHelper;
    
    $config = ArtworkHelper::getTypeConfig($type);
    $gridClass = ArtworkHelper::getGridClass($type);
    $paddingBottom = ArtworkHelper::getPaddingBottom($type);
    $displayName = ArtworkHelper::getDisplayName($type);
    $typeName = ArtworkHelper::getTypeName($type);
@endphp

<div class="artworks-grid {{ $gridClass }} artwork-type-{{ $typeName }}">
    @foreach($artworks as $artwork)
        <div class="artwork-item">
            <div class="artwork-image-container" 
                 style="padding-bottom: {{ $paddingBottom }}%;">
                {{-- Artwork anzeigen --}}
            </div>
        </div>
    @endforeach
</div>
```

## ✨ Besondere Features

### 1. **Dynamisches Aspect Ratio**

Das Padding-Bottom wird inline gesetzt, basierend auf dem Typ:

```blade
<div class="artwork-image-container" 
     style="padding-bottom: {{ $paddingBottom }}%;">
```

### 2. **ClearLogo Spezial-Handling**

Transparente Logos werden mit `object-fit: contain` dargestellt:

```scss
.artwork-type-clearlogo .artwork-thumbnail {
    object-fit: contain;
    padding: 1rem;
}
```

### 3. **Unterschiedliche Hover-Effekte**

- **Grid**: Overlay von unten nach oben, zentrierter Button
- **Stack**: Overlay von links nach rechts, linksbündiger Button

### 4. **Maximale Spaltenanzahl**

Grid-Layouts respektieren die maximale Spaltenanzahl auch bei großen Screens:

```scss
@media (min-width: 1200px) {
    .artwork-grid-columns-3 {
        grid-template-columns: repeat(3, 1fr);
    }
}
```

## 📊 Beispiel-Konfiguration

### Neuen Artwork-Typ hinzufügen:

Zuerst muss der Typ im `ArtworkTypeEnum` hinzugefügt werden:

```php
// In ArtworkTypeEnum.php
enum ArtworkTypeEnum: int
{
    // ...existing cases...
    case CUSTOM_WIDE = 30;  // Neue ID vergeben
}
```

Dann in `ArtworkHelper.php` die Konfiguration hinzufügen:

```php
// In ArtworkHelper.php - protected static array $typeConfig
ArtworkTypeEnum::CUSTOM_WIDE->value => [
    'enum' => ArtworkTypeEnum::CUSTOM_WIDE,
    'aspect_ratio' => '21:9',
    'layout' => self::LAYOUT_STACK,
    'max_columns' => 1,
    'min_width' => 800,
    'padding_bottom' => 42.85, // 21:9 = 42.85%
    'display_name' => 'Ultra-Wide Artworks',
    'type_name' => 'custom_wide',
],
```

### Verwendung des neuen Typs:

```php
use App\Http\Client\TheTVDB\Api\Enum\ArtworkTypeEnum;
use App\Helpers\ArtworkHelper;

// Typ-sichere Verwendung
$config = ArtworkHelper::getTypeConfig(ArtworkTypeEnum::CUSTOM_WIDE->value);
$isStack = ArtworkHelper::isStackLayout(ArtworkTypeEnum::CUSTOM_WIDE->value);
```

## 🎯 Vorteile

1. ✅ **Korrektes Aspect Ratio** für jeden Typ
2. ✅ **Optimale Darstellung** (Banner untereinander, Poster nebeneinander)
3. ✅ **Zentrale Konfiguration** in einer Helper-Klasse
4. ✅ **Einfache Erweiterbarkeit** für neue Typen
5. ✅ **Responsive** für alle Geräte
6. ✅ **Konsistentes Design** über alle Artwork-Typen
7. ✅ **Performance-optimiert** mit Lazy Loading

## 🚀 Testing

Um die Änderungen zu testen:

```bash
# 1. CSS kompilieren
npm run build

# 2. Cache leeren
php artisan view:clear

# 3. Seite neu laden und verschiedene Artwork-Typen ansehen
```

Achten Sie besonders auf:

- Banner sollten untereinander in voller Breite erscheinen
- Poster sollten maximal 3 nebeneinander erscheinen
- ClearLogos sollten transparent dargestellt werden
- Alle Bilder sollten ihr korrektes Aspect Ratio behalten


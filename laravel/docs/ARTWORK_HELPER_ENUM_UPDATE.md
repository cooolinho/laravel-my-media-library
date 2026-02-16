# ArtworkHelper Update - Verwendung von ArtworkTypeEnum

## ✅ Erfolgreich aktualisiert!

Der `ArtworkHelper` wurde komplett überarbeitet, um das `ArtworkTypeEnum` aus der TheTVDB API zu verwenden.

## 🔄 Hauptänderungen:

### 1. **Import des ArtworkTypeEnum**

```php
use App\Http\Client\TheTVDB\Api\Enum\ArtworkTypeEnum;
```

### 2. **Typ-sichere Konfiguration**

Alle Artwork-Typen verwenden jetzt Enum-Werte als Keys:

```php
protected static array $typeConfig = [
    ArtworkTypeEnum::SERIES_POSTER->value => [
        'enum' => ArtworkTypeEnum::SERIES_POSTER,
        'aspect_ratio' => self::ASPECT_RATIO_2_3,
        'layout' => self::LAYOUT_GRID,
        'max_columns' => 3,
        'min_width' => 200,
        'padding_bottom' => 150,
        'display_name' => 'Serien-Poster',
        'type_name' => 'poster',
    ],
    // ... weitere Typen
];
```

### 3. **Vollständige Enum-Abdeckung**

Alle 13 TheTVDB Artwork-Typen sind jetzt konfiguriert:

#### Series Artworks:

- ✅ `SERIES_POSTER` (ID: 2)
- ✅ `SERIES_BACKGROUND` (ID: 3)
- ✅ `SERIES_BANNER` (ID: 1)
- ✅ `SERIES_CLEARLOGO` (ID: 23)
- ✅ `SERIES_CLEARART` (ID: 22)
- ✅ `SERIES_ICON` (ID: 5)
- ✅ `SERIES_CINEMAGRAPH` (ID: 20)

#### Season Artworks:

- ✅ `SEASON_POSTER` (ID: 7)
- ✅ `SEASON_BACKGROUND` (ID: 8)
- ✅ `SEASON_BANNER` (ID: 6)
- ✅ `SEASON_ICON` (ID: 10)

#### Episode Artworks:

- ✅ `EPISODE_SCREENCAP_16_9` (ID: 11)
- ✅ `EPISODE_SCREENCAP_4_3` (ID: 12)

### 4. **Flexible Methodensignaturen**

Alle Methoden akzeptieren jetzt sowohl `int` (Enum value) als auch `string` (type_name):

```php
public static function getTypeConfig(int|string $type): array
public static function getAspectRatio(int|string $type): string
public static function getLayout(int|string $type): string
// ... usw.
```

### 5. **Neue Hilfsmethoden**

#### `getEnum()` - Gibt das Enum-Case zurück:

```php
$enum = ArtworkHelper::getEnum(2);
// Returns: ArtworkTypeEnum::SERIES_POSTER
```

#### `getTypeName()` - Gibt den CSS-Type-Namen zurück:

```php
$typeName = ArtworkHelper::getTypeName(2);
// Returns: "poster"
```

#### `getAllEnums()` - Gibt alle Enum-Cases zurück:

```php
$enums = ArtworkHelper::getAllEnums();
// Returns: [ArtworkTypeEnum::SERIES_POSTER, ArtworkTypeEnum::SERIES_BACKGROUND, ...]
```

#### `typeNameToEnumValue()` - Konvertiert type_name zu Enum value:

```php
$enumValue = ArtworkHelper::typeNameToEnumValue('poster');
// Returns: 2
```

#### `enumValueToTypeName()` - Konvertiert Enum value zu type_name:

```php
$typeName = ArtworkHelper::enumValueToTypeName(2);
// Returns: "poster"
```

## 📖 Verwendungsbeispiele:

### Mit Enum (empfohlen - type-safe):

```php
use App\Http\Client\TheTVDB\Api\Enum\ArtworkTypeEnum;
use App\Helpers\ArtworkHelper;

// Konfiguration abrufen
$config = ArtworkHelper::getTypeConfig(ArtworkTypeEnum::SERIES_POSTER->value);
$displayName = ArtworkHelper::getDisplayName(ArtworkTypeEnum::SERIES_POSTER->value);
$isStack = ArtworkHelper::isStackLayout(ArtworkTypeEnum::SERIES_BANNER->value);

// Enum zurückbekommen
$enum = ArtworkHelper::getEnum(ArtworkTypeEnum::SERIES_POSTER->value);
```

### Mit Integer (Enum value):

```php
// Direkt mit Enum value
$config = ArtworkHelper::getTypeConfig(2); // SERIES_POSTER
$displayName = ArtworkHelper::getDisplayName(3); // SERIES_BACKGROUND
$isStack = ArtworkHelper::isStackLayout(1); // SERIES_BANNER
```

### Mit String (Legacy-Support):

```php
// Mit type_name String (für Blade-Templates)
$config = ArtworkHelper::getTypeConfig('poster');
$displayName = ArtworkHelper::getDisplayName('background');
$isStack = ArtworkHelper::isStackLayout('banner');
```

## 🎯 Vorteile:

1. ✅ **Type-Safety**: Verwendung von PHP Enums verhindert Tippfehler
2. ✅ **API-Konformität**: Direkte Mapping zu TheTVDB API IDs
3. ✅ **Vollständigkeit**: Alle 13 TheTVDB Artwork-Typen abgedeckt
4. ✅ **Flexibilität**: Unterstützt int, string und Enum-Cases
5. ✅ **Backward-Compatible**: Alte string-basierte Aufrufe funktionieren weiter
6. ✅ **Erweiterbar**: Neue Typen können einfach hinzugefügt werden
7. ✅ **Dokumentiert**: Jeder Typ hat klare Konfiguration

## 🔧 Template-Kompatibilität:

Die bestehenden Blade-Templates funktionieren weiterhin ohne Änderungen:

```blade
{{-- Funktioniert wie vorher --}}
@php
    $config = \App\Helpers\ArtworkHelper::getTypeConfig($type);
    $isStack = \App\Helpers\ArtworkHelper::isStackLayout($type);
@endphp
```

Der `$type` kann dabei sein:

- Ein Enum value (int): `2`, `3`, `1`, etc.
- Ein type_name (string): `'poster'`, `'background'`, `'banner'`, etc.

## 📊 Mapping-Tabelle:

| Enum Case                | ID | Type Name           | Display Name                | Layout   |
|--------------------------|----|---------------------|-----------------------------|----------|
| `SERIES_POSTER`          | 2  | `poster`            | Serien-Poster               | Grid (3) |
| `SERIES_BACKGROUND`      | 3  | `background`        | Hintergründe                | Stack    |
| `SERIES_BANNER`          | 1  | `banner`            | Banner                      | Stack    |
| `SERIES_CLEARLOGO`       | 23 | `clearlogo`         | Clear Logos                 | Grid (4) |
| `SERIES_CLEARART`        | 22 | `clearart`          | Clear Art                   | Grid (3) |
| `SERIES_ICON`            | 5  | `icon`              | Icons                       | Grid (4) |
| `SERIES_CINEMAGRAPH`     | 20 | `cinemagraph`       | Cinemagraphs                | Stack    |
| `SEASON_POSTER`          | 7  | `season_poster`     | Staffel-Poster              | Grid (3) |
| `SEASON_BACKGROUND`      | 8  | `season_background` | Staffel-Hintergründe        | Stack    |
| `SEASON_BANNER`          | 6  | `season_banner`     | Staffel-Banner              | Stack    |
| `SEASON_ICON`            | 10 | `season_icon`       | Staffel-Icons               | Grid (4) |
| `EPISODE_SCREENCAP_16_9` | 11 | `episode_16_9`      | Episoden-Screenshots (16:9) | Grid (3) |
| `EPISODE_SCREENCAP_4_3`  | 12 | `episode_4_3`       | Episoden-Screenshots (4:3)  | Grid (3) |

## ✨ Ergebnis:

Der `ArtworkHelper` ist jetzt:

- ✅ Type-safe mit PHP Enums
- ✅ Vollständig kompatibel mit TheTVDB API
- ✅ Backward-compatible mit bestehenden Templates
- ✅ Flexibel in der Verwendung (int, string, Enum)
- ✅ Vollständig dokumentiert
- ✅ Einfach erweiterbar

Alle 13 TheTVDB Artwork-Typen sind konfiguriert und einsatzbereit! 🎉


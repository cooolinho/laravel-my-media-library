<?php

namespace App\Helpers;

use App\Http\Client\TheTVDB\Api\Enum\ArtworkTypeEnum;

class ArtworkHelper
{
    // Aspect Ratio Konstanten
    public const ASPECT_RATIO_2_3 = '2:3';      // Poster
    public const ASPECT_RATIO_16_9 = '16:9';    // Background, Banner
    public const ASPECT_RATIO_4_3 = '4:3';      // ClearArt
    public const ASPECT_RATIO_FREE = 'free';    // ClearLogo (transparente Logos)
    public const ASPECT_RATIO_1_1 = '1:1';      // Quadratisch

    // Layout Konstanten
    public const LAYOUT_GRID = 'grid';          // Mehrere nebeneinander
    public const LAYOUT_STACK = 'stack';        // Untereinander
    public const LAYOUT_FLEXIBLE = 'flexible';  // Flexibel anpassbar

    /**
     * Mapping von Artwork-Typen (Enum) zu ihren Eigenschaften
     */
    protected static array $typeConfig = [
        // Series Artworks
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
        ArtworkTypeEnum::SERIES_BACKGROUND->value => [
            'enum' => ArtworkTypeEnum::SERIES_BACKGROUND,
            'aspect_ratio' => self::ASPECT_RATIO_16_9,
            'layout' => self::LAYOUT_STACK,
            'max_columns' => 1,
            'min_width' => 600,
            'padding_bottom' => 56.25,
            'display_name' => 'Hintergründe',
            'type_name' => 'background',
        ],
        ArtworkTypeEnum::SERIES_BANNER->value => [
            'enum' => ArtworkTypeEnum::SERIES_BANNER,
            'aspect_ratio' => self::ASPECT_RATIO_16_9,
            'layout' => self::LAYOUT_STACK,
            'max_columns' => 1,
            'min_width' => 758,
            'padding_bottom' => 13.16,
            'display_name' => 'Banner',
            'type_name' => 'banner',
        ],
        ArtworkTypeEnum::SERIES_CLEARLOGO->value => [
            'enum' => ArtworkTypeEnum::SERIES_CLEARLOGO,
            'aspect_ratio' => self::ASPECT_RATIO_FREE,
            'layout' => self::LAYOUT_GRID,
            'max_columns' => 4,
            'min_width' => 200,
            'padding_bottom' => 50,
            'display_name' => 'Clear Logos',
            'type_name' => 'clearlogo',
        ],
        ArtworkTypeEnum::SERIES_CLEARART->value => [
            'enum' => ArtworkTypeEnum::SERIES_CLEARART,
            'aspect_ratio' => self::ASPECT_RATIO_4_3,
            'layout' => self::LAYOUT_GRID,
            'max_columns' => 3,
            'min_width' => 250,
            'padding_bottom' => 75,
            'display_name' => 'Clear Art',
            'type_name' => 'clearart',
        ],
        ArtworkTypeEnum::SERIES_ICON->value => [
            'enum' => ArtworkTypeEnum::SERIES_ICON,
            'aspect_ratio' => self::ASPECT_RATIO_1_1,
            'layout' => self::LAYOUT_GRID,
            'max_columns' => 4,
            'min_width' => 150,
            'padding_bottom' => 100,
            'display_name' => 'Icons',
            'type_name' => 'icon',
        ],
        ArtworkTypeEnum::SERIES_CINEMAGRAPH->value => [
            'enum' => ArtworkTypeEnum::SERIES_CINEMAGRAPH,
            'aspect_ratio' => self::ASPECT_RATIO_16_9,
            'layout' => self::LAYOUT_STACK,
            'max_columns' => 1,
            'min_width' => 600,
            'padding_bottom' => 56.25,
            'display_name' => 'Cinemagraphs',
            'type_name' => 'cinemagraph',
        ],

        // Season Artworks
        ArtworkTypeEnum::SEASON_POSTER->value => [
            'enum' => ArtworkTypeEnum::SEASON_POSTER,
            'aspect_ratio' => self::ASPECT_RATIO_2_3,
            'layout' => self::LAYOUT_GRID,
            'max_columns' => 3,
            'min_width' => 200,
            'padding_bottom' => 150,
            'display_name' => 'Staffel-Poster',
            'type_name' => 'season_poster',
        ],
        ArtworkTypeEnum::SEASON_BACKGROUND->value => [
            'enum' => ArtworkTypeEnum::SEASON_BACKGROUND,
            'aspect_ratio' => self::ASPECT_RATIO_16_9,
            'layout' => self::LAYOUT_STACK,
            'max_columns' => 1,
            'min_width' => 600,
            'padding_bottom' => 56.25,
            'display_name' => 'Staffel-Hintergründe',
            'type_name' => 'season_background',
        ],
        ArtworkTypeEnum::SEASON_BANNER->value => [
            'enum' => ArtworkTypeEnum::SEASON_BANNER,
            'aspect_ratio' => self::ASPECT_RATIO_16_9,
            'layout' => self::LAYOUT_STACK,
            'max_columns' => 1,
            'min_width' => 758,
            'padding_bottom' => 13.16,
            'display_name' => 'Staffel-Banner',
            'type_name' => 'season_banner',
        ],
        ArtworkTypeEnum::SEASON_ICON->value => [
            'enum' => ArtworkTypeEnum::SEASON_ICON,
            'aspect_ratio' => self::ASPECT_RATIO_1_1,
            'layout' => self::LAYOUT_GRID,
            'max_columns' => 4,
            'min_width' => 150,
            'padding_bottom' => 100,
            'display_name' => 'Staffel-Icons',
            'type_name' => 'season_icon',
        ],

        // Episode Artworks
        ArtworkTypeEnum::EPISODE_SCREENCAP_16_9->value => [
            'enum' => ArtworkTypeEnum::EPISODE_SCREENCAP_16_9,
            'aspect_ratio' => self::ASPECT_RATIO_16_9,
            'layout' => self::LAYOUT_GRID,
            'max_columns' => 3,
            'min_width' => 300,
            'padding_bottom' => 56.25,
            'display_name' => 'Episoden-Screenshots (16:9)',
            'type_name' => 'episode_16_9',
        ],
        ArtworkTypeEnum::EPISODE_SCREENCAP_4_3->value => [
            'enum' => ArtworkTypeEnum::EPISODE_SCREENCAP_4_3,
            'aspect_ratio' => self::ASPECT_RATIO_4_3,
            'layout' => self::LAYOUT_GRID,
            'max_columns' => 3,
            'min_width' => 300,
            'padding_bottom' => 75,
            'display_name' => 'Episoden-Screenshots (4:3)',
            'type_name' => 'episode_4_3',
        ],
    ];

    /**
     * Gibt das Aspect Ratio für einen Typ zurück
     */
    public static function getAspectRatio(int|string $type): string
    {
        $config = self::getTypeConfig($type);
        return $config['aspect_ratio'];
    }

    /**
     * Gibt die Konfiguration für einen Artwork-Typ zurück
     *
     * @param int|string $type Enum value (int) oder type_name (string)
     */
    public static function getTypeConfig(int|string $type): array
    {
        // Wenn String übergeben wird, finde den passenden Enum-Wert
        if (is_string($type)) {
            foreach (self::$typeConfig as $enumValue => $config) {
                if ($config['type_name'] === $type) {
                    return $config;
                }
            }
            // Fallback zu SERIES_POSTER
            return self::$typeConfig[ArtworkTypeEnum::SERIES_POSTER->value];
        }

        return self::$typeConfig[$type] ?? self::$typeConfig[ArtworkTypeEnum::SERIES_POSTER->value];
    }

    /**
     * Gibt die minimale Breite für ein Item zurück
     */
    public static function getMinWidth(int|string $type): int
    {
        $config = self::getTypeConfig($type);
        return $config['min_width'];
    }

    /**
     * Gibt das Padding-Bottom für CSS zurück (für Aspect Ratio)
     */
    public static function getPaddingBottom(int|string $type): float
    {
        $config = self::getTypeConfig($type);
        return $config['padding_bottom'];
    }

    /**
     * Gibt den Display-Namen für einen Typ zurück
     */
    public static function getDisplayName(int|string $type): string
    {
        $config = self::getTypeConfig($type);
        return $config['display_name'];
    }

    /**
     * Gibt den Type-Namen für einen Typ zurück (für CSS-Klassen)
     */
    public static function getTypeName(int|string $type): string
    {
        $config = self::getTypeConfig($type);
        return $config['type_name'];
    }

    /**
     * Gibt das Enum-Case für einen Typ zurück
     */
    public static function getEnum(int|string $type): ArtworkTypeEnum
    {
        $config = self::getTypeConfig($type);
        return $config['enum'];
    }

    /**
     * Gibt die Grid-CSS-Klasse für einen Typ zurück
     */
    public static function getGridClass(int|string $type): string
    {
        $layout = self::getLayout($type);
        $maxColumns = self::getMaxColumns($type);

        return match ($layout) {
            self::LAYOUT_STACK => 'artwork-grid-stack',
            self::LAYOUT_GRID => "artwork-grid-columns-{$maxColumns}",
            self::LAYOUT_FLEXIBLE => 'artwork-grid-flexible',
            default => 'artwork-grid-default',
        };
    }

    /**
     * Gibt das Layout für einen Typ zurück
     */
    public static function getLayout(int|string $type): string
    {
        $config = self::getTypeConfig($type);
        return $config['layout'];
    }

    /**
     * Gibt die maximale Anzahl an Spalten zurück
     */
    public static function getMaxColumns(int|string $type): int
    {
        $config = self::getTypeConfig($type);
        return $config['max_columns'];
    }

    /**
     * Gibt alle verfügbaren Artwork-Typen zurück
     */
    public static function getAllTypes(): array
    {
        return array_keys(self::$typeConfig);
    }

    /**
     * Gibt alle verfügbaren Artwork-Enums zurück
     */
    public static function getAllEnums(): array
    {
        return array_map(fn($config) => $config['enum'], self::$typeConfig);
    }

    /**
     * Prüft ob ein Typ ein Stack-Layout hat (untereinander)
     */
    public static function isStackLayout(int|string $type): bool
    {
        return self::getLayout($type) === self::LAYOUT_STACK;
    }

    /**
     * Prüft ob ein Typ ein Grid-Layout hat (nebeneinander)
     */
    public static function isGridLayout(int|string $type): bool
    {
        return self::getLayout($type) === self::LAYOUT_GRID;
    }

    /**
     * Konvertiert einen type_name zu einem Enum-Wert
     */
    public static function typeNameToEnumValue(string $typeName): ?int
    {
        foreach (self::$typeConfig as $enumValue => $config) {
            if ($config['type_name'] === $typeName) {
                return $enumValue;
            }
        }
        return null;
    }

    /**
     * Konvertiert einen Enum-Wert zu einem type_name
     */
    public static function enumValueToTypeName(int $enumValue): ?string
    {
        $config = self::$typeConfig[$enumValue] ?? null;
        return $config['type_name'] ?? null;
    }
}



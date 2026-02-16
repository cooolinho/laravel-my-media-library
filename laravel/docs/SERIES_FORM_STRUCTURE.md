# Series Form - Strukturierte Formular-Ansicht

## Übersicht

Die SeriesForm wurde komplett überarbeitet und in sinnvolle Sections strukturiert, um eine bessere Übersicht und
Benutzerfreundlichkeit zu bieten - analog zur EpisodeForm.

## Neue Struktur

### 1. Section: Basis-Informationen

**Icon:** `heroicon-o-information-circle`  
**Beschreibung:** Grundlegende Serien-Daten  
**Status:** Immer geöffnet  
**Relation:** Keine (direkt auf Series-Model)

**Felder:**

- **Serienname** (TextInput)
    - Maximal 255 Zeichen
    - Pflichtfeld
    - 2 Spalten (volle Breite)
    - Mit Platzhalter und Hilfetext

- **TheTVDB ID** (TextInput)
    - Numerisch
    - Pflichtfeld
    - 2 Spalten (volle Breite)
    - Mit Hilfetext

### 2. Section: Serien-Details

**Icon:** `heroicon-o-film`  
**Beschreibung:** Detaillierte Informationen zur Serie (TheTVDB-Daten)  
**Status:** Immer geöffnet  
**Relation:** `Series::has_one_data` (SeriesData)

**Felder:**

- **Titel (übersetzt)** (TextInput)
    - Maximal 255 Zeichen
    - 2 Spalten
    - Mit Platzhalter und Hilfetext
    - Nutzt TranslatableTrait

- **Beschreibung** (Textarea)
    - 5 Zeilen
    - 2 Spalten
    - Mit Platzhalter und Hilfetext

- **Slug** (TextInput)
    - Maximal 255 Zeichen
    - 2 Spalten
    - URL-freundlicher Identifier

- **Poster-URL** (TextInput)
    - URL-Validierung
    - Maximal 500 Zeichen
    - 2 Spalten
    - Mit Platzhalter und Hilfetext

### 3. Section: Ausstrahlungsdaten

**Icon:** `heroicon-o-calendar`  
**Beschreibung:** Zeitliche Informationen zur Serie  
**Status:** Immer geöffnet  
**Relation:** `Series::has_one_data` (SeriesData)  
**Layout:** 3 Spalten

**Felder:**

- **Erste Ausstrahlung** (DatePicker)
    - Format: d.m.Y
    - Nicht-nativ (Custom Picker)
    - 1 Spalte
    - Mit Hilfetext

- **Letzte Ausstrahlung** (DatePicker)
    - Format: d.m.Y
    - Nicht-nativ
    - 1 Spalte
    - Mit Hilfetext

- **Nächste Ausstrahlung** (DatePicker)
    - Format: d.m.Y
    - Nicht-nativ
    - 1 Spalte
    - Mit Hilfetext

### 4. Section: Status & Bewertung

**Icon:** `heroicon-o-star`  
**Beschreibung:** Status und Bewertungsinformationen  
**Status:** Immer geöffnet  
**Relation:** `Series::has_one_data` (SeriesData)  
**Layout:** 2 Spalten

**Felder:**

- **Status** (Select)
    - Optionen:
        - Continuing (Läuft)
        - Ended (Beendet)
        - Upcoming (Bevorstehend)
        - Pilot
    - 1 Spalte
    - Mit Hilfetext

- **Bewertung** (TextInput)
    - Numerisch (0-100)
    - Mit Suffix "/100"
    - 1 Spalte
    - Mit Platzhalter und Hilfetext

- **Jahr** (TextInput)
    - Numerisch (1900-2100)
    - 1 Spalte
    - Mit Platzhalter und Hilfetext

- **Durchschnittliche Laufzeit** (TextInput)
    - Numerisch (min: 1)
    - Mit Suffix "min"
    - 1 Spalte
    - Mit Platzhalter und Hilfetext

### 5. Section: Herkunft & Sprache

**Icon:** `heroicon-o-globe-alt`  
**Beschreibung:** Ursprungsland und Sprachinformationen  
**Status:** Standardmäßig eingeklappt (collapsed)  
**Relation:** `Series::has_one_data` (SeriesData)  
**Layout:** 2 Spalten

**Felder:**

- **Ursprungsland** (TextInput)
    - Maximal 100 Zeichen
    - 1 Spalte
    - Mit Platzhalter und Hilfetext

- **Originalsprache** (TextInput)
    - Maximal 100 Zeichen
    - 1 Spalte
    - Mit Platzhalter und Hilfetext

### 6. Section: Erweiterte Einstellungen

**Icon:** `heroicon-o-cog-6-tooth`  
**Beschreibung:** Zusätzliche technische Informationen  
**Status:** Standardmäßig eingeklappt (collapsed)  
**Relation:** `Series::has_one_data` (SeriesData)  
**Layout:** 2 Spalten

**Felder:**

- **Standard-Staffeltyp** (TextInput)
    - Numerisch
    - 1 Spalte
    - Mit Platzhalter und Hilfetext
    - TheTVDB Season Type ID

- **Reihenfolge randomisiert** (Toggle)
    - Boolean
    - 1 Spalte
    - Mit Hilfetext

- **Zuletzt aktualisiert** (DatePicker)
    - Format: d.m.Y H:i
    - Nicht-nativ
    - 2 Spalten
    - Mit Hilfetext

## Vorteile der neuen Struktur

### 1. Übersichtlichkeit

✅ **6 klar strukturierte Sections** für verschiedene Datentypen  
✅ Basis-Daten, Details, Zeitdaten, Status, Herkunft und Erweiterte Settings getrennt  
✅ Icons helfen bei der visuellen Orientierung  
✅ Beschreibungen erklären jeden Bereich

### 2. Benutzerfreundlichkeit

✅ Wichtigste Sections (1-4) sind immer sichtbar  
✅ Weniger wichtige Sections (5-6) sind eingeklappt  
✅ Hilfetext bei allen wichtigen Feldern  
✅ Platzhalter zeigen Beispielwerte  
✅ Status-Select mit deutschen Übersetzungen

### 3. Datenintegrität

✅ Validierungen auf Feldebene (numeric, minValue, maxValue, URL)  
✅ Required-Flags für Pflichtfelder  
✅ Beziehung zu SeriesData wird korrekt gehandhabt  
✅ Maximal-Längen für Text-Felder

### 4. Flexibilität

✅ Alle SeriesData-Felder sind editierbar  
✅ Layout passt sich an Bildschirmgröße an  
✅ Sections können ein-/ausgeklappt werden

## Bearbeitbare Daten

### Series-Model (Haupt-Tabelle)

- ✅ Serienname (name)
- ✅ TheTVDB ID (theTvDbId)

### SeriesData-Model (Relation-Tabelle)

- ✅ Titel übersetzt (name) - über TranslatableTrait
- ✅ Beschreibung (overview)
- ✅ Slug (slug)
- ✅ Poster-URL (image)
- ✅ Erste Ausstrahlung (firstAired)
- ✅ Letzte Ausstrahlung (lastAired)
- ✅ Nächste Ausstrahlung (nextAired)
- ✅ Status (status)
- ✅ Bewertung (score)
- ✅ Jahr (year)
- ✅ Durchschnittliche Laufzeit (averageRuntime)
- ✅ Ursprungsland (originalCountry)
- ✅ Originalsprache (originalLanguage)
- ✅ Standard-Staffeltyp (defaultSeasonType)
- ✅ Reihenfolge randomisiert (isOrderRandomized)
- ✅ Zuletzt aktualisiert (lastUpdated)

## Layout-Details

### Spalten-Layout pro Section

- **Section 1 (Basis):** 2 Spalten
- **Section 2 (Details):** 2 Spalten
- **Section 3 (Ausstrahlungsdaten):** 3 Spalten
- **Section 4 (Status & Bewertung):** 2 Spalten
- **Section 5 (Herkunft):** 2 Spalten
- **Section 6 (Erweitert):** 2 Spalten

### Collapsed Sections

- **Section 5 (Herkunft & Sprache):** Eingeklappt
- **Section 6 (Erweiterte Einstellungen):** Eingeklappt
    - Spart Platz bei weniger genutzten Feldern
    - Kann bei Bedarf aufgeklappt werden

### Feld-Breiten

- **Volle Breite (2 Spalten):** Serienname, TheTVDB ID, Titel, Beschreibung, Slug, Poster-URL, Zuletzt aktualisiert
- **Halbe Breite (1 Spalte):** Status, Bewertung, Jahr, Laufzeit, Land, Sprache, Staffeltyp, Toggle

## Validierungen

### Pflichtfelder

- ✅ Serienname
- ✅ TheTVDB ID

### Numerische Validierungen

- **TheTVDB ID:** numerisch
- **Bewertung:** min: 0, max: 100
- **Jahr:** min: 1900, max: 2100
- **Laufzeit:** min: 1
- **Staffeltyp:** numerisch

### String-Validierungen

- **Serienname:** max: 255 Zeichen
- **Titel:** max: 255 Zeichen
- **Slug:** max: 255 Zeichen
- **Poster-URL:** max: 500 Zeichen, URL-Format
- **Land:** max: 100 Zeichen
- **Sprache:** max: 100 Zeichen

## Status-Optionen

Die Serie kann folgende Status haben:

- **Continuing:** Serie läuft noch
- **Ended:** Serie wurde beendet
- **Upcoming:** Serie kommt demnächst
- **Pilot:** Serie ist in Pilot-Phase

## Hilfetexte & Platzhalter

Alle Felder haben:

- ✅ Beschreibenden Platzhalter
- ✅ Hilfetext zur Erklärung
- ✅ Beispielwerte (z.B. "z.B. 2024", "z.B. USA")

## Verwendung

### Standard-Verwendung

```php
use App\Filament\Resources\Series\Schemas\SeriesForm;

public function form(Schema $schema): Schema
{
    return SeriesForm::configure($schema);
}
```

### Komponenten direkt nutzen

```php
use App\Filament\Resources\Series\Schemas\SeriesForm;

public function form(Schema $schema): Schema
{
    return $schema->components(
        SeriesForm::getComponents()
    );
}
```

## Best Practices

### 1. Beim Erstellen einer neuen Serie

1. Gib Serienname und TheTVDB ID ein (Pflichtfelder)
2. Fülle Serien-Details aus (Titel, Beschreibung, Poster)
3. Setze Ausstrahlungsdaten wenn bekannt
4. Wähle Status und setze Bewertung
5. Optional: Fülle Herkunft & Sprache aus
6. Optional: Setze erweiterte Einstellungen

### 2. Beim Bearbeiten

- Alle Felder können jederzeit geändert werden
- SeriesData-Felder werden automatisch gespeichert
- Eingeklappte Sections nur öffnen wenn benötigt

### 3. Automatische Daten von TheTVDB

- Viele Felder werden automatisch von TheTVDB gefüllt
- Manuelle Bearbeitung überschreibt automatische Daten
- "Zuletzt aktualisiert" zeigt letzten Sync von TheTVDB

## Technische Details

### Relation Handling

Sections mit `->relationship(Series::has_one_data)` bedeuten:

- ✅ SeriesData wird automatisch mit Series verknüpft
- ✅ Felder in diesen Sections beziehen sich auf SeriesData
- ✅ Create/Update wird automatisch für beide Models ausgeführt

### TranslatableTrait

Das Feld "name" (übersetzt) nutzt den TranslatableTrait:

- Wird in `translations` Array gespeichert
- Kann mehrsprachig sein
- Unterschiedlich von `Series::name` (Hauptname)

## Vergleich: EpisodeForm vs SeriesForm

| Eigenschaft        | EpisodeForm | SeriesForm                    |
|--------------------|-------------|-------------------------------|
| Sections           | 3           | 6                             |
| Collapsed Sections | 1 (Notizen) | 2 (Herkunft, Erweitert)       |
| Pflichtfelder      | 4           | 2                             |
| Relation-Felder    | 6           | 14                            |
| Besonderheit       | Notizen     | Status-Select, 3 Datumsfelder |

## Zusammenfassung

Die neue SeriesForm bietet:

- ✅ **6 strukturierte Sections** für optimale Übersicht
- ✅ **Alle Series- und SeriesData-Felder** sind editierbar
- ✅ **Icons und Beschreibungen** für jede Section
- ✅ **Validierungen** für Datenintegrität
- ✅ **Hilfetext und Platzhalter** für Benutzerfreundlichkeit
- ✅ **Responsive Layout** mit 2-3 Spalten-Grid
- ✅ **2 Collapsed Sections** für weniger genutzte Felder
- ✅ **Status-Select** mit deutschen Übersetzungen
- ✅ **3 Datumsfelder** für Ausstrahlungsinformationen

Perfekt für das Erstellen und Bearbeiten von Serien! 📺✨


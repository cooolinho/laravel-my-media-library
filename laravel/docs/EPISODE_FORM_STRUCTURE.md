# Episode Form - Strukturierte Formular-Ansicht

## Übersicht

Die EpisodeForm wurde komplett überarbeitet und in sinnvolle Sections strukturiert, um eine bessere Übersicht und
Benutzerfreundlichkeit zu bieten.

## Neue Struktur

### 1. Section: Basis-Informationen

**Icon:** `heroicon-o-information-circle`  
**Beschreibung:** Grundlegende Episode-Daten  
**Status:** Immer geöffnet

**Felder:**

- **Serie** (Select)
    - Beziehung zu Series
    - Durchsuchbar und vorgeladen
    - Pflichtfeld
    - Nur sichtbar wenn `$showSeriesField = true`
    - Nimmt 2 Spalten ein

- **Staffel** (TextInput)
    - Numerisch
    - Minimum: 0
    - Standardwert: 1
    - Pflichtfeld
    - 1 Spalte

- **Episode** (TextInput)
    - Numerisch
    - Minimum: 1
    - Standardwert: 1
    - Pflichtfeld
    - 1 Spalte

- **TheTVDB ID** (TextInput)
    - Numerisch
    - Pflichtfeld
    - 2 Spalten

- **In Besitz** (Toggle)
    - Boolean
    - Standardwert: false
    - Mit Hilfetext
    - 2 Spalten

### 2. Section: Episode-Details

**Icon:** `heroicon-o-film`  
**Beschreibung:** Detaillierte Informationen zur Episode (TheTVDB-Daten)  
**Status:** Immer geöffnet  
**Relation:** `Episode::has_one_data` (EpisodeData)

**Felder:**

- **Titel** (TextInput)
    - Maximal 255 Zeichen
    - Mit Platzhalter und Hilfetext
    - 2 Spalten

- **Beschreibung** (Textarea)
    - 4 Zeilen
    - Mit Platzhalter und Hilfetext
    - 2 Spalten

- **Ausstrahlungsdatum** (DatePicker)
    - Format: d.m.Y
    - Nicht-nativ (Custom Picker)
    - Mit Hilfetext
    - 1 Spalte

- **Jahr** (TextInput)
    - Numerisch
    - Minimum: 1900
    - Maximum: 2100
    - Mit Platzhalter
    - 1 Spalte

- **Laufzeit** (TextInput)
    - Numerisch in Minuten
    - Minimum: 1
    - Mit Suffix "min"
    - Mit Platzhalter
    - 1 Spalte

- **Bild-URL** (TextInput)
    - URL-Validierung
    - Maximal 500 Zeichen
    - Mit Platzhalter und Hilfetext
    - 1 Spalte

### 3. Section: Persönliche Notizen

**Icon:** `heroicon-o-pencil-square`  
**Beschreibung:** Ihre eigenen Notizen zur Episode  
**Status:** Standardmäßig eingeklappt (collapsed)

**Felder:**

- **Notizen** (Textarea)
    - 5 Zeilen
    - Mit Platzhalter und Hilfetext
    - Volle Breite (columnSpanFull)

## Vorteile der neuen Struktur

### 1. Übersichtlichkeit

✅ Klare Trennung zwischen verschiedenen Datentypen  
✅ Basis-Daten, Episode-Details und Notizen sind getrennt  
✅ Icons helfen bei der visuellen Orientierung

### 2. Benutzerfreundlichkeit

✅ Wichtige Felder (Basis-Informationen) sind immer sichtbar  
✅ Episode-Details sind direkt erreichbar  
✅ Notizen sind eingeklappt, um Platz zu sparen  
✅ Hilfetext bei allen wichtigen Feldern  
✅ Platzhalter zeigen Beispielwerte

### 3. Datenintegrität

✅ Validierungen auf Feldebene (numeric, minValue, maxValue)  
✅ Required-Flags für Pflichtfelder  
✅ URL-Validierung für Bild-URL  
✅ Beziehung zu EpisodeData wird korrekt gehandhabt

### 4. Flexibilität

✅ Serie-Feld kann optional ausgeblendet werden  
✅ Alle EpisodeData-Felder sind editierbar  
✅ Layout passt sich an Bildschirmgröße an (columns)

## Verwendung

### Standard-Verwendung (mit Serie-Feld)

```php
use App\Filament\Resources\Episodes\Schemas\EpisodeForm;

public function form(Schema $schema): Schema
{
    return EpisodeForm::configure($schema);
}
```

### Ohne Serie-Feld (z.B. in RelationManager)

```php
use App\Filament\Resources\Episodes\Schemas\EpisodeForm;

public function form(Schema $schema): Schema
{
    return $schema->components(
        EpisodeForm::getComponents(showSeriesField: false)
    );
}
```

## Bearbeitbare Daten

### Episode-Model (Haupt-Tabelle)

- ✅ Serie (series_id)
- ✅ Staffelnummer (seasonNumber)
- ✅ Episodennummer (number)
- ✅ TheTVDB ID (theTvDbId)
- ✅ Besitzstatus (owned)
- ✅ Notizen (notes)

### EpisodeData-Model (Relation-Tabelle)

- ✅ Titel (name) - über TranslatableTrait
- ✅ Beschreibung (overview)
- ✅ Ausstrahlungsdatum (aired)
- ✅ Jahr (year)
- ✅ Laufzeit (runtime)
- ✅ Bild-URL (image)

## Layout-Details

### Spalten-Layout

- **Section 1 & 2:** 2 Spalten (columns: 2)
- **Section 3:** 1 Spalte (full-width)

### Feld-Breiten

- **Serie, TheTVDB ID, Owned:** 2 Spalten
- **Staffel, Episode:** Je 1 Spalte (nebeneinander)
- **Titel, Beschreibung:** 2 Spalten (volle Breite)
- **Datum, Jahr, Laufzeit, Bild-URL:** Jeweils 1 Spalte (paarweise nebeneinander)

### Collapsed Sections

- **Persönliche Notizen:** Standardmäßig eingeklappt
    - Spart Platz bei Seiten mit vielen Episoden
    - Kann bei Bedarf aufgeklappt werden

## Validierungen

### Pflichtfelder

- ✅ Serie (wenn sichtbar)
- ✅ Staffel
- ✅ Episode
- ✅ TheTVDB ID

### Numerische Validierungen

- **Staffel:** min: 0
- **Episode:** min: 1
- **TheTVDB ID:** numerisch
- **Jahr:** min: 1900, max: 2100
- **Laufzeit:** min: 1

### String-Validierungen

- **Titel:** max: 255 Zeichen
- **Bild-URL:** max: 500 Zeichen, URL-Format

## Hilfetexte & Platzhalter

Alle wichtigen Felder haben:

- ✅ Beschreibenden Platzhalter
- ✅ Hilfetext zur Erklärung
- ✅ Beispielwerte (z.B. "z.B. 2024")

## Best Practices

### 1. Beim Erstellen einer neuen Episode

1. Wähle die Serie aus
2. Gib Staffel und Episodennummer ein
3. Gib die TheTVDB ID ein
4. Markiere "In Besitz" wenn du die Episode hast
5. Fülle Episode-Details aus (optional)
6. Füge persönliche Notizen hinzu (optional)

### 2. Beim Bearbeiten

- Alle Felder können jederzeit geändert werden
- EpisodeData-Felder werden automatisch gespeichert
- Notizen-Section bleibt eingeklappt wenn nicht genutzt

### 3. In RelationManager

- Serie-Feld wird ausgeblendet (da bereits im Kontext)
- Alle anderen Felder bleiben verfügbar

## Technische Details

### Relation Handling

Die Section "Episode-Details" nutzt:

```php
->relationship(Episode::has_one_data)
```

Dies bedeutet:

- ✅ EpisodeData wird automatisch mit Episode verknüpft
- ✅ Felder in dieser Section beziehen sich auf EpisodeData
- ✅ Create/Update wird automatisch für beide Models ausgeführt

### TranslatableTrait

Das Feld "name" nutzt den TranslatableTrait:

- Wird in `translations` Array gespeichert
- Kann mehrsprachig sein (via TranslatableTrait)

## Zusammenfassung

Die neue EpisodeForm bietet:

- ✅ **3 strukturierte Sections** für bessere Übersicht
- ✅ **Alle Episode- und EpisodeData-Felder** sind editierbar
- ✅ **Icons und Beschreibungen** für jede Section
- ✅ **Validierungen** für Datenintegrität
- ✅ **Hilfetext und Platzhalter** für Benutzerfreundlichkeit
- ✅ **Responsive Layout** mit 2-Spalten-Grid
- ✅ **Collapsed Section** für Notizen (Platzsparend)
- ✅ **Flexibel einsetzbar** (mit/ohne Serie-Feld)

Perfekt für das Erstellen und Bearbeiten von Episoden! 🎬✨


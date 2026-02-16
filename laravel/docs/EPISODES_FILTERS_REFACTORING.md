# Episoden-Filter - Refactoring Dokumentation

## Übersicht

Die Filter der EpisodesTable wurden in separate, wiederverwendbare Klassen aufgeteilt, analog zu den Serien-Filtern.

## 📁 Neue Struktur

```
app/Filament/Resources/Episodes/Tables/
├── EpisodesTable.php                        # Haupttabelle (vereinfacht)
└── Filters/                                 # Filter-Klassen
    ├── OwnedFilter.php                      # Im Besitz Filter
    ├── SeriesFilter.php                     # Serien-Filter
    ├── SeasonFilter.php                     # Staffel-Filter
    ├── YearFilter.php                       # Jahr-Filter
    ├── WithoutDataFilter.php                # Ohne Metadaten Filter
    ├── WithoutNameFilter.php                # Ohne Titel Filter
    └── SpecialsFilter.php                   # Nur Specials Filter
```

## 🎯 Vorteile des Refactorings

### Vorher ❌

```php
// EpisodesTable.php - 150+ Zeilen
->filters([
    TernaryFilter::make(Episode::owned)
        ->label('Im Besitz')
        ->placeholder('Alle Episoden')
        ->trueLabel('Nur im Besitz')
        ->falseLabel('Nicht im Besitz')
        ->queries(
            true: fn (Builder $query) => $query->where(Episode::owned, true),
            false: fn (Builder $query) => $query->where(Episode::owned, false),
            blank: fn (Builder $query) => $query,
        ),
    // ... 100+ weitere Zeilen inline Filter ...
])
```

**Probleme:**

- Unübersichtlich
- Schwer wartbar
- Nicht wiederverwendbar

### Nachher ✅

```php
// EpisodesTable.php - 62 Zeilen
use App\Filament\Resources\Episodes\Tables\Filters\OwnedFilter;
use App\Filament\Resources\Episodes\Tables\Filters\SeriesFilter;
// ...

->filters([
    OwnedFilter::make(),
    SeriesFilter::make(),
    SeasonFilter::make(),
    YearFilter::make(),
    WithoutDataFilter::make(),
    WithoutNameFilter::make(),
    SpecialsFilter::make(),
])
```

**Vorteile:**

- ✅ Übersichtlich
- ✅ Leicht wartbar
- ✅ Wiederverwendbar
- ✅ 60% weniger Code

## 📚 Filter-Klassen im Detail

### 1. OwnedFilter.php

**Typ:** TernaryFilter  
**Zweck:** Filtert nach Besitz-Status der Episoden

**Features:**

- Drei Zustände: Alle / Im Besitz / Nicht im Besitz
- Einfache Boolean-Abfrage
- Benutzerfreundlich

**Code:**

```php
TernaryFilter::make(Episode::owned)
    ->label('Im Besitz')
    ->placeholder('Alle Episoden')
    ->trueLabel('Nur im Besitz')
    ->falseLabel('Nicht im Besitz')
```

**Verwendung:**

```php
OwnedFilter::make()
```

---

### 2. SeriesFilter.php

**Typ:** SelectFilter  
**Zweck:** Filtert Episoden nach Serien

**Features:**

- Suchbar
- Preload-Option
- Alphabetisch sortiert
- Dynamisch aus Datenbank geladen

**Code:**

```php
SelectFilter::make(Episode::series_id)
    ->label('Serie')
    ->searchable()
    ->preload()
    ->options(Series::query()->orderBy('name')->pluck('name', 'id'))
```

**Verwendung:**

```php
SeriesFilter::make()
```

---

### 3. SeasonFilter.php

**Typ:** SelectFilter  
**Zweck:** Filtert nach Staffel-Nummer

**Features:**

- Dynamisch aus vorhandenen Staffeln generiert
- Format: "Staffel X"
- Sortiert aufsteigend

**Code:**

```php
SelectFilter::make(Episode::seasonNumber)
    ->label('Staffel')
    ->options(/* Dynamisch aus DB */)
```

**Verwendung:**

```php
SeasonFilter::make()
```

---

### 4. YearFilter.php

**Typ:** SelectFilter  
**Zweck:** Filtert nach Erscheinungsjahr

**Features:**

- Basiert auf `episode_data.year`
- Sortiert absteigend (neueste zuerst)
- Nur Jahre mit Daten

**Code:**

```php
SelectFilter::make('year')
    ->label('Jahr')
    ->options(/* Jahre aus episode_data */)
    ->query(/* WhereHas auf episode_data */)
```

**Verwendung:**

```php
YearFilter::make()
```

---

### 5. WithoutDataFilter.php

**Typ:** Toggle Filter  
**Zweck:** Zeigt Episoden ohne Metadaten

**Features:**

- Einfacher Toggle
- `doesntHave()` Query
- Für Wartungsarbeiten

**Code:**

```php
Filter::make('without_data')
    ->label('Ohne Metadaten')
    ->toggle()
    ->query(fn($query) => $query->doesntHave(Episode::has_one_data))
```

**Verwendung:**

```php
WithoutDataFilter::make()
```

---

### 6. WithoutNameFilter.php

**Typ:** Toggle Filter  
**Zweck:** Zeigt Episoden ohne deutschen Titel

**Features:**

- Komplexe JSON-Abfrage
- Prüft auf leere/fehlende Translations
- Qualitätskontrolle

**Code:**

```php
Filter::make('without_name')
    ->label('Ohne Titel')
    ->toggle()
    ->query(/* Komplexe JSON-Query für translations */)
```

**Verwendung:**

```php
WithoutNameFilter::make()
```

---

### 7. SpecialsFilter.php

**Typ:** Toggle Filter  
**Zweck:** Zeigt nur Spezial-Episoden (Staffel 0)

**Features:**

- Einfacher Toggle
- Filtert `seasonNumber = 0`
- Für Bonus-Content

**Code:**

```php
Filter::make('specials')
    ->label('Nur Specials')
    ->toggle()
    ->query(fn($query) => $query->where(Episode::seasonNumber, 0))
```

**Verwendung:**

```php
SpecialsFilter::make()
```

---

## 📊 Statistik

### Code-Reduzierung

**EpisodesTable.php:**

```
Vorher: 150+ Zeilen
Nachher: 62 Zeilen
Reduzierung: ~60%
```

### Neue Dateien

**7 Filter-Klassen:**

1. OwnedFilter.php (24 Zeilen)
2. SeriesFilter.php (25 Zeilen)
3. SeasonFilter.php (23 Zeilen)
4. YearFilter.php (34 Zeilen)
5. WithoutDataFilter.php (17 Zeilen)
6. WithoutNameFilter.php (28 Zeilen)
7. SpecialsFilter.php (17 Zeilen)

**Durchschnitt:** 24 Zeilen pro Filter

---

## 🎨 Pattern-Konsistenz

Alle Filter folgen dem gleichen Pattern wie die Serien-Filter:

```php
<?php

namespace App\Filament\Resources\Episodes\Tables\Filters;

use Filament\Tables\Filters\Filter;

class ExampleFilter
{
    public static function make(): Filter
    {
        return Filter::make('example')
            ->label('Example')
            ->query(fn($query) => $query);
    }
}
```

**Vorteile:**

- Konsistenz über alle Tabellen hinweg
- Einfach verständlich
- Leicht erweiterbar

---

## 🔄 Wiederverwendbarkeit

Filter können jetzt in anderen Tabellen verwendet werden:

```php
// In einer anderen Tabelle:
use App\Filament\Resources\Episodes\Tables\Filters\OwnedFilter;
use App\Filament\Resources\Episodes\Tables\Filters\SeriesFilter;

->filters([
    OwnedFilter::make(),
    SeriesFilter::make(),
])
```

---

## 🚀 Erweiterbarkeit

### Neuen Filter hinzufügen

**Schritt 1:** Erstelle Filter-Klasse

```php
// app/Filament/Resources/Episodes/Tables/Filters/DurationFilter.php
class DurationFilter
{
    public static function make(): SelectFilter
    {
        return SelectFilter::make('duration')
            ->label('Laufzeit')
            ->options([
                'short' => '< 30 Min',
                'medium' => '30-60 Min',
                'long' => '> 60 Min',
            ]);
    }
}
```

**Schritt 2:** In EpisodesTable verwenden

```php
use App\Filament\Resources\Episodes\Tables\Filters\DurationFilter;

->filters([
    // ...existing...
    DurationFilter::make(),
])
```

---

## 📈 Vergleich: Episodes vs. Series Filter

### Ähnlichkeiten

- Gleiche Pattern-Struktur
- Statische `make()` Methode
- Gleiche Namespace-Konvention
- Konsistente Namensgebung

### Unterschiede

- Episodes: 7 Filter
- Series: 9 Filter
- Episodes: Fokus auf Metadaten & Staffeln
- Series: Fokus auf Vollständigkeit & Besitz

---

## 🎯 Zusammenfassung der Verbesserungen

### Code-Qualität

- ✅ 60% weniger Code in EpisodesTable
- ✅ 7 separate, fokussierte Klassen
- ✅ Single Responsibility Principle
- ✅ Bessere Lesbarkeit

### Wartbarkeit

- ✅ Einfacher zu debuggen
- ✅ Klare Verantwortlichkeiten
- ✅ Isolierte Änderungen möglich
- ✅ Bessere Organisation

### Wiederverwendbarkeit

- ✅ Filter in anderen Tabellen nutzbar
- ✅ Konsistentes Pattern
- ✅ Einfach zu kombinieren
- ✅ Modular erweiterbar

### Testbarkeit

- ✅ Einzelne Filter testbar
- ✅ Mock-freundlich
- ✅ Unit-Tests möglich
- ✅ Klare Test-Targets

---

## 📝 Dateien-Übersicht

### Geändert:

✅ `EpisodesTable.php` - Von 150+ auf 62 Zeilen reduziert

### Neu erstellt:

✅ `Filters/OwnedFilter.php`  
✅ `Filters/SeriesFilter.php`  
✅ `Filters/SeasonFilter.php`  
✅ `Filters/YearFilter.php`  
✅ `Filters/WithoutDataFilter.php`  
✅ `Filters/WithoutNameFilter.php`  
✅ `Filters/SpecialsFilter.php`

---

## 🎓 Best Practices

### DO ✅

- Klare, beschreibende Namen
- Statische `make()` Methode
- Konfiguration in der Klasse
- Deutsche Labels für Benutzerfreundlichkeit

### DON'T ❌

- Generische Namen wie "Filter1"
- Externe Konfiguration
- Instanz-Methoden statt statisch
- Mehrere Verantwortlichkeiten pro Klasse

---

## 🔧 Technische Details

### Filter-Typen verwendet:

- **TernaryFilter:** OwnedFilter (3 Zustände)
- **SelectFilter:** SeriesFilter, SeasonFilter, YearFilter (Dropdown)
- **Toggle Filter:** WithoutDataFilter, WithoutNameFilter, SpecialsFilter (An/Aus)

### Performance-Optimierungen:

- `preload()` für SeriesFilter
- `distinct()` für SeasonFilter
- Lazy Loading für Options
- Effiziente Query-Builder-Nutzung

---

## ✨ Fazit

**Status:** ✅ **KOMPLETT ABGESCHLOSSEN!**

Das Refactoring der Episoden-Filter ist analog zu den Serien-Filtern erfolgreich durchgeführt worden.

**Ergebnis:**

- 7 neue, saubere Filter-Klassen
- 60% Code-Reduzierung in EpisodesTable
- Konsistente Struktur über alle Tabellen
- Bessere Wartbarkeit und Wiederverwendbarkeit

**Beide Tabellen (Episodes & Series) folgen jetzt dem gleichen, sauberen Pattern!** 🎉


# Serien-Filter - Refactoring Dokumentation

## Übersicht

Die Filter der SeriesTable wurden in separate, wiederverwendbare Klassen aufgeteilt, um die Code-Organisation und
Wartbarkeit zu verbessern.

## 📁 Neue Struktur

```
app/Filament/Resources/Series/Tables/
├── SeriesTable.php                          # Haupttabelle (vereinfacht)
└── Filters/                                 # Filter-Klassen
    ├── CompletenessFilter.php               # Vollständigkeits-Filter
    ├── StatusFilter.php                     # Status-Filter (Ended/Continuing/Upcoming)
    ├── YearFilter.php                       # Jahr-Filter
    ├── EpisodeCountFilter.php               # Episodenanzahl-Filter
    ├── OwnershipPercentageFilter.php        # Besitzanteil-Filter
    ├── WithoutDataFilter.php                # Ohne Metadaten Filter
    ├── WithoutEpisodesFilter.php            # Ohne Episoden Filter
    └── WithoutArtworksFilter.php            # Ohne Cover Filter
```

## 🎯 Vorteile des Refactorings

### 1. **Bessere Organisation**

```php
// Vorher: SeriesTable.php (200+ Zeilen)
->filters([
    TernaryFilter::make('complete')
        ->label('Vollständigkeit')
        // ... 40 Zeilen Code ...
    SelectFilter::make('status')
        // ... 20 Zeilen Code ...
    // ... weitere 100+ Zeilen ...
])

// Nachher: SeriesTable.php (83 Zeilen)
->filters([
    CompletenessFilter::make(),
    StatusFilter::make(),
    YearFilter::make(),
    EpisodeCountFilter::make(),
    OwnershipPercentageFilter::make(),
    WithoutDataFilter::make(),
    WithoutEpisodesFilter::make(),
    WithoutArtworksFilter::make(),
])
```

### 2. **Wiederverwendbarkeit**

Filter können jetzt in anderen Tabellen verwendet werden:

```php
// In einer anderen Tabelle:
use App\Filament\Resources\Series\Tables\Filters\StatusFilter;

->filters([
    StatusFilter::make(),
])
```

### 3. **Einfachere Wartung**

Jeder Filter hat seine eigene Datei:

- Einfacher zu finden
- Einfacher zu testen
- Einfacher zu debuggen
- Klare Verantwortlichkeiten

### 4. **Bessere Testbarkeit**

```php
// Test für einzelnen Filter
public function test_completeness_filter()
{
    $filter = CompletenessFilter::make();
    // Test-Logic
}
```

## 📚 Filter-Klassen im Detail

### 1. CompletenessFilter.php

**Typ:** TernaryFilter  
**Zweck:** Filtert nach Vollständigkeit (100% vs. Unvollständig)

**Features:**

- Drei Zustände: Alle / Vollständig / Unvollständig
- Komplexe SQL-Aggregation
- Nutzt `withCount()` für Performance

**Verwendung:**

```php
CompletenessFilter::make()
```

---

### 2. StatusFilter.php

**Typ:** SelectFilter  
**Zweck:** Filtert nach Serien-Status (TheTVDB)

**Optionen:**

- Ended (Beendet)
- Continuing (Laufend)
- Upcoming (Geplant)

**Verwendung:**

```php
StatusFilter::make()
```

---

### 3. YearFilter.php

**Typ:** SelectFilter  
**Zweck:** Filtert nach Erscheinungsjahr

**Features:**

- Dynamische Optionen aus Datenbank
- Sortiert absteigend (neueste zuerst)
- Cached für Performance

**Verwendung:**

```php
YearFilter::make()
```

---

### 4. EpisodeCountFilter.php

**Typ:** SelectFilter  
**Zweck:** Filtert nach Episodenanzahl

**Bereiche:**

- 1-10 Episoden
- 11-25 Episoden
- 26-50 Episoden
- 51-100 Episoden
- 100+ Episoden

**Verwendung:**

```php
EpisodeCountFilter::make()
```

---

### 5. OwnershipPercentageFilter.php

**Typ:** SelectFilter  
**Zweck:** Filtert nach Besitzanteil in Prozent

**Bereiche:**

- 0% (Keine)
- 1-25%
- 26-50%
- 51-75%
- 76-99%
- 100% (Vollständig)

**Verwendung:**

```php
OwnershipPercentageFilter::make()
```

---

### 6. WithoutDataFilter.php

**Typ:** Toggle Filter  
**Zweck:** Zeigt Serien ohne Metadaten

**SQL:** `doesntHave(Series::has_one_data)`

**Verwendung:**

```php
WithoutDataFilter::make()
```

---

### 7. WithoutEpisodesFilter.php

**Typ:** Toggle Filter  
**Zweck:** Zeigt Serien ohne Episoden

**SQL:** `doesntHave(Series::has_many_episodes)`

**Verwendung:**

```php
WithoutEpisodesFilter::make()
```

---

### 8. WithoutArtworksFilter.php

**Typ:** Toggle Filter  
**Zweck:** Zeigt Serien ohne Cover/Artworks

**SQL:** `doesntHave(Series::has_many_artworks)`

**Verwendung:**

```php
WithoutArtworksFilter::make()
```

---

## 🔧 Implementierungspattern

Alle Filter folgen diesem Pattern:

```php
<?php

namespace App\Filament\Resources\Series\Tables\Filters;

use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class ExampleFilter
{
    public static function make(): Filter
    {
        return Filter::make('filter_name')
            ->label('Filter Label')
            ->query(fn (Builder $query) => $query->where(...));
    }
}
```

### Pattern-Vorteile:

- ✅ Statische `make()` Methode
- ✅ Gibt Filament-Filter zurück
- ✅ Konfiguration innerhalb der Klasse
- ✅ Klare Namespace-Struktur

## 🚀 Erweiterbarkeit

### Neuen Filter hinzufügen

**Schritt 1:** Erstelle neue Filter-Klasse

```php
// app/Filament/Resources/Series/Tables/Filters/GenreFilter.php
<?php

namespace App\Filament\Resources\Series\Tables\Filters;

use Filament\Tables\Filters\SelectFilter;

class GenreFilter
{
    public static function make(): SelectFilter
    {
        return SelectFilter::make('genre')
            ->label('Genre')
            ->options([
                'drama' => 'Drama',
                'comedy' => 'Comedy',
            ]);
    }
}
```

**Schritt 2:** In SeriesTable verwenden

```php
use App\Filament\Resources\Series\Tables\Filters\GenreFilter;

->filters([
    // ...existing filters...
    GenreFilter::make(),
])
```

## 📊 Vergleich: Vorher vs. Nachher

### Vorher (inline Filter)

```php
// SeriesTable.php - 200+ Zeilen
class SeriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->filters([
                TernaryFilter::make('complete')
                    ->label('Vollständigkeit')
                    ->placeholder('Alle Serien')
                    ->trueLabel('Vollständig (100%)')
                    ->falseLabel('Unvollständig')
                    ->queries(
                        true: function (Builder $query) {
                            // 20 Zeilen Logic
                        },
                        false: function (Builder $query) {
                            // 20 Zeilen Logic
                        },
                    ),
                // ... 150+ weitere Zeilen ...
            ]);
    }
}
```

**Probleme:**

- ❌ Unübersichtlich
- ❌ Schwer wartbar
- ❌ Nicht wiederverwendbar
- ❌ Schwer zu testen

### Nachher (separate Klassen)

```php
// SeriesTable.php - 83 Zeilen
class SeriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->filters([
                CompletenessFilter::make(),
                StatusFilter::make(),
                YearFilter::make(),
                EpisodeCountFilter::make(),
                OwnershipPercentageFilter::make(),
                WithoutDataFilter::make(),
                WithoutEpisodesFilter::make(),
                WithoutArtworksFilter::make(),
            ]);
    }
}

// CompletenessFilter.php - 45 Zeilen
class CompletenessFilter
{
    public static function make(): TernaryFilter
    {
        return TernaryFilter::make('complete')
            // Configuration
    }
}
```

**Vorteile:**

- ✅ Übersichtlich
- ✅ Einfach wartbar
- ✅ Wiederverwendbar
- ✅ Testbar
- ✅ Single Responsibility Principle

## 🧪 Testing

Filter können jetzt einzeln getestet werden:

```php
// tests/Unit/Filters/CompletenessFilterTest.php
class CompletenessFilterTest extends TestCase
{
    public function test_filter_returns_complete_series()
    {
        $filter = CompletenessFilter::make();
        
        // Test complete series filtering
        // ...
    }
    
    public function test_filter_returns_incomplete_series()
    {
        $filter = CompletenessFilter::make();
        
        // Test incomplete series filtering
        // ...
    }
}
```

## 📈 Performance

**Keine Performance-Einbußen:**

- Filter werden zur Laufzeit instanziiert
- Lazy Loading der Options
- Query-Optimierungen bleiben erhalten

**Vorteile:**

- Besseres Code-Splitting
- Einfachere Optimierung einzelner Filter
- Klare Query-Logik

## 🎯 Best Practices

### DO ✅

```php
// Klare Namensgebung
class CompletenessFilter { }

// Statische make() Methode
public static function make(): Filter

// Konfiguration in der Klasse
->label('Vollständigkeit')
->placeholder('Alle Serien')
```

### DON'T ❌

```php
// Keine allgemeinen Namen
class Filter1 { }

// Keine Instanz-Methode
public function make(): Filter

// Keine externe Konfiguration
CompletenessFilter::make()->label('...')
```

## 🔄 Migration Guide

Falls du eigene Filter hinzufügen möchtest:

**1. Erstelle neue Filter-Klasse:**

```bash
# Erstelle Datei
touch app/Filament/Resources/Series/Tables/Filters/MyFilter.php
```

**2. Implementiere Pattern:**

```php
<?php

namespace App\Filament\Resources\Series\Tables\Filters;

use Filament\Tables\Filters\Filter;

class MyFilter
{
    public static function make(): Filter
    {
        return Filter::make('my_filter')
            ->label('My Filter')
            ->query(fn($query) => $query);
    }
}
```

**3. In SeriesTable verwenden:**

```php
use App\Filament\Resources\Series\Tables\Filters\MyFilter;

->filters([
    // ...existing...
    MyFilter::make(),
])
```

## 📝 Zusammenfassung

### Was wurde gemacht:

✅ 8 Filter-Klassen erstellt  
✅ SeriesTable vereinfacht (200+ → 83 Zeilen)  
✅ Klare Struktur mit Namespaces  
✅ Wiederverwendbares Pattern

### Vorteile:

✅ Bessere Code-Organisation  
✅ Einfachere Wartung  
✅ Wiederverwendbarkeit  
✅ Testbarkeit  
✅ Single Responsibility

### Dateien:

- ✅ 8 neue Filter-Klassen
- ✅ 1 vereinfachte SeriesTable
- ✅ Keine Breaking Changes

**Das Refactoring ist abgeschlossen und produktionsbereit!** 🎉


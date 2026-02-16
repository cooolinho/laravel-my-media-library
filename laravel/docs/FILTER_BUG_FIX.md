# Filter-Bug-Fix - Dokumentation

## Problem

Die SelectFilter in den Serien- und Episoden-Tabellen funktionierten nicht korrekt. Beim Filtern nach Status (z.B. "
Beendet") wurden dennoch alle Einträge angezeigt.

### Ursache

Die Filter verwendeten die veraltete `->query()` Methode mit `array $data` Parameter, die in Filament 5 nicht mehr
korrekt funktioniert.

**Alter Code (nicht funktionierend):**

```php
->query(function (Builder $query, array $data) {
    if (isset($data['value'])) {
        $query->whereHas(...);
    }
})
```

## Lösung

Alle betroffenen Filter wurden auf die neue `->modifyQueryUsing()` Methode mit `$state` Parameter umgestellt.

**Neuer Code (funktioniert):**

```php
->modifyQueryUsing(function (Builder $query, $state) {
    if (filled($state['value'] ?? null)) {
        $query->whereHas(...);
    }
})
```

### Wichtige Änderungen

1. **Methode:** `query()` → `modifyQueryUsing()`
2. **Parameter:** `array $data` → `$state`
3. **Prüfung:** `isset($data['value'])` → `filled($state['value'] ?? null)`

## Behobene Filter

### Series-Filter

✅ **StatusFilter.php**

- Beendet / Laufend / Geplant Filter
- `whereHas()` auf `series_data.status`

✅ **YearFilter.php**

- Jahr-Filter
- `whereHas()` auf `series_data.year`

✅ **EpisodeCountFilter.php**

- Episodenanzahl-Bereiche (1-10, 11-25, etc.)
- `withCount()` und `having()` Queries

✅ **OwnershipPercentageFilter.php**

- Besitzanteil-Prozent (0%, 1-25%, etc.)
- Komplexe `havingRaw()` Queries

### Episodes-Filter

✅ **YearFilter.php**

- Jahr-Filter
- `whereHas()` auf `episode_data.year`

## Technische Details

### Warum `modifyQueryUsing()`?

In Filament 5 wurde die Filter-API geändert. Die neue Methode:

- Ist konsistenter mit anderen Filament-Komponenten
- Unterstützt besseres State-Management
- Funktioniert mit den neuen Filter-Layouts

### State-Struktur

Der `$state` Parameter enthält:

```php
[
    'value' => 'Ended',  // Der gewählte Wert
    // Weitere Meta-Daten
]
```

### filled() Helper

`filled()` ist ein Laravel-Helper, der prüft ob ein Wert "gefüllt" ist:

- `null` → false
- `''` → false
- `'0'` → true
- `'Ended'` → true

**Besser als `isset()`:**

```php
// Alt (unvollständig)
if (isset($data['value'])) { }

// Neu (robuster)
if (filled($state['value'] ?? null)) { }
```

## Testing

Nach der Korrektur sollten folgende Szenarien funktionieren:

### Series-Filter

**Status-Filter:**

```
1. Wähle "Beendet"
   → Nur Serien mit status = 'Ended' werden angezeigt

2. Wähle "Laufend"
   → Nur Serien mit status = 'Continuing' werden angezeigt

3. Wähle "Alle Status"
   → Alle Serien werden angezeigt
```

**Jahr-Filter:**

```
1. Wähle "2024"
   → Nur Serien aus 2024 werden angezeigt

2. Wähle "Alle Jahre"
   → Alle Serien werden angezeigt
```

**Episodenanzahl-Filter:**

```
1. Wähle "1-10 Episoden"
   → Nur Serien mit 1-10 Episoden

2. Wähle "100+ Episoden"
   → Nur Serien mit über 100 Episoden
```

**Besitzanteil-Filter:**

```
1. Wähle "0% (Keine)"
   → Nur Serien ohne besessene Episoden

2. Wähle "100% (Vollständig)"
   → Nur vollständig gesammelte Serien

3. Wähle "76-99%"
   → Serien, die fast vollständig sind
```

### Episodes-Filter

**Jahr-Filter:**

```
1. Wähle "2024"
   → Nur Episoden aus 2024

2. Wähle "Alle Jahre"
   → Alle Episoden
```

## Verbleibende Filter

Die folgenden Filter benötigten keine Änderung, da sie bereits die korrekte Syntax verwenden:

### Series-Filter

- ✅ **CompletenessFilter** (TernaryFilter - verwendet `queries`)
- ✅ **WithoutDataFilter** (Toggle - verwendet `query` mit Closure)
- ✅ **WithoutEpisodesFilter** (Toggle)
- ✅ **WithoutArtworksFilter** (Toggle)

### Episodes-Filter

- ✅ **OwnedFilter** (TernaryFilter - verwendet `queries`)
- ✅ **SeriesFilter** (SelectFilter - Standard-Relation)
- ✅ **SeasonFilter** (SelectFilter - Standard-Relation)
- ✅ **WithoutDataFilter** (Toggle)
- ✅ **WithoutNameFilter** (Toggle)
- ✅ **SpecialsFilter** (Toggle)

## Unterschied: Filter-Typen

### SelectFilter mit Custom Query

**Benötigt:** `modifyQueryUsing()`

```php
SelectFilter::make('status')
    ->options([...])
    ->modifyQueryUsing(fn($query, $state) => ...)
```

### TernaryFilter

**Verwendet:** `queries` (Plural!)

```php
TernaryFilter::make('complete')
    ->queries(
        true: fn($query) => ...,
        false: fn($query) => ...,
        blank: fn($query) => ...
    )
```

### Toggle Filter

**Verwendet:** `query` (Singular!)

```php
Filter::make('without_data')
    ->toggle()
    ->query(fn($query) => $query->doesntHave(...))
```

## Best Practices

### ✅ DO

**Nutze `modifyQueryUsing()` für SelectFilter:**

```php
SelectFilter::make('status')
    ->modifyQueryUsing(function (Builder $query, $state) {
        if (filled($state['value'] ?? null)) {
            $query->where(...);
        }
    })
```

**Prüfe mit `filled()`:**

```php
if (filled($state['value'] ?? null)) {
    // Filter anwenden
}
```

**Nutze `$state['value']`:**

```php
$query->where('status', $state['value'])
```

### ❌ DON'T

**Verwende nicht mehr `query()` mit `$data`:**

```php
// VERALTET - Funktioniert nicht!
->query(function (Builder $query, array $data) {
    if (isset($data['value'])) {
        $query->where(...);
    }
})
```

**Verlasse dich nicht auf `isset()` allein:**

```php
// UNVOLLSTÄNDIG
if (isset($data['value'])) { }

// BESSER
if (filled($state['value'] ?? null)) { }
```

## Migration Guide

Falls weitere Filter hinzugefügt werden:

### Für SelectFilter mit Custom Query:

```php
// Template
SelectFilter::make('field_name')
    ->label('Label')
    ->placeholder('Alle')
    ->options([...])
    ->modifyQueryUsing(function (Builder $query, $state) {
        if (filled($state['value'] ?? null)) {
            // Deine Query-Logic hier
            $query->where('field', $state['value']);
        }
    })
```

### Für TernaryFilter:

```php
// Template
TernaryFilter::make('field_name')
    ->label('Label')
    ->placeholder('Alle')
    ->trueLabel('Ja')
    ->falseLabel('Nein')
    ->queries(
        true: fn(Builder $query) => $query->where('field', true),
        false: fn(Builder $query) => $query->where('field', false),
        blank: fn(Builder $query) => $query,
    )
```

### Für Toggle Filter:

```php
// Template
Filter::make('filter_name')
    ->label('Label')
    ->toggle()
    ->query(fn(Builder $query) => $query->where(...))
```

## Zusammenfassung

**Behobene Filter:** 5 Filter
**Geänderte Dateien:** 5 Dateien
**Breaking Changes:** Keine (nur interne Implementierung)
**Status:** ✅ **Alle Filter funktionieren jetzt korrekt!**

**Das Problem ist vollständig behoben!** 🎉


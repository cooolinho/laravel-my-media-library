# Episoden-Tabelle - Filter-Dokumentation

## Übersicht

Die Episoden-Tabelle wurde mit umfassenden, benutzerfreundlichen Filtern ausgestattet, um schnell die gewünschten
Episoden zu finden.

## 🎯 Verfügbare Filter

### 1. **Im Besitz** (TernaryFilter)

**Typ:** Ternary (Drei Zustände)

**Optionen:**

- 📦 **Alle Episoden** (Standard)
- ✅ **Nur im Besitz** - Zeigt nur Episoden an, die du besitzt
- ❌ **Nicht im Besitz** - Zeigt nur Episoden, die du noch nicht hast

**Verwendung:**
Schnelle Übersicht darüber, welche Episoden noch fehlen oder bereits vorhanden sind.

---

### 2. **Serie** (SelectFilter)

**Typ:** Dropdown mit Suche

**Funktionen:**

- 🔍 Suchbar - Finde Serien durch Tippen
- 📝 Vorgeladen - Alle Serien werden geladen
- 🔤 Alphabetisch sortiert

**Verwendung:**
Filtere Episoden einer bestimmten Serie, z.B. "Breaking Bad" oder "Game of Thrones".

**Beispiel:**

```
Serie: Breaking Bad
→ Zeigt nur Episoden von Breaking Bad
```

---

### 3. **Staffel** (SelectFilter)

**Typ:** Dropdown

**Funktionen:**

- Dynamisch generiert aus vorhandenen Staffeln
- Sortiert von niedrig nach hoch
- Zeigt Format "Staffel X"

**Verwendung:**
Fokussiere auf eine bestimmte Staffel, z.B. Staffel 1 oder Staffel 5.

**Beispiel:**

```
Staffel: Staffel 3
→ Zeigt nur Episoden der Staffel 3 aller Serien
```

**Tipp:** Kombiniere mit Serie-Filter für beste Ergebnisse!

---

### 4. **Jahr** (SelectFilter)

**Typ:** Dropdown

**Funktionen:**

- Basiert auf `episode_data.year`
- Sortiert absteigend (neueste zuerst)
- Nur Jahre mit vorhandenen Daten

**Verwendung:**
Finde Episoden, die in einem bestimmten Jahr ausgestrahlt wurden.

**Beispiel:**

```
Jahr: 2024
→ Zeigt alle Episoden aus 2024
```

**Anwendungsfälle:**

- Finde neue Episoden aus dem aktuellen Jahr
- Vergleiche Episoden verschiedener Jahre
- Analyse von Veröffentlichungsmustern

---

### 5. **Ohne Metadaten** (Toggle)

**Typ:** Toggle (An/Aus)

**Funktionalität:**
Zeigt Episoden **ohne** verknüpfte `episode_data` an.

**Verwendung:**
Identifiziere Episoden, für die noch keine Metadaten von TheTVDB heruntergeladen wurden.

**Workflow:**

1. Filter aktivieren
2. Fehlende Metadaten identifizieren
3. Metadaten-Jobs für diese Episoden starten

**SQL-Query:**

```sql
WHERE NOT EXISTS (
    SELECT 1 FROM episode_data 
    WHERE episode_data.episode_id = episodes.id
)
```

---

### 6. **Ohne Titel** (Toggle)

**Typ:** Toggle (An/Aus)

**Funktionalität:**
Zeigt Episoden, die:

- Keine deutschen Namen haben
- Leere Translations haben
- Oder gar keine Daten haben

**Verwendung:**
Finde Episoden mit unvollständigen Übersetzungen oder fehlenden Titeln.

**Use Cases:**

- Qualitätskontrolle der Metadaten
- Identifikation fehlender Übersetzungen
- Prüfung von Datenintegrität

**SQL-Logik:**

```sql
WHERE (
    translations IS NULL
    OR JSON_LENGTH(JSON_EXTRACT(translations, '$.deu.name')) = 0
    OR JSON_UNQUOTE(JSON_EXTRACT(translations, '$.deu.name')) = ''
)
OR episode_data NOT EXISTS
```

---

### 7. **Nur Specials** (Toggle)

**Typ:** Toggle (An/Aus)

**Funktionalität:**
Zeigt nur Spezial-Episoden (Staffel 0).

**Was sind Specials?**

- Behind-the-Scenes
- Interviews
- Pilotfolgen
- Bonusmaterial
- Zusammenfassungen

**Verwendung:**
Schneller Zugriff auf Bonus-Content und Specials.

**Beispiel:**

```
Staffel: 0
→ S00E01, S00E02, etc.
```

---

## 🎨 Filter-Kombinationen

### Praktische Beispiele

#### 1. Fehlende Episoden einer Serie

```
Filter:
- Serie: Breaking Bad
- Im Besitz: Nicht im Besitz
```

**Ergebnis:** Alle Breaking Bad Episoden, die du noch nicht hast

---

#### 2. Qualitätskontrolle

```
Filter:
- Ohne Titel: AN
- Ohne Metadaten: AN
```

**Ergebnis:** Episoden mit unvollständigen Daten

---

#### 3. Neue Episoden

```
Filter:
- Jahr: 2026
- Im Besitz: Nicht im Besitz
```

**Ergebnis:** Neue Episoden aus 2026, die noch fehlen

---

#### 4. Staffel-Übersicht

```
Filter:
- Serie: Game of Thrones
- Staffel: Staffel 5
```

**Ergebnis:** Alle Episoden der 5. Staffel von GoT

---

#### 5. Specials-Check

```
Filter:
- Nur Specials: AN
- Im Besitz: Nicht im Besitz
```

**Ergebnis:** Fehlende Bonus-Episoden

---

## 🔧 Technische Details

### Filter-Typen

**TernaryFilter:**

- Drei Zustände: Alle / Wahr / Falsch
- Bessere UX als einfache Boolean-Filter
- Verwendet für: Im Besitz

**SelectFilter:**

- Dropdown mit Optionen
- Optional mit Suche
- Verwendet für: Serie, Staffel, Jahr

**Toggle Filter:**

- Einfaches An/Aus
- Für spezifische Bedingungen
- Verwendet für: Ohne Metadaten, Ohne Titel, Nur Specials

### Performance-Optimierungen

**Preload:**

```php
->preload()  // Lädt Optionen im Voraus
```

**Searchable:**

```php
->searchable()  // Aktiviert Suchfunktion
```

**Eager Loading:**
Die Tabelle lädt automatisch Beziehungen via `modifyQueryUsing()`.

---

## 📊 Filter-Status

### Übersicht der implementierten Filter

| Filter         | Typ           | Status | Verwendung        |
|----------------|---------------|--------|-------------------|
| Im Besitz      | TernaryFilter | ✅      | Hoch              |
| Serie          | SelectFilter  | ✅      | Sehr hoch         |
| Staffel        | SelectFilter  | ✅      | Hoch              |
| Jahr           | SelectFilter  | ✅      | Mittel            |
| Ohne Metadaten | Toggle        | ✅      | Niedrig (Wartung) |
| Ohne Titel     | Toggle        | ✅      | Niedrig (Wartung) |
| Nur Specials   | Toggle        | ✅      | Niedrig           |

---

## 🚀 Zukünftige Erweiterungen

### Mögliche zusätzliche Filter:

1. **Laufzeit**
    - Filter nach Episode-Länge
    - z.B. "< 30 Min", "30-60 Min", "> 60 Min"

2. **Ausstrahlungsdatum**
    - Filter nach Datum-Bereich
    - z.B. "Letzte 30 Tage", "Dieses Jahr"

3. **Bewertung**
    - Falls TheTVDB-Ratings vorhanden
    - z.B. "> 8.0", "Top bewertet"

4. **Mit Bild**
    - Episoden mit/ohne Cover-Bild
    - Für Qualitätskontrolle

5. **Serie-Status**
    - Filter nach laufenden/beendeten Serien
    - Nützlich für Update-Strategie

---

## 💡 Best Practices

### Effiziente Nutzung

1. **Kombiniere Filter** für präzise Ergebnisse
2. **Speichere häufige Filter** als Lesezeichen
3. **Nutze Suche** zusätzlich zu Filtern
4. **Prüfe regelmäßig** "Ohne Metadaten" für Wartung

### Workflow-Empfehlungen

**Wöchentlich:**

- Filter "Jahr: Aktuell" + "Nicht im Besitz"
- Neue Episoden identifizieren

**Monatlich:**

- Filter "Ohne Metadaten" + "Ohne Titel"
- Datenqualität prüfen

**Bei neuer Serie:**

- Filter "Serie: X"
- Übersicht über alle Episoden

---

## 📝 Zusammenfassung

Die Episoden-Tabelle bietet jetzt **7 leistungsstarke Filter**:

✅ **Besitz-Status** - Finde fehlende Episoden
✅ **Serie** - Fokus auf eine Serie
✅ **Staffel** - Staffel-spezifische Ansicht
✅ **Jahr** - Zeitliche Eingrenzung
✅ **Ohne Metadaten** - Qualitätskontrolle
✅ **Ohne Titel** - Unvollständige Daten
✅ **Nur Specials** - Bonus-Content

**Alle Filter sind kombinierbar für maximale Flexibilität!** 🎉


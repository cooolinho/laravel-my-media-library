# Serien-Tabelle - Filter-Dokumentation

## Übersicht

Die Serien-Tabelle wurde mit **9 umfassenden Filtern** ausgestattet, um schnell die gewünschten Serien zu finden und zu
analysieren.

## 🎯 Verfügbare Filter

### 1. **Vollständigkeit** (TernaryFilter)

**Typ:** Ternary (Drei Zustände)

**Optionen:**

- 📦 **Alle Serien** (Standard)
- ✅ **Vollständig (100%)** - Serien, bei denen alle Episoden im Besitz sind
- ⚠️ **Unvollständig** - Serien mit fehlenden Episoden

**SQL-Logik:**

```sql
-- Vollständig:
owned_episodes_count
= episodes_count
AND episodes_count > 0

-- Unvollständig:
owned_episodes_count < episodes_count
```

**Verwendung:**

- Finde Serien, die du noch vervollständigen musst
- Identifiziere bereits komplett gesammelte Serien
- Priorisiere Downloads

**Beispiel:**

```
Vollständigkeit: Unvollständig
→ Zeigt alle Serien mit fehlenden Episoden
```

---

### 2. **Status** (SelectFilter)

**Typ:** Dropdown

**Optionen:**

- 🏁 **Beendet** (Ended) - Abgeschlossene Serien
- ▶️ **Laufend** (Continuing) - Aktive Serien mit neuen Episoden
- 📅 **Geplant** (Upcoming) - Noch nicht gestartete Serien

**Datenquelle:** `series_data.status` (TheTVDB)

**Verwendung:**

- Fokus auf laufende Serien für Updates
- Filtere abgeschlossene Serien
- Plane zukünftige Downloads

**Anwendungsfälle:**

```
Status: Laufend
→ Zeigt Serien mit neuen Episoden

Status: Beendet
→ Zeigt abgeschlossene Serien
```

---

### 3. **Jahr** (SelectFilter)

**Typ:** Dropdown

**Funktionen:**

- Basiert auf `series_data.year` (Erstausstrahlung)
- Sortiert absteigend (neueste zuerst)
- Dynamisch aus Datenbank generiert

**Verwendung:**

- Finde Serien aus einem bestimmten Jahr
- Analyse von Veröffentlichungsjahren
- Retro-Serien vs. neue Serien

**Beispiele:**

```
Jahr: 2026
→ Neue Serien aus 2026

Jahr: 2008
→ Breaking Bad, etc.
```

---

### 4. **Episodenanzahl** (SelectFilter)

**Typ:** Dropdown mit Bereichen

**Optionen:**

- 📺 **1-10 Episoden** - Mini-Serien
- 📺 **11-25 Episoden** - Kurze Serien / Staffeln
- 📺 **26-50 Episoden** - Standard-Serien
- 📺 **51-100 Episoden** - Längere Serien
- 📺 **Über 100 Episoden** - Langzeit-Serien

**Verwendung:**

- Finde kurze Serien für schnelles Binge-Watching
- Identifiziere umfangreiche Serien-Sammlungen
- Planung von Speicherplatz

**Use Cases:**

```
Episodenanzahl: 1-10
→ Mini-Serien wie "Chernobyl"

Episodenanzahl: 100+
→ Langzeit-Serien wie "Supernatural"
```

**Beispiele:**

- **1-10:** Limitierte Serien, Mini-Series
- **11-25:** Typisch für 1-2 Staffeln
- **26-50:** Standard-Serie mit 2-3 Staffeln
- **51-100:** Mehrere Staffeln
- **100+:** The Simpsons, Grey's Anatomy, etc.

---

### 5. **Besitzanteil** (SelectFilter)

**Typ:** Dropdown mit Prozent-Bereichen

**Optionen:**

- ❌ **0% (Keine)** - Noch keine Episode besessen
- 🔴 **1-25%** - Sehr unvollständig
- 🟠 **26-50%** - Weniger als Hälfte
- 🟡 **51-75%** - Mehr als Hälfte
- 🟢 **76-99%** - Fast vollständig
- ✅ **100% (Vollständig)** - Alle Episoden

**SQL-Logik:**

```sql
(owned_episodes_count / episodes_count * 100)
BETWEEN X AND Y
```

**Verwendung:**

- Priorisiere fast vollständige Serien
- Finde Serien, die gerade gestartet wurden
- Identifiziere Serien ohne Downloads

**Strategische Anwendung:**

```
Besitzanteil: 76-99%
→ Serien, die nur noch wenige Episoden brauchen
→ Priorisiere diese für schnelle Vervollständigung

Besitzanteil: 0%
→ Serien, die du hinzugefügt aber nicht heruntergeladen hast
```

---

### 6. **Ohne Metadaten** (Toggle)

**Typ:** Toggle (An/Aus)

**Funktionalität:**
Zeigt Serien **ohne** verknüpfte `series_data`.

**SQL-Query:**

```sql
WHERE NOT EXISTS (
    SELECT 1 FROM series_data 
    WHERE series_data.series_id = series.id
)
```

**Verwendung:**

- Wartung: Identifiziere Serien ohne TheTVDB-Daten
- Qualitätskontrolle
- Trigger für Metadaten-Import

**Workflow:**

1. Filter aktivieren
2. Serien ohne Metadaten identifizieren
3. Metadaten-Jobs für diese Serien starten
4. Cover, Beschreibungen, etc. nachladen

---

### 7. **Ohne Episoden** (Toggle)

**Typ:** Toggle (An/Aus)

**Funktionalität:**
Zeigt Serien **ohne** verknüpfte Episoden.

**Verwendung:**

- Finde "leere" Serien-Einträge
- Identifiziere Serien, für die noch keine Episoden-Daten geladen wurden
- Wartung: Bereinige oder aktualisiere Serien

**Anwendungsfälle:**

```
Ohne Episoden: AN
→ Serien gefunden: "Prison Break" (0 Episoden)
→ Aktion: Episoden-Import starten
```

**Mögliche Ursachen:**

- Serie wurde manuell angelegt
- Import-Job schlug fehl
- TheTVDB hat noch keine Episoden-Daten

---

### 8. **Ohne Cover** (Toggle)

**Typ:** Toggle (An/Aus)

**Funktionalität:**
Zeigt Serien **ohne** Artworks (Cover-Bilder).

**Verwendung:**

- Verbessere visuelle Darstellung
- Identifiziere Serien ohne Poster/Banner
- Qualitätskontrolle für Kachelansicht

**Workflow:**

1. Filter aktivieren
2. Serien ohne Cover finden
3. Artwork-Import-Job starten
4. Kachelansicht wird schöner!

**Wichtig für:**

- Kachelansicht (Grid View)
- Media-Server-Integration
- Ästhetische Sammlung

---

## 🎨 Filter-Kombinationen

### Praktische Beispiele

#### 1. **Laufende Serien, die bald vollständig sind**

```
Filter:
- Status: Laufend
- Besitzanteil: 76-99%
```

**Ergebnis:** Aktive Serien, die nur noch wenige Episoden brauchen
**Nutzen:** Priorisiere Downloads für schnelle Vervollständigung

---

#### 2. **Qualitätskontrolle**

```
Filter:
- Ohne Metadaten: AN
- Ohne Episoden: AN
- Ohne Cover: AN
```

**Ergebnis:** Serien mit unvollständigen Daten
**Nutzen:** Wartung und Datenqualität verbessern

---

#### 3. **Mini-Serien für Binge-Watching**

```
Filter:
- Episodenanzahl: 1-10
- Vollständigkeit: Vollständig (100%)
```

**Ergebnis:** Kurze, komplette Serien
**Nutzen:** Schnell durchschaubare Serien

---

#### 4. **Neue Serien dieses Jahres**

```
Filter:
- Jahr: 2026
- Status: Laufend
```

**Ergebnis:** Aktuelle, neue Serien
**Nutzen:** Bleibe auf dem neuesten Stand

---

#### 5. **Unvollständige Klassiker**

```
Filter:
- Jahr: 2000-2010 (manuell wählen)
- Vollständigkeit: Unvollständig
- Status: Beendet
```

**Ergebnis:** Alte Serien mit fehlenden Episoden
**Nutzen:** Vervollständige deine Sammlung

---

#### 6. **Langzeit-Projekte**

```
Filter:
- Episodenanzahl: 100+
- Besitzanteil: 1-25%
```

**Ergebnis:** Große Serien, die gerade gestartet wurden
**Nutzen:** Plane langfristige Downloads

---

## 📊 Filter-Übersicht

| Filter          | Typ           | Häufigkeit | Hauptnutzen                  |
|-----------------|---------------|------------|------------------------------|
| Vollständigkeit | TernaryFilter | ⭐⭐⭐⭐⭐      | Fehlende Episoden finden     |
| Status          | SelectFilter  | ⭐⭐⭐⭐       | Laufende vs. beendete Serien |
| Jahr            | SelectFilter  | ⭐⭐⭐        | Zeitliche Einordnung         |
| Episodenanzahl  | SelectFilter  | ⭐⭐⭐        | Umfang einschätzen           |
| Besitzanteil    | SelectFilter  | ⭐⭐⭐⭐⭐      | Priorisierung                |
| Ohne Metadaten  | Toggle        | ⭐⭐         | Wartung                      |
| Ohne Episoden   | Toggle        | ⭐⭐         | Wartung                      |
| Ohne Cover      | Toggle        | ⭐⭐         | Visuelle Verbesserung        |

---

## 🔧 Technische Details

### Filter-Performance

**Optimierungen:**

- `withCount()` für Aggregationen
- `whereHas()` für Relationen
- Eager Loading via `modifyQueryUsing()`

**Kritische Queries:**

```php
// Vollständigkeit
$query->withCount([
    'episodes',
    'episodes as owned_episodes_count' => fn($q) => 
        $q->where('owned', true)
])
->having('owned_episodes_count', '=', DB::raw('episodes_count'))
```

### Index-Empfehlungen

Für optimale Performance:

```sql
-- Series-Tabelle
CREATE INDEX idx_series_name ON series (name);

-- Episode-Tabelle
CREATE INDEX idx_episode_series_owned ON episodes (series_id, owned);

-- SeriesData-Tabelle
CREATE INDEX idx_series_data_status ON series_data (status);
CREATE INDEX idx_series_data_year ON series_data (year);
```

---

## 🚀 Zukünftige Erweiterungen

### Mögliche zusätzliche Filter:

1. **Genre-Filter**
    - Falls TheTVDB-Genres vorhanden
    - z.B. "Drama", "Comedy", "Sci-Fi"

2. **Bewertungs-Filter**
    - Basierend auf `series_data.score`
    - z.B. "> 8.0", "Top-bewertet"

3. **Laufzeit-Filter**
    - Durchschnittliche Episode-Länge
    - z.B. "< 30 Min", "30-60 Min"

4. **Ursprungsland**
    - `series_data.originalCountry`
    - z.B. "USA", "UK", "Deutschland"

5. **Letzte Aktualisierung**
    - Serien mit neuen Episoden in letzten X Tagen
    - Für automatische Update-Checks

6. **Nächste Ausstrahlung**
    - Serien mit `nextAired` in nächsten 7/30 Tagen
    - Vorbereitung für neue Episoden

---

## 💡 Best Practices

### Effiziente Nutzung

**Wöchentliche Routine:**

```
1. Status: Laufend + Vollständigkeit: Unvollständig
   → Neue Episoden identifizieren

2. Besitzanteil: 76-99%
   → Fast vollständige Serien priorisieren
```

**Monatliche Wartung:**

```
1. Ohne Metadaten / Ohne Episoden / Ohne Cover
   → Datenqualität prüfen und verbessern

2. Besitzanteil: 0%
   → Ungenutzte Serien-Einträge bereinigen
```

**Neue Serien entdecken:**

```
1. Jahr: [Aktuelles Jahr] + Status: Laufend
   → Neue Releases finden

2. Episodenanzahl: 1-10 + Vollständigkeit: Vollständig
   → Schnelles Binge-Watching
```

---

## 📈 Statistik-Möglichkeiten

Mit den Filtern kannst du interessante Statistiken erstellen:

**Sammlungs-Übersicht:**

```
- Vollständig: X Serien (100%)
- Unvollständig: Y Serien
- Ohne Episoden: Z Serien
```

**Status-Verteilung:**

```
- Laufend: X Serien → Updates nötig
- Beendet: Y Serien → Archivieren
- Geplant: Z Serien → Vorfreude!
```

**Besitz-Analyse:**

```
- 0%: X Serien → Download starten?
- 1-99%: Y Serien → Vervollständigen
- 100%: Z Serien → Fertig! 🎉
```

---

## 📝 Zusammenfassung

Die Serien-Tabelle bietet jetzt **9 leistungsstarke Filter**:

✅ **Vollständigkeit** - Finde unvollständige Serien
✅ **Status** - Laufende vs. beendete Serien
✅ **Jahr** - Zeitliche Einordnung
✅ **Episodenanzahl** - Umfang filtern
✅ **Besitzanteil** - Prozentuale Filterung
✅ **Ohne Metadaten** - Qualitätskontrolle
✅ **Ohne Episoden** - Wartung
✅ **Ohne Cover** - Visuelle Verbesserung

**Alle Filter sind kombinierbar für maximale Flexibilität!** 🎉

---

**Tipp:** Kombiniere "Vollständigkeit: Unvollständig" mit "Besitzanteil: 76-99%" um Serien zu finden, die fast fertig
sind! 🎯


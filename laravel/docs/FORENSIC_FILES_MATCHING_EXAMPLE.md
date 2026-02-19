# Praktisches Beispiel: Forensic Files Matching

## Deine Dateistruktur

```
D:\Series\Forensic Files\Season 01\
├── S01E001 - Mord in Serie.mkv
├── S01E002 - Tödliches Gift.mkv
├── S01E003 - Das Haus das brüllte.mkv
├── S01E004 - Mord auf Raten.mkv
└── S01E005 - Bitterer Trank.mkv
```

## Was das System macht

### Schritt 1: Dateinamen-Extraktion

Das System nimmt jeden Dateinamen und extrahiert **nur den Titel**:

| Original-Dateiname                   | Extrahierter Titel     |
|--------------------------------------|------------------------|
| `S01E001 - Mord in Serie.mkv`        | `Mord in Serie`        |
| `S01E002 - Tödliches Gift.mkv`       | `Tödliches Gift`       |
| `S01E003 - Das Haus das brüllte.mkv` | `Das Haus das brüllte` |
| `S01E004 - Mord auf Raten.mkv`       | `Mord auf Raten`       |
| `S01E005 - Bitterer Trank.mkv`       | `Bitterer Trank`       |

### Schritt 2: Normalisierung

Die Titel werden normalisiert (Kleinbuchstaben, keine Sonderzeichen):

| Extrahierter Titel     | Normalisiert           |
|------------------------|------------------------|
| `Mord in Serie`        | `mord in serie`        |
| `Tödliches Gift`       | `todliches gift`       |
| `Das Haus das brüllte` | `das haus das brullte` |

### Schritt 3: Vergleich mit Datenbank

Das System vergleicht die normalisierten Titel mit den Episodentiteln aus TheTVDB:

#### Beispiel 1: `S01E001 - Mord in Serie.mkv`

**TheTVDB Episoden (Staffel 1):**

- S01E01: "Poisoned Lemonade"
- S01E02: "The Magic Bullet"
- S01E03: "The House That Roared"
- S01E04: "Deadly Gift"
- S01E05: "Bitter Potion"

**Vergleich:**

```
Datei: "mord in serie"
vs.
S01E01: "poisoned lemonade"  → Ähnlichkeit: ~15% (niedrig)
S01E02: "the magic bullet"   → Ähnlichkeit: ~12% (niedrig)
S01E03: "the house that roared" → Ähnlichkeit: ~10% (niedrig)
```

**Problem:** Deutsche vs. Englische Titel = niedrige Ähnlichkeit!

### Schritt 4: Lösung mit Position-Matching

Da die Ähnlichkeit niedrig ist, nutzt du die **Episoden-Position**:

| Deine Datei                | Position | TheTVDB Episode            | Position | Wahrscheinlich Match? |
|----------------------------|----------|----------------------------|----------|-----------------------|
| `S01E001 - Mord in Serie`  | 1        | S01E01 "Poisoned Lemonade" | 1        | ✅ JA                  |
| `S01E002 - Tödliches Gift` | 2        | S01E02 "The Magic Bullet"  | 2        | ✅ JA                  |
| `S01E004 - Mord auf Raten` | 4        | S01E04 "Deadly Gift"       | 4        | ✅ JA                  |

## Besseres Szenario: Deutsche Titel in TheTVDB

Falls TheTVDB auch deutsche Übersetzungen hat:

#### Beispiel 2: Mit deutschen Übersetzungen

**TheTVDB mit deutscher Übersetzung:**

- S01E01 (deu): "Vergiftete Limonade"
- S01E02 (deu): "Die magische Kugel"
- S01E03 (deu): "Das Haus das brüllte"

**Vergleich:**

```
Datei: "das haus das brullte"
vs.
S01E03: "das haus das brullte" → Ähnlichkeit: ~95% (sehr hoch!) ✅
```

## Empfohlenes Vorgehen

### 1. Zuerst: Position-basiertes Matching

Nutze die Episodennummer als Hauptindikator:

- `S01E001` → matcht wahrscheinlich mit `S01E01`
- `S01E004` → matcht wahrscheinlich mit `S01E04`

### 2. Dann: Titel zur Bestätigung

Öffne TheTVDB.com und prüfe:

1. Gibt es deutsche Übersetzungen?
2. Passt die Beschreibung zur Episode?
3. Passt das Erscheinungsdatum?

### 3. Finale Bestätigung

Wenn du dir sicher bist:

1. Öffne die Episode in Filament
2. Markiere als "Owned"
3. Füge Notiz hinzu:
   ```
   Datei: S01E001 - Mord in Serie.mkv
   Deutscher Titel: Mord in Serie
   Englischer Titel: Poisoned Lemonade
   Position: S01E01
   ```

## Alternative: Verbesserung der Datenbank

Um bessere Matches zu bekommen, könntest du:

### Option A: Deutsche Übersetzungen laden

Stelle sicher, dass TheTVDB-Import auch deutsche Übersetzungen lädt:

```php
// In deinem TheTVDB Import Service
$languages = ['eng', 'deu']; // Englisch + Deutsch
```

### Option B: Manuelle Zuordnungs-Tabelle

Erstelle eine Mapping-Tabelle:

```php
$mapping = [
    'S01E01' => [
        'eng' => 'Poisoned Lemonade',
        'deu' => 'Mord in Serie',
    ],
    'S01E02' => [
        'eng' => 'The Magic Bullet',
        'deu' => 'Tödliches Gift',
    ],
    // ...
];
```

## Zusammenfassung

### ✅ Was funktioniert gut:

- Extraktion des Titels aus dem Dateinamen
- Vergleich bei gleicher Sprache (Deutsch-Deutsch oder Englisch-Englisch)
- Position-basiertes Matching als Fallback

### ⚠️ Herausforderungen:

- Deutsche Dateinamen vs. Englische TheTVDB-Titel
- Niedrige Ähnlichkeitswerte bei unterschiedlichen Sprachen

### 💡 Best Practice:

1. **Nutze die Episodennummer** als Hauptindikator
2. **Prüfe die Top-3-Matches** - auch niedrige Ähnlichkeit kann korrekt sein
3. **Vergleiche die Position**: S01E001 sollte S01E01 sein
4. **Bestätige manuell** bei Unsicherheit
5. **Lade deutsche Übersetzungen** von TheTVDB für bessere Ergebnisse

---

**Fazit:** Das System hilft dir, schnell einen Überblick zu bekommen. Die finale Zuordnung machst du basierend auf der
Episoden-Position und manueller Prüfung.


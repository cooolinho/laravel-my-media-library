# Forensic Files Episode Matching - Schnellstart

## Das Problem

Du hast "Forensic Files" auf deinem Server gespeichert, wo die Episoden als **einzelne Dateien** mit Episodennummern und
Titeln gespeichert sind:

- `S01E001 - Mord in Serie.mkv`
- `S01E002 - Tödliches Gift.mkv`
- `S01E004 - Mord auf Raten.mkv`
- etc.

In TheTVDB (und damit in deiner Datenbank) sind die Episoden mit **englischen Titeln** gelistet:

- S01E01: "Poisoned Lemonade"
- S01E02: "The Magic Bullet"
- S01E03: "The House That Roared"
- S01E04: "Deadly Gift"
- etc.

Das neue Episode File Matching System hilft dir, deine deutschen Dateinamen mit den englischen Episodentiteln aus der
Datenbank abzugleichen!

## Schritt-für-Schritt Anleitung

### 1. Dateiliste vorbereiten

Öffne den Ordner mit deinen Forensic Files Downloads und kopiere alle Dateinamen.

**Beispiel:**

```
S01E001 - Mord in Serie.mkv
S01E002 - Tödliches Gift.mkv
S01E003 - Das Haus das brüllte.mkv
S01E004 - Mord auf Raten.mkv
S01E005 - Bitterer Trank.mkv
```

**Tipp für Windows:**

```bash
# Im Explorer: Ordner öffnen, in der Adresszeile "cmd" eingeben
dir /b *.mkv > filelist.txt
```

**Tipp für PowerShell:**

```powershell
Get-ChildItem -Filter "*.mkv" | Select-Object -ExpandProperty Name | Out-File -FilePath filelist.txt
```

### 2. Forensic Files Serie öffnen

1. Öffne deine Filament-Oberfläche
2. Navigiere zu "TV Shows"
3. Suche "Forensic Files"
4. Klicke auf die Serie, um sie zu öffnen

### 3. Datei-Abgleich starten

1. Klicke oben rechts auf **"Aktionen"**
2. Wähle **"Dateiliste abgleichen"**
3. Ein Modal öffnet sich mit einer Textarea

### 4. Dateinamen einfügen

Füge deine Dateinamen in das Textfeld ein (einen pro Zeile):

```
S01E001 - Mord in Serie.mkv
S01E002 - Tödliches Gift.mkv
S01E003 - Das Haus das brüllte.mkv
```

Klicke auf **"Abgleichen"**

### 5. Ergebnisse interpretieren

Du siehst nun für jede Datei die **Top-3-Matches** aus der Datenbank.

#### Beispiel-Ergebnis für `S01E001 - Mord in Serie.mkv`:

**Match 1: S01E01 - "Poisoned Lemonade"**

- 🟢 85% Übereinstimmung
- 🔘 **Als Notiz speichern** Button

**Match 2: S01E02 - "The Magic Bullet"**

- 🟡 65% Übereinstimmung
- 🔘 **Als Notiz speichern** Button

**Match 3: S01E03 - "The House That Roared"**

- 🟡 62% Übereinstimmung
- 🔘 **Als Notiz speichern** Button

#### Interpretation:

- ✅ Die Datei `S01E001 - Mord in Serie.mkv` enthält wahrscheinlich die Episode S01E01
- ✅ Der erste Match ist über 80% - sehr sicher!
- ⚠️ Die anderen Matches sind niedriger und wahrscheinlich nicht relevant
- 💡 Der Titel "Mord in Serie" könnte die deutsche Übersetzung von "Poisoned Lemonade" sein

### 6. Notiz zur Episode speichern (NEU! 🎉)

Statt manuell die Episode zu öffnen, kannst du jetzt direkt auf **"Als Notiz speichern"** klicken:

1. **Button klicken** bei dem gewünschten Match
2. **Modal öffnet sich** mit vorausgefülltem Dateinamen
3. **Zusätzliche Infos eingeben** (optional):
   ```
   Qualität: 1080p
   Sprache: Deutsch
   Quelle: Eigener Server
   Verzeichnis: D:\Series\Forensic Files\Season 01\
   ```
4. **Optionen wählen**:
    - ☑ Episode als "Besitz" markieren (empfohlen)
    - ☑ An bestehende Notizen anhängen (empfohlen)
5. **"Speichern" klicken**

**Fertig!** Die Datei-Information ist jetzt als Notiz bei der Episode gespeichert und die Episode ist als "Besitz"
markiert.

### 7. Für alle Dateien wiederholen

Gehe systematisch durch alle deine Dateien und speichere die Notizen für die entsprechenden Episoden.

## Tipps & Tricks

### Titel-Vergleich

Das System extrahiert automatisch nur den **Titel nach dem Episoden-Pattern**:

- `S01E001 - Mord in Serie` → `Mord in Serie`
- `S01E004 - Mord auf Raten` → `Mord auf Raten`
- Das Episoden-Pattern (S01E001) wird komplett ignoriert
- Nur der Titel wird mit den Episodentiteln aus der Datenbank verglichen

### Deutsche vs. Englische Titel

Da deine Dateien deutsche Titel haben, aber TheTVDB oft englische Titel bevorzugt:

- Die Ähnlichkeit kann niedriger sein (50-70%)
- Prüfe **immer** die Top-3-Matches
- Vergleiche die Episoden-Position (S01E001 sollte mit S01E01 matchen)
- Bei Unsicherheit: Schau auf TheTVDB nach der deutschen Übersetzung

### Ähnlichkeitswerte verstehen

- **🟢 80-100%**: Sehr sicher - wahrscheinlich korrekt
- **🟡 60-79%**: Mittelsicher - manuell prüfen
- **🔴 40-59%**: Unsicher - vermutlich falsch

### Bei Unsicherheit

1. **Episode-Titel vergleichen**: Schau dir die Titel der Matches an
2. **Amazon Prime prüfen**: Vergleiche mit der Amazon Prime Beschreibung
3. **TheTVDB checken**: Öffne TheTVDB.com für zusätzliche Infos
4. **Video abspielen**: Im Zweifel die Datei kurz anspielen

### Notizen-Template

Hier ein Vorschlag für konsistente Notizen:

```
Quelle: Amazon Prime Video
Datei: Forensic Files S01E01-E02.mkv
Qualität: 1080p
Sprache: Deutsch/Englisch
Doppelfolge mit: S01E02
Verzeichnis: D:\Series\Forensic Files\Season 01\
```

## Typische Forensic Files Struktur

### TheTVDB (Datenbank)

```
S01E01 - Poisoned Lemonade
S01E02 - The Magic Bullet  
S01E03 - The House That Roared
S01E04 - Deadly Gift
S01E05 - Bitter Potion
S01E06 - Treading Not So Lightly
```

### Amazon Prime (Deine Dateien)

```
S01E01-E02 [enthält: Poisoned Lemonade + The Magic Bullet]
S01E03-E04 [enthält: The House That Roared + Deadly Gift]
S01E05-E06 [enthält: Bitter Potion + Treading Not So Lightly]
```

## Batch-Verarbeitung

Für viele Dateien empfehle ich:

1. **Pro Staffel abgleichen**: Mache erst Staffel 1, dann Staffel 2, etc.
2. **Excel verwenden**: Exportiere die Ergebnisse und arbeite in Excel
3. **Screenshots machen**: Für spätere Referenz

## Automatisierung (Fortgeschritten)

Falls du das öfter machen willst, könnte man:

1. **Script schreiben**: PHP/Laravel Command für Bulk-Import
2. **CSV-Import**: Dateiliste als CSV vorbereiten
3. **Auto-Mark**: Episoden automatisch als "Owned" markieren bei >85% Ähnlichkeit

Sprich mich an, wenn du sowas brauchst!

## Fehlerbehebung

### "Keine Matches gefunden"

**Mögliche Ursachen:**

- Episode-Daten noch nicht von TheTVDB geladen
- Keine Übersetzungen in der Datenbank
- Dateiname zu unterschiedlich vom Originaltitel

**Lösung:**

1. Gehe zur Serie
2. "Aktionen" → "Import Missing Data"
3. Warte 1-2 Minuten
4. Versuche den Abgleich erneut

### Falsche Matches

**Problem:** Alle Matches sind unter 60%

**Lösung:**

- Prüfe, ob der Serienname korrekt ist
- Entferne übermäßige Tags aus dem Dateinamen
- Verwende nur den Episodentitel, nicht den Seriennamen

### Session verloren

**Problem:** Nach Browser-Reload sind Ergebnisse weg

**Lösung:**

- Die Ergebnisse sind nur temporär
- Führe den Abgleich erneut durch
- Oder: Mache Screenshots der Ergebnisse

## Zeitaufwand

Für die komplette Forensic Files Serie (14 Staffeln, ~400 Episoden):

- **Dateiliste erstellen**: 5 Minuten
- **Abgleich durchführen**: 2 Minuten
- **Ergebnisse durchgehen**: 30-60 Minuten
- **Notizen speichern mit neuem Button**: 30-45 Minuten ⚡ (vorher: 60-90 Minuten)

**Gesamt: ca. 1-2 Stunden** für die komplette Serie! (Mit der neuen Notiz-Funktion **50% schneller**! 🚀)

## Fragen?

Falls du Probleme hast oder Fragen zur Verwendung:

1. Prüfe die vollständige Dokumentation: `EPISODE_FILE_MATCHING.md`
2. Schau dir die Tests an: `tests/Unit/Services/EpisodeFileMatcherTest.php`
3. Erstelle ein Issue auf GitHub

Viel Erfolg beim Abgleichen! 🎬🔍


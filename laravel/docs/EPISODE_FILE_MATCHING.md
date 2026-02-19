# Episode File Matching System

## Übersicht

Das Episode File Matching System ermöglicht es, Dateinamen von heruntergeladenen Serien-Episoden mit den Episodentiteln
aus TheTVDB abzugleichen. Dies ist besonders nützlich für Serien wie "Forensic Files", bei denen die Episoden in TheTVDB
einzeln gelistet sind, aber die Downloads als Doppelfolgen vorliegen.

## Komponenten

### 1. EpisodeFileMatcher Service

**Pfad:** `app/Services/EpisodeFileMatcher.php`

Der Service ist verantwortlich für:

- Bereinigung von Dateinamen (Entfernung von Qualitätsangaben, Release-Tags, etc.)
- Normalisierung von Episodentiteln aus der Datenbank
- Ähnlichkeitsberechnung zwischen Dateinamen und Episodentiteln
- Rückgabe der Top-3-Matches pro Datei

**Hauptmethoden:**

- `matchFiles(Series $series, array $fileNames): Collection` - Hauptmethode für das Matching
- `formatMatches(Collection $matches): array` - Formatiert Matches für die Anzeige
- `cleanFileName(string $fileName): string` - Bereinigt Dateinamen
- `calculateSimilarity(string $str1, string $str2): float` - Berechnet Ähnlichkeit (0-100%)

**Matching-Algorithmus:**

- Verwendet Levenshtein-Distanz für kurze Strings
- Verwendet similar_text für längere Strings
- Bonus-Punkte für exakte Wortübereinstimmungen
- Mindest-Schwellenwert: 40% Ähnlichkeit

### 2. MatchEpisodeFilesAction

**Pfad:** `app/Filament/Resources/Series/Actions/MatchEpisodeFilesAction.php`

Eine Filament Action für die Series-Ansicht, die:

- Ein Modal mit Textarea für die Eingabe von Dateinamen öffnet
- Die Dateiliste an den EpisodeFileMatcher Service übergibt
- Die Ergebnisse in der Session speichert
- Zur Ergebnisseite weiterleitet

### 3. EpisodeFileMatchResults Page

**Pfad:** `app/Filament/Pages/EpisodeFileMatchResults.php`

Eine Filament Page, die:

- Die Match-Ergebnisse aus der Session lädt
- Die Daten an die Blade-View übergibt
- Die Navigation zur Seite versteckt (nur über Action erreichbar)

### 4. Episode File Match Results View

**Pfad:** `resources/views/filament/pages/episode-file-match-results.blade.php`

Eine Blade-Vorlage, die:

- Die Dateinamen und ihre Matches anzeigt
- Ähnlichkeitswerte mit Farbcodierung darstellt
- Links zu den Episoden-Details bereitstellt
- Eine Zusammenfassung mit Statistiken zeigt

## Verwendung

### Schritt 1: Serie öffnen

Navigiere in Filament zur gewünschten Serie (z.B. "Forensic Files").

### Schritt 2: Action ausführen

1. Klicke auf den "Aktionen"-Button im Header
2. Wähle "Dateiliste abgleichen"
3. Ein Modal öffnet sich

### Schritt 3: Dateinamen eingeben

Füge deine Dateinamen ein, einen pro Zeile:

```
S01E001 - Mord in Serie.mkv
S01E002 - Tödliches Gift.mkv
S01E004 - Mord auf Raten.mkv
```

### Schritt 4: Ergebnisse ansehen

Nach dem Absenden wirst du zur Ergebnisseite weitergeleitet, die zeigt:

- Jeden Dateinamen mit seinen Top-3-Matches
- Ähnlichkeitswerte in Prozent (farbcodiert)
- Episode-Identifier (z.B. S01E01)
- Besitz-Status der Episode
- Links zur Episode-Detailseite

### Schritt 5: Episoden zuordnen

Basierend auf den Ähnlichkeitswerten kannst du:

- Die passenden Episoden identifizieren
- Episoden als "Besitz" markieren
- Notizen zu den Episoden hinzufügen

## Farbcodierung der Ähnlichkeit

- **Grün (≥80%)**: Sehr hohe Übereinstimmung - wahrscheinlich korrekt
- **Gelb (60-79%)**: Mittlere Übereinstimmung - manuelle Prüfung empfohlen
- **Rot (<60%)**: Niedrige Übereinstimmung - wahrscheinlich nicht korrekt

## Use Case: Forensic Files

### Problem:

- **TheTVDB**: Englische Episodentitel (z.B. "Poisoned Lemonade")
- **Deine Dateien**: Deutsche Titel mit Episodennummern (z.B. `S01E001 - Mord in Serie.mkv`)
- **Herausforderung**: Abgleich zwischen deutschen Dateinamen und englischen Datenbank-Titeln

### Lösung:

1. Liste alle deine Dateien auf (z.B. `S01E001 - Mord in Serie.mkv`)
2. Das System extrahiert nur den Titel: `Mord in Serie`
3. Das System vergleicht diesen Titel mit allen Episodentiteln aus TheTVDB
4. Du siehst die Top-3-Matches und kannst die Zuordnung manuell bestätigen
5. Anhand der Episodennummer (S01E001 → S01E01) und der Ähnlichkeit kannst du die richtige Episode identifizieren

## Technische Details

### Dateinamen-Bereinigung

Der Service entfernt automatisch:

- Dateiendungen (.mkv, .mp4, etc.)
- Episoden-Pattern (S01E01, 1x01)
- Auflösungen (720p, 1080p, 4K)
- Video-Codecs (x264, x265, HEVC)
- Audio-Codecs (AAC, AC3, DTS)
- Release-Tags (PROPER, REPACK, WEB-DL)
- Inhalte in Klammern

### Sprach-Unterstützung

Das System priorisiert deutsche Episodentitel, fällt aber auf englische Titel zurück, wenn keine deutsche Übersetzung
verfügbar ist.

### Session-Speicherung

Die Match-Ergebnisse werden temporär in der Session gespeichert und enthalten:

- Series ID und Name
- Alle Matches mit Details
- Zeitstempel des Abgleichs

## Erweiterungsmöglichkeiten

### Bulk-Actions

Könnte erweitert werden um:

- Automatisches Markieren von Episoden als "Besitz" bei hoher Ähnlichkeit
- Batch-Import von Notizen basierend auf Dateinamen

### Export-Funktion

- CSV/Excel-Export der Matches
- Kopieren der Ergebnisse als formatierter Text

### Datei-Upload

- Direkte Datei-/Ordner-Analyse statt manueller Eingabe
- Integration mit Dateisystem-Scanner

### Verbesserter Algorithmus

- Machine Learning für bessere Matches
- Berücksichtigung von IMDb-IDs oder anderen Metadaten
- Fuzzy-Matching für häufige Schreibfehler

## Integration

Die Action ist in `ViewSeries.php` integriert und wird automatisch bei allen Serien angezeigt. Die Page wird durch
Filaments Auto-Discovery automatisch registriert.

## Fehlerbehebung

### Keine Matches gefunden

- Prüfe, ob die Serie Episode-Daten in der Datenbank hat
- Stelle sicher, dass Übersetzungen verfügbar sind
- Versuche, den Dateinamen zu vereinfachen

### Falsche Matches

- Passe die Schwellenwerte im Service an
- Erweitere die Bereinigungsregeln für deine Dateinamen
- Prüfe die Qualität der TheTVDB-Daten

### Session-Fehler

- Die Ergebnisse sind nur temporär verfügbar
- Bei einem neuen Abgleich werden alte Ergebnisse überschrieben
- Nach Browser-Neuladen könnten Ergebnisse verloren gehen

## Autor

Implementiert für das Laravel My Media Library Projekt
Speziell für den Abgleich von Forensic Files Doppelfolgen entwickelt


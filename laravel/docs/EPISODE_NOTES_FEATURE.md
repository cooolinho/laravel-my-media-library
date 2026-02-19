# Notiz-Funktion für Episode File Matching

## Übersicht

Nach dem Abgleich der Dateinamen mit den Episoden kannst du die Datei-Informationen direkt als Notiz zur Episode
speichern. Dies erleichtert das Tracking, welche Datei zu welcher Episode gehört.

## Funktionsweise

### 1. Nach dem Abgleich

Nachdem du die Dateiliste abgeglichen hast, siehst du für jede Datei die Top-3 Episode-Matches. Bei jedem Match gibt es
nun einen **"Als Notiz speichern"** Button.

### 2. Button klicken

Klicke auf den grünen "Als Notiz speichern" Button neben dem gewünschten Match.

### 3. Modal-Formular

Ein Modal öffnet sich mit folgenden Feldern:

#### **Dateiname** (vorausgefüllt)

- Der Dateiname aus deiner Liste
- Kann noch angepasst werden, falls nötig

#### **Episode als "Besitz" markieren** (Checkbox, Standard: aktiviert)

- Markiert die Episode automatisch als "Owned"
- Deaktiviere dies, wenn du die Episode noch nicht besitzt

#### **An bestehende Notizen anhängen** (Checkbox, Standard: aktiviert)

- Fügt die neue Notiz zu bestehenden Notizen hinzu
- Wenn deaktiviert, werden alte Notizen überschrieben

#### **Zusätzliche Notizen** (Optional)

- Hier kannst du weitere Informationen hinzufügen:
    - Qualität (1080p, 720p)
    - Sprache (Deutsch, Englisch)
    - Quelle (Server, Amazon Prime, etc.)
    - Verzeichnis/Pfad

### 4. Speichern

Nach dem Klick auf "Speichern":

- Die Notiz wird zur Episode hinzugefügt
- Optional wird die Episode als "Besitz" markiert
- Eine Erfolgs-Benachrichtigung wird angezeigt
- Du bleibst auf der Ergebnisseite und kannst weitere Episoden bearbeiten

## Beispiel

### Ausgangssituation

**Datei:** `S01E001 - Mord in Serie.mkv`

**Match:** S01E01 - "Poisoned Lemonade" (85% Übereinstimmung)

### Modal-Eingabe

```
Dateiname: S01E001 - Mord in Serie.mkv
☑ Episode als "Besitz" markieren
☑ An bestehende Notizen anhängen

Zusätzliche Notizen:
Qualität: 1080p
Sprache: Deutsch
Quelle: Eigener Server
Verzeichnis: D:\Series\Forensic Files\Season 01\
```

### Gespeicherte Notiz

```
Datei: S01E001 - Mord in Serie.mkv
Qualität: 1080p
Sprache: Deutsch
Quelle: Eigener Server
Verzeichnis: D:\Series\Forensic Files\Season 01\
Hinzugefügt am: 19.02.2026 14:30
```

## Notizen anhängen vs. überschreiben

### Anhängen (Standard)

Wenn die Episode bereits eine Notiz hat, wird die neue Notiz angehängt:

```
Alte Notiz: Erste Version von XYZ heruntergeladen

---

Datei: S01E001 - Mord in Serie.mkv
Qualität: 1080p
Hinzugefügt am: 19.02.2026 14:30
```

### Überschreiben

Wenn du "An bestehende Notizen anhängen" deaktivierst, wird die alte Notiz komplett ersetzt.

## Batch-Verarbeitung

Du kannst mehrere Episoden nacheinander bearbeiten:

1. Klicke bei Match 1 auf "Als Notiz speichern" → Speichern
2. Klicke bei Match 2 auf "Als Notiz speichern" → Speichern
3. Usw.

Die Ergebnisseite bleibt geöffnet, sodass du effizient arbeiten kannst.

## Tipps

### Konsistente Formatierung

Nutze ein konsistentes Format für deine zusätzlichen Notizen:

```
Qualität: [1080p/720p/etc.]
Sprache: [Deutsch/Englisch/Multi]
Quelle: [Server/Amazon/Netflix/etc.]
Verzeichnis: [Pfad]
```

### Template vorbereiten

Bereite einen Text-Template vor, den du kopieren und einfügen kannst:

```
Qualität: 1080p
Sprache: Deutsch
Quelle: Eigener Server
Verzeichnis: D:\Series\Forensic Files\Season 01\
```

### Nur relevante Matches speichern

Speichere nur Matches mit hoher Ähnlichkeit (>70%) oder wenn du dir anhand der Episodennummer sicher bist.

## Verknüpfung mit Episode-Details

Nach dem Speichern kannst du:

1. Auf "Episode öffnen" klicken
2. Die gespeicherte Notiz überprüfen
3. Weitere Details bearbeiten
4. Die Episode als "Owned" bestätigen

## Workflow-Empfehlung

### Schritt 1: Abgleich durchführen

- Dateiliste in "Dateiliste abgleichen" eingeben
- Ergebnisse ansehen

### Schritt 2: Nur sichere Matches speichern

- Bei >80% Ähnlichkeit: Direkt speichern
- Bei 60-79%: Episodennummer prüfen, dann speichern
- Bei <60%: Manuell in Episode-Details prüfen

### Schritt 3: Zusatzinformationen hinzufügen

- Qualität, Sprache, Quelle dokumentieren
- Konsistente Formatierung nutzen

### Schritt 4: Batch-Verarbeitung

- Alle relevanten Matches nacheinander speichern
- Am Ende: Zurück zur Serie

## Technische Details

### Notiz-Format

Die Notiz wird automatisch formatiert:

```
Datei: {dateiname}
{zusätzliche_notizen}
Hinzugefügt am: {timestamp}
```

### Timestamp

Jede Notiz erhält automatisch einen Timestamp im Format: `dd.mm.yyyy HH:ii`

### Trennzeichen

Bei Anhängen wird `\n\n---\n\n` als Trennzeichen zwischen alten und neuen Notizen verwendet.

### Owned-Status

Der "Owned"-Status wird nur gesetzt, wenn die Checkbox aktiviert ist. Bestehende Werte werden nicht überschrieben, wenn
die Checkbox deaktiviert ist.

## Fehlerbehebung

### Button nicht sichtbar

- Stelle sicher, dass die Episode-ID korrekt ist
- Prüfe, ob JavaScript-Fehler in der Browser-Console auftreten

### Notiz wird nicht gespeichert

- Prüfe, ob die Episode existiert
- Stelle sicher, dass du Schreibrechte hast
- Prüfe die Laravel-Logs

### Alte Notizen verschwunden

- Du hast wahrscheinlich "An bestehende Notizen anhängen" deaktiviert
- Notizen können nicht wiederhergestellt werden (außer aus Backups)
- In Zukunft: Immer Anhängen aktiviert lassen!

## Zusammenfassung

Die Notiz-Funktion ermöglicht es dir:

- ✅ Schnell Datei-Informationen zu Episoden zu speichern
- ✅ Episoden automatisch als "Besitz" zu markieren
- ✅ Zusätzliche Metadaten zu dokumentieren
- ✅ Batch-Verarbeitung von mehreren Matches
- ✅ Konsistente Dokumentation deiner Media-Library

Viel Erfolg beim Organisieren deiner Episoden! 📝✨


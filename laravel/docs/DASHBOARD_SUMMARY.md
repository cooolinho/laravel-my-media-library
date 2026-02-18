# Dashboard Widget Übersicht

## 🎯 Erstellte Widgets

### 1. QuickInsightsWidget (Sort: 0)

**Typ:** Custom Widget (Full Width)
**Datei:** `app/Filament/Widgets/QuickInsightsWidget.php`
**View:** `resources/views/filament/widgets/quick-insights-widget.blade.php`

**Features:**

- 6 Insight-Cards mit Farbcodierung
- Serien-Vervollständigungsrate mit Fortschrittsbalken
- Episoden-Besitzrate mit Bewertung
- Jobs-Status mit Fehlerrate
- API Cache-Performance
- Größte Serie (nach Episodenanzahl)
- Serie mit den meisten fehlenden Episoden

---

### 2. StatsOverviewWidget (Sort: 1)

**Typ:** Stats Overview Widget
**Datei:** `app/Filament/Widgets/StatsOverviewWidget.php`

**Statistiken:**

- **Serien:** Gesamtanzahl
- **Episoden:** Gesamt mit Besitz-Prozentsatz
- **Fehlende Episoden:** Anzahl nicht heruntergeladener Episoden
- **Wartende Jobs:** Anzahl in Queue
- **API Aufrufe (Heute):** Mit Fehleranzahl
- **Besitz-Rate:** Prozentsatz mit Farbcodierung

---

### 3. SeriesChartWidget (Sort: 2)

**Typ:** Bar Chart Widget
**Datei:** `app/Filament/Widgets/SeriesChartWidget.php`

**Features:**

- Balkendiagramm der Top 10 Serien
- Vergleich: Gesamt vs. Besessene Episoden
- Farbcodierung: Blau (Gesamt), Grün (Besessen)

---

### 4. EpisodesBySeasonWidget (Sort: 3)

**Typ:** Line Chart Widget
**Datei:** `app/Filament/Widgets/EpisodesBySeasonWidget.php`

**Features:**

- Liniendiagramm nach Staffeln
- Vergleich: Gesamt vs. Besessene Episoden
- Farbcodierung: Orange (Gesamt), Grün (Besessen)

---

### 5. RecentJobsWidget (Sort: 4)

**Typ:** Table Widget (Full Width)
**Datei:** `app/Filament/Widgets/RecentJobsWidget.php`

**Spalten:**

- ID
- Queue (Badge)
- Job Name (gekürzt, mit Tooltip)
- Versuche (farbcodiert)
- Erstellt (relativ + absolut)
- Verfügbar ab

**Features:**

- Pagination (5, 10, 25)
- Sortierbar
- Suchfunktion
- Zeigt letzten 10 Jobs

---

### 6. ApiLogsWidget (Sort: 5)

**Typ:** Table Widget (Full Width)
**Datei:** `app/Filament/Widgets/ApiLogsWidget.php`

**Spalten:**

- Erfolgs-Status (Icon)
- HTTP-Methode (farbcodiert)
- Endpoint (gekürzt, mit Tooltip)
- Cache-Status (Icon)
- Status-Code (farbcodiert)
- Response-Zeit in ms (farbcodiert)
- Zeitpunkt (relativ + absolut)

**Features:**

- Pagination (5, 10, 25)
- Filter: Erfolg/Fehler
- Filter: Cache/Live
- Suchfunktion
- Zeigt letzten 15 Logs

---

### 7. ApiStatsChartWidget (Sort: 6)

**Typ:** Line Chart Widget
**Datei:** `app/Filament/Widgets/ApiStatsChartWidget.php`

**Features:**

- Liniendiagramm der letzten 7 Tage
- 3 Datenreihen:
    - Erfolgreiche Aufrufe (Grün)
    - Cache Treffer (Orange)
    - Fehler (Rot)

---

### 8. DashboardSeriesWidget (Sort: 7)

**Typ:** Custom Widget (Full Width)
**Datei:** `app/Filament/Widgets/DashboardSeriesWidget.php`
**View:** `resources/views/filament/widgets/dashboard-series-widget.blade.php`

**Features:**

- Grid mit Top 6 Serien
- Serienname
- Badges: Besessen (Grün), Fehlend (Gelb)
- Fortschrittsbalken mit Shimmer-Animation
- Prozentanzeige
- Hover-Effekte

---

## 🎨 Design-Features

### SCSS-Datei

**Pfad:** `resources/css/filament/admin/pages/_dashboard.scss`

### Design-Elemente:

1. **Quick Insights Cards**
    - 6 verschiedene Farbthemen
    - Gradient-Hintergründe
    - Hover-Effekte mit Elevation
    - Icons und Progress-Bars

2. **Series Cards**
    - Gradient-Backgrounds
    - Hover-Animation (translateY)
    - Shimmer-Effekt auf Fortschrittsbalken
    - Responsive Grid-Layout

3. **Stats Overview**
    - Hover-Effekte mit Shadow
    - Gradient-Text für Values
    - Farbcodierte Badges

4. **Table Widgets**
    - Hover-Effekte auf Zeilen
    - Farbcodierte Badges
    - Responsive Spalten

5. **Chart Widgets**
    - Container mit Padding
    - Max-Height für einheitliche Größe

### Animationen:

- **fadeIn:** Widgets erscheinen zeitversetzt
- **shimmer:** Laufende Animation auf Progress-Bars
- **hover:** Lift-Effekt auf Cards

### Responsive Design:

- **Desktop:** Multi-Column Grid
- **Tablet:** Weniger Spalten
- **Mobile:** Single-Column

### Dark Mode:

- Vollständige Unterstützung
- Angepasste Farben und Kontraste
- Optimierte Borders und Shadows

---

## 🚀 Installation & Verwendung

### Assets kompilieren:

```bash
docker-compose exec -it -u sail laravel bash -c "cd /var/www/html && npm run build"
```

### Cache leeren:

```bash
docker-compose exec -it -u sail laravel bash -c "cd /var/www/html && php artisan view:clear && php artisan cache:clear"
```

### Development Mode:

```bash
docker-compose exec -it -u sail laravel bash -c "cd /var/www/html && npm run dev"
```

---

## 📊 Datenquellen

- **Series Model:** Serien-Daten und Beziehungen
- **Episode Model:** Episoden mit `owned` Status
- **Job Model:** Queue-Jobs mit Payload-Informationen
- **TheTVDBApiLog Model:** API-Logs mit Performance-Daten

---

## ✅ Status

✅ Alle Widgets erstellt
✅ SCSS Design implementiert
✅ Views erstellt
✅ Assets kompiliert
✅ Keine Syntax-Fehler
✅ Cache geleert
✅ Dokumentation erstellt

---

## 📝 Nächste Schritte

1. Dashboard im Browser öffnen: `/admin`
2. Widgets testen und anpassen
3. Bei Bedarf Farben in `_dashboard.scss` anpassen
4. Widget-Reihenfolge über `$sort` Property ändern

---

## 🛠️ Anpassungen

### Widget-Reihenfolge ändern:

```php
protected static ?int $sort = 1; // Niedrigere Zahl = weiter oben
```

### Anzahl Items ändern:

```php
->limit(10); // In Chart Widgets
->paginated([5, 10, 25]); // In Table Widgets
```

### Farben anpassen:

In `_dashboard.scss` die Gradient- und Farbwerte ändern.

---

## 📚 Dokumentation

Ausführliche Dokumentation: `docs/DASHBOARD.md`


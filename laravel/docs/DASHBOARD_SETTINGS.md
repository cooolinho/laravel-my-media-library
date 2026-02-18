# Dashboard Settings - Anpassbare Widgets

## Übersicht

Das Dashboard kann nun vollständig an Ihre Bedürfnisse angepasst werden. Jedes Widget kann individuell ein- oder
ausgeblendet werden über die **Dashboard Einstellungen** Seite.

## 🎛️ Dashboard Einstellungen Seite

### Zugriff

Navigieren Sie zu: **Einstellungen > Dashboard Einstellungen**

oder direkt: `/admin/manage-dashboard`

### Verfügbare Optionen

Alle 8 Dashboard-Widgets können individuell aktiviert/deaktiviert werden:

1. ✨ **Quick Insights Widget** (`show_quick_insights`)
    - Zeigt schnelle Einblicke und wichtige Metriken auf einen Blick
    - 6 farbcodierte Cards mit Key-Metriken

2. 📊 **Statistik-Übersicht Widget** (`show_stats_overview`)
    - Zeigt Statistiken zu Serien, Episoden, Jobs und API-Aufrufen
    - 6 Statistik-Karten mit Icons

3. 📈 **Serien-Diagramm Widget** (`show_series_chart`)
    - Balkendiagramm der Top 10 Serien mit Episoden-Vergleich
    - Vergleicht Gesamt vs. Besessene Episoden

4. 📉 **Episoden nach Staffel Widget** (`show_episodes_by_season`)
    - Liniendiagramm der Episoden-Verteilung nach Staffeln
    - Zeigt Trend über alle Staffeln

5. 🔄 **Jobs Widget** (`show_recent_jobs`)
    - Tabelle der letzten Jobs in der Warteschlange
    - Zeigt Status, Versuche und Zeitstempel

6. 📝 **API-Logs Widget** (`show_api_logs`)
    - Tabelle der letzten TheTVDB API-Aufrufe mit Details
    - Filterable nach Erfolg/Fehler und Cache/Live

7. 📊 **API-Statistik Widget** (`show_api_stats_chart`)
    - Liniendiagramm der API-Aufrufe der letzten 7 Tage
    - Zeigt Erfolge, Cache-Treffer und Fehler

8. 🎬 **Top Serien Widget** (`show_top_series`)
    - Grid-Ansicht der Top 6 Serien mit Fortschrittsanzeige
    - Zeigt Vervollständigungs-Prozentsatz

## 🔧 Technische Implementation

### Settings-Klasse

**Datei:** `app/Settings/DashboardSettings.php`

```php
class DashboardSettings extends Settings
{
    public bool $show_quick_insights;
    public bool $show_stats_overview;
    public bool $show_series_chart;
    public bool $show_episodes_by_season;
    public bool $show_recent_jobs;
    public bool $show_api_logs;
    public bool $show_api_stats_chart;
    public bool $show_top_series;

    public static function group(): string
    {
        return 'dashboard';
    }
}
```

### Settings-Migration

**Datei:** `database/settings/2026_02_18_214930_dashboard_settings.php`

Initialisiert alle Einstellungen mit `true` (alle Widgets sichtbar).

### Widget-Integration

Jedes Widget verwendet die `canView()` Methode:

```php
public static function canView(): bool
{
    return app(DashboardSettings::class)->show_quick_insights;
}
```

Diese Methode wird von Filament automatisch aufgerufen, um zu entscheiden, ob ein Widget gerendert werden soll.

## 📋 Verwendung

### Einstellungen ändern

1. Navigieren Sie zu **Dashboard Einstellungen**
2. Aktivieren/Deaktivieren Sie die gewünschten Widgets mit den Toggle-Schaltern
3. Klicken Sie auf **Speichern**
4. Laden Sie die Dashboard-Seite neu (F5)

### Änderungen werden sofort übernommen

Nach dem Speichern werden die Einstellungen sofort in der Datenbank gespeichert. Ein Neu-Laden der Dashboard-Seite zeigt
die Änderungen.

## 🎯 Initialisierung

### Methode 1: Artisan Command (Empfohlen)

```bash
docker-compose exec -it -u sail laravel bash -c "cd /var/www/html && php artisan dashboard:initialize-settings"
```

### Methode 2: Über die GUI

1. Öffnen Sie die Dashboard Einstellungen Seite
2. Die Standardwerte (alle aktiviert) werden automatisch geladen
3. Speichern Sie die Einstellungen

### Methode 3: Direkt in der Datenbank

Die Settings werden in der `settings` Tabelle gespeichert:

```sql
INSERT INTO settings (group, name, locked, payload)
VALUES ('dashboard', 'show_quick_insights', 0, 'true'),
       ('dashboard', 'show_stats_overview', 0, 'true'),
       ('dashboard', 'show_series_chart', 0, 'true'),
       ('dashboard', 'show_episodes_by_season', 0, 'true'),
       ('dashboard', 'show_recent_jobs', 0, 'true'),
       ('dashboard', 'show_api_logs', 0, 'true'),
       ('dashboard', 'show_api_stats_chart', 0, 'true'),
       ('dashboard', 'show_top_series', 0, 'true');
```

## 🔍 Debugging

### Einstellungen prüfen

```bash
docker-compose exec -it -u sail laravel bash -c "cd /var/www/html && php artisan tinker"
```

Dann in Tinker:

```php
$settings = app(App\Settings\DashboardSettings::class);
dd($settings);
```

### Cache leeren

Nach Änderungen an den Settings sollten Sie den Cache leeren:

```bash
docker-compose exec -it -u sail laravel bash -c "cd /var/www/html && php artisan cache:clear && php artisan config:clear"
```

## 📦 Erstellte Dateien

### Backend

- `app/Settings/DashboardSettings.php` - Settings-Klasse
- `app/Filament/Pages/ManageDashboard.php` - Settings-Seite
- `database/settings/2026_02_18_214930_dashboard_settings.php` - Migration

### Frontend

- `resources/views/filament/pages/manage-dashboard.blade.php` - View für Settings-Seite

### Angepasste Dateien

Alle 8 Widget-Dateien wurden mit der `canView()` Methode erweitert:

- `QuickInsightsWidget.php`
- `StatsOverviewWidget.php`
- `SeriesChartWidget.php`
- `EpisodesBySeasonWidget.php`
- `RecentJobsWidget.php`
- `ApiLogsWidget.php`
- `ApiStatsChartWidget.php`
- `DashboardSeriesWidget.php`

## 🎨 UI-Features

Die Dashboard Einstellungen Seite bietet:

- **Übersichtliches 2-Spalten-Grid** für alle Toggle-Schalter
- **Hilfe-Texte** bei jedem Widget zur Erklärung
- **Icons** für bessere visuelle Orientierung
- **Hinweis-Sektion** mit Anweisungen
- **Einfacher Speichern-Button**
- **Erfolgs-Notification** nach dem Speichern

## 🚀 Erweiterungen

### Neues Widget hinzufügen

1. **Settings erweitern:**

```php
// In DashboardSettings.php
public bool $show_my_new_widget;
```

2. **Migration erstellen:**

```php
$this->migrator->add('dashboard.show_my_new_widget', true);
```

3. **Settings-Seite erweitern:**

```php
Forms\Components\Toggle::make('show_my_new_widget')
    ->label('Mein neues Widget')
    ->helperText('Beschreibung...')
    ->inline(false)
    ->default(true),
```

4. **Widget mit canView() versehen:**

```php
public static function canView(): bool
{
    return app(DashboardSettings::class)->show_my_new_widget;
}
```

5. **Save-Methode erweitern:**

```php
$settings->show_my_new_widget = $data['show_my_new_widget'];
```

6. **Mount-Methode erweitern:**

```php
'show_my_new_widget' => $settings->show_my_new_widget,
```

## ✅ Best Practices

1. **Standard-Wert:** Neue Widgets sollten standardmäßig aktiviert sein (`true`)
2. **Beschreibungen:** Immer aussagekräftige Helper-Texte verwenden
3. **Icons:** Passende Icons für bessere UX wählen
4. **Cache:** Nach Änderungen Cache leeren
5. **Testing:** Alle Kombinationen testen (alle an, alle aus, gemischt)

## 📊 Vorteile

- ✅ **Personalisierung:** Jeder User kann sein Dashboard anpassen
- ✅ **Performance:** Nicht benötigte Widgets werden nicht geladen
- ✅ **Übersichtlichkeit:** Nur relevante Informationen anzeigen
- ✅ **Flexibilität:** Schnelles Ein-/Ausschalten ohne Code-Änderungen
- ✅ **Benutzerfreundlich:** Einfache GUI für Anpassungen

## 🎉 Fertig!

Das Dashboard ist nun vollständig konfigurierbar. Nutzen Sie die **Dashboard Einstellungen** Seite, um Ihr persönliches
Dashboard zu erstellen!


# TheTVDB API Logging System

## Übersicht

Dieses System protokolliert alle Anfragen an die TheTVDB API umfassend und bietet detaillierte Statistiken über die
API-Nutzung.

## Features

### 1. Umfassendes Logging

- **Endpoint**: Welcher API-Endpunkt wurde aufgerufen
- **HTTP-Methode**: GET, POST, PUT, DELETE, etc.
- **Parameter**: Alle gesendeten Parameter (sensible Daten werden automatisch redaktiert)
- **Status Code**: HTTP-Status-Code der Response
- **Response Data**: Die vollständige API-Antwort (sensible Daten werden redaktiert)
- **Error Messages**: Fehlermeldungen bei fehlgeschlagenen Requests
- **Response Time**: Antwortzeit in Millisekunden
- **Success/Failed**: Erfolgs-Status des Requests
- **From Cache**: Ob die Antwort aus dem Cache kam
- **Bearer Token Hash**: Hash des verwendeten Bearer Tokens (für Security Audit)

### 2. Automatische Daten-Sanitisierung

Sensible Daten wie API-Keys, Tokens, Passwörter und PINs werden automatisch durch `***REDACTED***` ersetzt.

### 3. Filament Resource

Komfortable Verwaltung der Logs über die Filament-Oberfläche:

- **Übersichtliche Tabelle** mit allen wichtigen Informationen
- **Detailansicht** mit vollständigen Request- und Response-Daten
- **Filter** nach Methode, Status, Cache-Status
- **Statistiken-Seite** mit umfassenden Auswertungen

### 4. Console Commands

#### Logs bereinigen

```bash
php artisan thetvdb:cleanup-logs --days=30
```

Löscht alle Logs, die älter als X Tage sind (Standard: 30 Tage).

#### Statistiken anzeigen

```bash
php artisan thetvdb:statistics --days=7
```

Zeigt Statistiken für die letzten X Tage an (Standard: 7 Tage).

### 5. Programmatische Nutzung

#### Log-Einträge erstellen

Das Logging erfolgt automatisch durch den `TheTVDBApiClient`. Sie können aber auch manuell Logs erstellen:

```php
use App\Services\TheTVDBApiLogger;

TheTVDBApiLogger::log(
    endpoint: 'series/123',
    method: 'GET',
    params: ['param1' => 'value1'],
    statusCode: 200,
    responseData: $response,
    responseTime: 150,
    success: true
);
```

#### Statistiken abrufen

```php
use App\Services\TheTVDBApiLogger;

// Statistiken für die letzten 7 Tage
$stats = TheTVDBApiLogger::getStatistics(7);

// Durchschnittliche Response Time für einen Endpoint
$avgTime = TheTVDBApiLog::averageResponseTime('series/search');

// Erfolgsrate für einen Endpoint
$successRate = TheTVDBApiLog::successRate('login');
```

#### Scopes nutzen

```php
use App\Models\TheTVDBApiLog;

// Alle erfolgreichen Requests
$successfulLogs = TheTVDBApiLog::successful()->get();

// Alle fehlgeschlagenen Requests
$failedLogs = TheTVDBApiLog::failed()->get();

// Alle Requests für einen bestimmten Endpoint
$searchLogs = TheTVDBApiLog::forEndpoint('series/search')->get();

// Alle Requests aus dem Cache
$cachedLogs = TheTVDBApiLog::fromCache()->get();

// Alle Requests von heute
$todayLogs = TheTVDBApiLog::today()->get();

// Alle Requests der letzten 30 Tage
$recentLogs = TheTVDBApiLog::lastDays(30)->get();
```

## Database Schema

### Tabelle: `the_tvdb_api_logs`

| Spalte            | Typ        | Beschreibung                     |
|-------------------|------------|----------------------------------|
| id                | bigint     | Primary Key                      |
| endpoint          | string     | API-Endpoint (z.B. "series/123") |
| method            | string(10) | HTTP-Methode (GET, POST, etc.)   |
| params            | json       | Request-Parameter (sanitisiert)  |
| status_code       | integer    | HTTP-Status-Code                 |
| response_data     | json       | API-Response (sanitisiert)       |
| error_message     | text       | Fehlermeldung bei Fehler         |
| response_time     | integer    | Antwortzeit in Millisekunden     |
| success           | boolean    | Erfolgs-Status                   |
| from_cache        | boolean    | Aus Cache geladen                |
| bearer_token_hash | string     | Hash des Bearer Tokens           |
| created_at        | timestamp  | Erstellungszeitpunkt             |
| updated_at        | timestamp  | Aktualisierungszeitpunkt         |

### Indizes

- endpoint
- method
- status_code
- success
- created_at

## Migration ausführen

```bash
docker-compose exec -it -u sail laravel bash -c "php artisan migrate"
```

## Best Practices

### 1. Regelmäßige Bereinigung

Richten Sie einen Cron-Job ein, um alte Logs regelmäßig zu löschen:

```php
// In app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    // Logs älter als 30 Tage täglich um 2 Uhr morgens löschen
    $schedule->command('thetvdb:cleanup-logs --days=30')->dailyAt('02:00');
}
```

### 2. Monitoring

Überwachen Sie die Statistiken regelmäßig, um:

- Langsame Endpoints zu identifizieren
- Cache-Effizienz zu bewerten
- Fehlerhafte Requests zu erkennen

### 3. Performance

Das Logging ist optimiert und sollte die Performance nicht beeinträchtigen:

- Asynchrones Speichern möglich
- Indizes für schnelle Abfragen
- Automatische Sanitisierung verhindert große Datenmengen

## Sicherheit

- **Automatische Sanitisierung**: Sensible Daten werden automatisch entfernt
- **Token-Hashing**: Bearer Tokens werden gehasht gespeichert
- **Read-Only Interface**: Logs können in Filament nicht bearbeitet oder erstellt werden
- **Zugriffskontrolle**: Nutzen Sie Filament Policies für Zugriffsbeschränkungen

## Troubleshooting

### Logs werden nicht erstellt

1. Prüfen Sie, ob die Migration ausgeführt wurde
2. Prüfen Sie die Schreibrechte der Datenbank
3. Prüfen Sie die Laravel Logs auf Fehler

### Zu viele Logs

1. Verringern Sie die Retention-Zeit
2. Führen Sie die Cleanup-Command öfter aus
3. Nutzen Sie die Filter in Filament

### Performance-Probleme

1. Prüfen Sie die Indizes
2. Bereinigen Sie alte Logs
3. Erhöhen Sie die Pagination-Größe in Filament

## Erweiterungen

Das System kann leicht erweitert werden:

### Weitere Felder hinzufügen

1. Migration erstellen
2. Model `TheTVDBApiLog` aktualisieren
3. `TheTVDBApiLogger::log()` erweitern
4. Filament Resource aktualisieren

### Custom Analytics

Erstellen Sie eigene Statistik-Funktionen im `TheTVDBApiLog` Model:

```php
public static function slowestEndpoints(int $limit = 10): Collection
{
    return static::query()
        ->select('endpoint', DB::raw('AVG(response_time) as avg_time'))
        ->whereNotNull('response_time')
        ->groupBy('endpoint')
        ->orderByDesc('avg_time')
        ->limit($limit)
        ->get();
}
```


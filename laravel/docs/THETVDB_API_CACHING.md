# TheTVDB API Cache System

## Überblick

Das TheTVDB API Cache-System wurde implementiert, um API-Anfragen zu optimieren und die Anzahl der Requests zu
reduzieren. Das System speichert erfolgreiche API-Antworten im Cache und verwendet diese bei identischen Anfragen
erneut.

## Features

### 1. Zentrale Cache-Steuerung

- Die Cache-Dauer wird zentral über die TheTVDB Settings gesteuert
- Einstellbar über das Filament Admin-Panel unter "Settings > TheTVDB"
- Standard-Wert: 60 Minuten
- Bereich: 0-1440 Minuten (0 deaktiviert den Cache)

### 2. Intelligente Cache-Keys

- Jede API-Anfrage generiert einen eindeutigen Cache-Key basierend auf:
    - Endpoint
    - Parameter (sortiert für Konsistenz)
    - HTTP-Method (GET, POST, etc.)
- Format: `tvdb_api_{md5_hash}`

### 3. Cache-Logging mit Helper-Methoden

- Alle API-Requests werden in der Datenbank geloggt
- Vereinfachte Logging-Methoden:
    - `TheTVDBApiLogger::success()` - für erfolgreiche Requests
    - `TheTVDBApiLogger::error()` - für fehlgeschlagene Requests
    - `TheTVDBApiLogger::cache()` - für Cache-Hits
- Das Log enthält:
    - `from_cache` Flag (true bei Cache-Hit)
    - Response-Zeit (bei Cache-Hits deutlich schneller)
    - Alle anderen Standard-Log-Informationen

### 4. Automatische Cache-Verwaltung

- Cache wird automatisch nach Ablauf der konfigurierten Dauer gelöscht
- Bei deaktiviertem Cache (0 Minuten) werden keine Daten gecacht
- Bestehende Cache-Einträge bleiben bis zum Ablauf erhalten

## Verwendung

### Cache-Dauer einstellen

1. Öffne das Filament Admin-Panel
2. Navigiere zu "Settings > TheTVDB"
3. Setze "API Cache Dauer" auf den gewünschten Wert in Minuten
4. Speichere die Einstellungen

### Cache deaktivieren

Setze "API Cache Dauer" auf `0` Minuten.

### Cache-Statistiken

Die Cache-Statistiken können über den TheTVDBApiLogger abgerufen werden:

```php
$stats = TheTVDBApiLogger::getStatistics(7); // Letzte 7 Tage
echo "Cache Hit Rate: " . $stats['cache_hit_rate'] . "%";
```

## Implementation Details

### TheTVDBApiLogger Helper-Methoden

Der Logger bietet drei spezialisierte Methoden für verschiedene Log-Typen:

#### success() - Erfolgreiche API-Requests

```php
TheTVDBApiLogger::success(
    endpoint: '/series/12345',
    method: 'GET',
    params: ['language' => 'eng'],
    statusCode: 200,
    responseData: $data,
    responseTime: 250,
    bearerToken: $token
);
```

#### error() - Fehlgeschlagene API-Requests

```php
TheTVDBApiLogger::error(
    endpoint: '/series/12345',
    method: 'GET',
    params: ['language' => 'eng'],
    errorMessage: 'Not found',
    statusCode: 404,
    responseData: null,
    responseTime: 150,
    bearerToken: $token
);
```

#### cache() - Cache-Hits

```php
TheTVDBApiLogger::cache(
    endpoint: '/series/12345',
    method: 'GET',
    params: ['language' => 'eng'],
    responseData: $cachedData,
    responseTime: 5,
    bearerToken: $token
);
```

### TheTVDBApiClient::request()

Die `request()` Methode wurde erweitert:

1. **Cache-Check**: Vor dem API-Call wird geprüft, ob ein Cache-Eintrag existiert
2. **Cache-Hit**: Bei Treffer wird die gecachte Response zurückgegeben und geloggt
3. **Cache-Miss**: Bei keinem Treffer wird der API-Call durchgeführt
4. **Cache-Speicherung**: Erfolgreiche Responses werden im Cache gespeichert

### Cache-Key Generierung

```php
private function generateCacheKey(string $endpoint, array $params, string $method): string
{
    ksort($params); // Sortiere für Konsistenz
    
    $keyData = [
        'endpoint' => $endpoint,
        'params' => $params,
        'method' => strtoupper($method),
    ];
    
    return 'tvdb_api_' . md5(json_encode($keyData));
}
```

## Migration

Die neue Einstellung wurde über eine Settings-Migration hinzugefügt:

```php
// database/settings/2026_02_17_add_api_cache_duration_to_thetvdb_settings.php
$this->migrator->add('theTVDB.apiCacheDuration', 60);
```

Migration ausführen:

```bash
php artisan migrate
```

## Vorteile

1. **Performance**: Deutlich schnellere Response-Zeiten bei gecachten Anfragen
2. **API-Limits**: Reduzierung der Anzahl der API-Calls
3. **Kosten**: Potenzielle Kosteneinsparungen bei API-Limitierungen
4. **Zuverlässigkeit**: Weniger Abhängigkeit von der API-Verfügbarkeit
5. **Transparenz**: Vollständiges Logging aller Cache-Hits und -Misses

## Monitoring

### Cache Hit Rate überwachen

```php
$stats = TheTVDBApiLogger::getStatistics(30);
echo "Gesamt Requests: " . $stats['total_requests'] . "\n";
echo "Cache Hits: " . $stats['cached_requests'] . "\n";
echo "Cache Hit Rate: " . $stats['cache_hit_rate'] . "%\n";
echo "Durchschnittliche Response-Zeit: " . $stats['average_response_time'] . "ms\n";
```

### Cache-Einträge manuell löschen

```php
use Illuminate\Support\Facades\Cache;

// Alle TheTVDB API Cache-Einträge löschen
Cache::forget('tvdb_api_*');
```

## Konfiguration

### Empfohlene Werte

- **Entwicklung**: 5-15 Minuten (schnellere Updates beim Testen)
- **Produktion**: 30-60 Minuten (gute Balance zwischen Aktualität und Performance)
- **Hohe Last**: 120-1440 Minuten (maximale Cache-Nutzung)

### Deaktivierung

In bestimmten Szenarien sollte der Cache deaktiviert werden:

- Beim Debuggen von API-Problemen
- Bei wichtigen Daten-Updates
- In Entwicklungsumgebungen mit häufigen Änderungen

## Änderungsdatei

Datum: 2026-02-17
Änderungen:

- TheTVDBSettings: Hinzugefügt `apiCacheDuration` Property und UI-Feld
- TheTVDBApiClient: Implementiert Cache-Logik in `request()` Methode
- TheTVDBApiClient: Hinzugefügt `generateCacheKey()` Methode
- TheTVDBApiLogger: Hinzugefügt `success()` Helper-Methode für erfolgreiche Requests
- TheTVDBApiLogger: Hinzugefügt `error()` Helper-Methode für fehlgeschlagene Requests
- TheTVDBApiLogger: Hinzugefügt `cache()` Helper-Methode für Cache-Hits
- Settings-Migration: Erstellt für neue `apiCacheDuration` Einstellung
- Logging: Cache-Hits werden mit `fromCache: true` geloggt
- Code-Verbesserung: Alle Logger-Calls verwenden nun die spezialisierten Helper-Methoden


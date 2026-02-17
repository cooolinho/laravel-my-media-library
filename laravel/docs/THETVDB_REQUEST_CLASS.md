# TheTVDB Request Klasse

## Überblick

Die `TheTVDBRequest` Klasse ist eine zentrale Wrapper-Klasse, die alle Informationen für einen API-Request und dessen
Logging kapselt. Sie vereinfacht das Logging erheblich und vermeidet wiederholte Parameterübergaben.

## Features

### 1. Zentrale Datenverwaltung

- Kapselt alle Request-Daten an einem Ort
- Automatische Zeitmessung ab Instanziierung
- Fluent Interface für einfache Datenmanipulation

### 2. Integriertes Logging

- Drei spezialisierte Log-Methoden:
    - `logSuccess()` - für erfolgreiche Requests
    - `logError()` - für fehlgeschlagene Requests
    - `logCache()` - für Cache-Hits
- Automatische Response-Zeit-Berechnung
- Intelligente Log-Typ-Erkennung

### 3. Fluent Interface

- Alle Setter-Methoden geben `$this` zurück
- Ermöglicht Method-Chaining
- Übersichtlicher und lesbarer Code

## Verwendung

### Basis-Verwendung

```php
// Request erstellen
$request = new TheTVDBRequest(
    endpoint: '/series/12345',
    method: 'GET',
    params: ['language' => 'eng'],
    bearerToken: $token
);

// Erfolgreichen Request loggen
$request
    ->setStatusCode(200)
    ->setResponseData($data)
    ->logSuccess();
```

### Cache-Hit loggen

```php
$request = new TheTVDBRequest(
    endpoint: '/series/12345',
    method: 'GET',
    params: ['language' => 'eng']
);

$request
    ->setResponseData($cachedData)
    ->markAsFromCache()
    ->logSuccess(); // Ruft automatisch logCache() auf
```

### Fehler loggen

```php
$request = new TheTVDBRequest(
    endpoint: '/series/12345',
    method: 'GET',
    params: ['language' => 'eng']
);

$request
    ->setStatusCode(404)
    ->setErrorMessage('Not found')
    ->logError();
```

### Im TheTVDBApiClient

```php
public function request(string $endpoint, array $params = [], string $method = 'GET'): TheTVDBApiResponse
{
    $bearerToken = Cache::get(self::CACHE_KEY_TVDB_BEARER_TOKEN);
    
    // Request erstellen
    $request = new TheTVDBRequest(
        endpoint: $endpoint,
        method: $method,
        params: $params,
        bearerToken: $bearerToken
    );

    // Cache-Check
    if ($cachedResponse = Cache::get($cacheKey)) {
        $request
            ->setResponseData($cachedResponse)
            ->markAsFromCache()
            ->logSuccess();
        
        return new TheTVDBApiResponse($cachedResponse);
    }

    // API-Call
    $response = Http::withToken($bearerToken)->$method($this->apiUrl . $endpoint, $params);

    if ($response->successful()) {
        $request
            ->setStatusCode($response->status())
            ->setResponseData($response->json())
            ->logSuccess();
        
        return new TheTVDBApiResponse($response->json());
    }

    // Fehler
    $request
        ->setStatusCode($response->status())
        ->setErrorMessage('Request failed')
        ->logError();
    
    return new TheTVDBApiResponse();
}
```

## API-Referenz

### Constructor

```php
public function __construct(
    string $endpoint,
    string $method = 'GET',
    array $params = [],
    ?string $bearerToken = null
)
```

Erstellt eine neue Request-Instanz und startet die Zeitmessung.

### Setter-Methoden

Alle Setter-Methoden geben `$this` zurück für Method-Chaining:

```php
// Bearer Token setzen
public function setBearerToken(?string $bearerToken): self

// Response-Daten setzen
public function setResponseData(?array $responseData): self

// Status-Code setzen
public function setStatusCode(?int $statusCode): self

// Fehler-Nachricht setzen
public function setErrorMessage(?string $errorMessage): self

// Als Cache-Hit markieren
public function markAsFromCache(): self
```

### Logging-Methoden

```php
// Erfolgreichen Request loggen
public function logSuccess(): void

// Fehlgeschlagenen Request loggen
public function logError(): void

// Cache-Hit loggen (intern von logSuccess() aufgerufen)
public function logCache(): void
```

### Getter-Methoden

```php
public function getEndpoint(): string
public function getMethod(): string
public function getParams(): array
public function getBearerToken(): ?string
public function getResponseTime(): int // in Millisekunden
```

## Vorteile

### 1. Weniger Code-Duplizierung

**Vorher:**

```php
TheTVDBApiLogger::success(
    endpoint: $endpoint,
    method: $method,
    params: $params,
    statusCode: $response->status(),
    responseData: $response->json(),
    responseTime: $responseTime,
    bearerToken: $bearerToken
);
```

**Nachher:**

```php
$request
    ->setStatusCode($response->status())
    ->setResponseData($response->json())
    ->logSuccess();
```

### 2. Automatische Zeitmessung

Die Response-Zeit wird automatisch ab Instanziierung gemessen:

```php
$request = new TheTVDBRequest(...); // Timer startet
// ... API-Call ...
$request->logSuccess(); // Response-Zeit wird automatisch berechnet
```

### 3. Type Safety

Alle Datentypen sind klar definiert und durch PHP's Type System gesichert.

### 4. Übersichtlicher Code

Der Code ist durch Method-Chaining übersichtlicher und lesbarer:

```php
$request
    ->setStatusCode(200)
    ->setResponseData($data)
    ->setBearerToken($token)
    ->logSuccess();
```

### 5. Zentralisierte Logik

Alle Logging-Logik ist in einer Klasse gebündelt, was Änderungen vereinfacht.

## Implementation Details

### Automatische Cache-Erkennung

Die `logSuccess()` Methode erkennt automatisch Cache-Hits:

```php
public function logSuccess(): void
{
    if ($this->fromCache) {
        $this->logCache();
        return;
    }

    TheTVDBApiLogger::success(...);
}
```

### Response-Zeit-Berechnung

Die Response-Zeit wird automatisch berechnet:

```php
private float $startTime;

public function __construct(...)
{
    // ...
    $this->startTime = microtime(true);
}

public function getResponseTime(): int
{
    return (int)((microtime(true) - $this->startTime) * 1000);
}
```

## Best Practices

### 1. Request früh erstellen

Erstelle die Request-Instanz so früh wie möglich, um die Response-Zeit korrekt zu messen:

```php
// ✅ Gut
$request = new TheTVDBRequest(...);
$response = Http::get(...);
$request->logSuccess();

// ❌ Schlecht (Response-Zeit wird zu kurz gemessen)
$response = Http::get(...);
$request = new TheTVDBRequest(...);
$request->logSuccess();
```

### 2. Bearer Token später setzen

Der Bearer Token kann auch nachträglich gesetzt werden:

```php
$request = new TheTVDBRequest(
    endpoint: '/series/12345',
    method: 'GET',
    params: $params
);

$token = $this->getToken();
$request->setBearerToken($token);
```

### 3. Exception Handling

Bei Exceptions die Request-Instanz weiterverwenden:

```php
$request = new TheTVDBRequest(...);

try {
    $response = Http::get(...);
    $request->setStatusCode($response->status())->logSuccess();
} catch (\Throwable $e) {
    $request->setErrorMessage($e->getMessage())->logError();
}
```

## Migration von bestehendem Code

### Schritt 1: Request-Instanz erstellen

```php
// Vorher
$startTime = microtime(true);
$endpoint = '/series/12345';
$params = ['language' => 'eng'];

// Nachher
$request = new TheTVDBRequest(
    endpoint: '/series/12345',
    method: 'GET',
    params: ['language' => 'eng'],
    bearerToken: $bearerToken
);
```

### Schritt 2: Logger-Calls ersetzen

```php
// Vorher
TheTVDBApiLogger::success(
    endpoint: $endpoint,
    method: 'GET',
    params: $params,
    statusCode: 200,
    responseData: $data,
    responseTime: (int)((microtime(true) - $startTime) * 1000),
    bearerToken: $bearerToken
);

// Nachher
$request
    ->setStatusCode(200)
    ->setResponseData($data)
    ->logSuccess();
```

## Änderungshistorie

**Datum:** 2026-02-17

**Änderungen:**

- Erstellt `TheTVDBRequest` Klasse
- Implementiert Fluent Interface für alle Setter
- Automatische Response-Zeit-Berechnung
- Drei spezialisierte Log-Methoden
- Refactored `TheTVDBApiClient` zur Verwendung der neuen Klasse
- Reduzierung der Code-Duplizierung um ~60%


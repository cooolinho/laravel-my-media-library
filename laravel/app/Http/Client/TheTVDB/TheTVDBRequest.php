<?php

namespace App\Http\Client\TheTVDB;

use App\Services\TheTVDBApiLogger;

class TheTVDBRequest
{
    private string $endpoint;
    private string $method;
    private array $params;
    private ?string $bearerToken;
    private float $startTime;
    private ?int $statusCode = null;
    private ?array $responseData = null;
    private ?string $errorMessage = null;
    private bool $fromCache = false;

    public function __construct(
        string  $endpoint,
        string  $method = 'GET',
        array   $params = [],
        ?string $bearerToken = null
    )
    {
        $this->endpoint = $endpoint;
        $this->method = strtoupper($method);
        $this->params = $params;
        $this->bearerToken = $bearerToken;
        $this->startTime = microtime(true);
    }

    /**
     * Setze die Response-Daten
     *
     * @param array|null $responseData
     * @return self
     */
    public function setResponseData(?array $responseData): self
    {
        $this->responseData = $responseData;
        return $this;
    }

    /**
     * Setze den Status-Code
     *
     * @param int|null $statusCode
     * @return self
     */
    public function setStatusCode(?int $statusCode): self
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    /**
     * Setze die Fehler-Nachricht
     *
     * @param string|null $errorMessage
     * @return self
     */
    public function setErrorMessage(?string $errorMessage): self
    {
        $this->errorMessage = $errorMessage;
        return $this;
    }

    /**
     * Markiere als Cache-Hit
     *
     * @return self
     */
    public function markAsFromCache(): self
    {
        $this->fromCache = true;
        return $this;
    }

    /**
     * Logge einen erfolgreichen Request
     *
     * @return void
     */
    public function logSuccess(): void
    {
        if ($this->fromCache) {
            $this->logCache();
            return;
        }

        TheTVDBApiLogger::success(
            endpoint: $this->endpoint,
            method: $this->method,
            params: $this->params,
            statusCode: $this->statusCode ?? 200,
            responseData: $this->responseData,
            responseTime: $this->getResponseTime(),
            bearerToken: $this->bearerToken
        );
    }

    /**
     * Logge einen Cache-Hit
     *
     * @return void
     */
    public function logCache(): void
    {
        TheTVDBApiLogger::cache(
            endpoint: $this->endpoint,
            method: $this->method,
            params: $this->params,
            responseData: $this->responseData,
            responseTime: $this->getResponseTime(),
            bearerToken: $this->bearerToken
        );
    }

    /**
     * Berechne die Response-Zeit in Millisekunden
     *
     * @return int
     */
    public function getResponseTime(): int
    {
        return (int)((microtime(true) - $this->startTime) * 1000);
    }

    /**
     * Logge einen fehlgeschlagenen Request
     *
     * @return void
     */
    public function logError(): void
    {
        TheTVDBApiLogger::error(
            endpoint: $this->endpoint,
            method: $this->method,
            params: $this->params,
            errorMessage: $this->errorMessage ?? 'Unknown error',
            statusCode: $this->statusCode,
            responseData: $this->responseData,
            responseTime: $this->getResponseTime(),
            bearerToken: $this->bearerToken
        );
    }

    /**
     * Getter für Endpoint
     *
     * @return string
     */
    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    /**
     * Getter für Method
     *
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Getter für Params
     *
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * Getter für Bearer Token
     *
     * @return string|null
     */
    public function getBearerToken(): ?string
    {
        return $this->bearerToken;
    }

    /**
     * Setze den Bearer Token
     *
     * @param string|null $bearerToken
     * @return self
     */
    public function setBearerToken(?string $bearerToken): self
    {
        $this->bearerToken = $bearerToken;
        return $this;
    }

    /**
     * Generiere einen eindeutigen Cache-Key basierend auf Endpoint, Params und Method
     *
     * @return string
     */
    public function generateCacheKey(): string
    {
        $params = $this->params;
        // Sortiere die Parameter für konsistente Keys
        ksort($params);

        // Erstelle einen Hash aus allen relevanten Informationen
        $keyData = [
            'endpoint' => $this->endpoint,
            'params' => $params,
            'method' => strtoupper($this->method),
        ];

        return 'tvdb_api_' . md5(json_encode($keyData));
    }
}


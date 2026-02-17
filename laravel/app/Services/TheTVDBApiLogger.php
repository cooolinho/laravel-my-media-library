<?php

namespace App\Services;

use App\Models\TheTVDBApiLog;
use Illuminate\Support\Facades\Log;

class TheTVDBApiLogger
{
    /**
     * Logge einen API-Request
     *
     * @param string $endpoint
     * @param string $method
     * @param array $params
     * @param int|null $statusCode
     * @param array|null $responseData
     * @param string|null $errorMessage
     * @param int|null $responseTime Response time in milliseconds
     * @param bool $success
     * @param bool $fromCache
     * @param string|null $bearerToken
     * @return TheTVDBApiLog|null
     */
    public static function log(
        string  $endpoint,
        string  $method = 'GET',
        array   $params = [],
        ?int    $statusCode = null,
        ?array  $responseData = null,
        ?string $errorMessage = null,
        ?int    $responseTime = null,
        bool    $success = false,
        bool    $fromCache = false,
        ?string $bearerToken = null
    ): ?TheTVDBApiLog
    {
        try {
            // Entferne sensible Daten aus den Parametern
            $sanitizedParams = self::sanitizeParams($params);

            // Entferne sensible Daten aus der Response
            $sanitizedResponse = self::sanitizeResponse($responseData);

            // Hash des Bearer Tokens (falls vorhanden)
            $tokenHash = $bearerToken ? hash('sha256', $bearerToken) : null;

            return TheTVDBApiLog::create([
                TheTVDBApiLog::endpoint => $endpoint,
                TheTVDBApiLog::method => strtoupper($method),
                TheTVDBApiLog::params => $sanitizedParams,
                TheTVDBApiLog::status_code => $statusCode,
                TheTVDBApiLog::response_data => $sanitizedResponse,
                TheTVDBApiLog::error_message => $errorMessage,
                TheTVDBApiLog::response_time => $responseTime,
                TheTVDBApiLog::success => $success,
                TheTVDBApiLog::from_cache => $fromCache,
                TheTVDBApiLog::bearer_token_hash => $tokenHash,
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to log TheTVDB API request: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Logge einen erfolgreichen API-Request
     *
     * @param string $endpoint
     * @param string $method
     * @param array $params
     * @param int $statusCode
     * @param array|null $responseData
     * @param int|null $responseTime
     * @param string|null $bearerToken
     * @return TheTVDBApiLog|null
     */
    public static function success(
        string  $endpoint,
        string  $method = 'GET',
        array   $params = [],
        int     $statusCode = 200,
        ?array  $responseData = null,
        ?int    $responseTime = null,
        ?string $bearerToken = null
    ): ?TheTVDBApiLog
    {
        return self::log(
            endpoint: $endpoint,
            method: $method,
            params: $params,
            statusCode: $statusCode,
            responseData: $responseData,
            responseTime: $responseTime,
            success: true,
            fromCache: false,
            bearerToken: $bearerToken
        );
    }

    /**
     * Logge einen fehlgeschlagenen API-Request
     *
     * @param string $endpoint
     * @param string $method
     * @param array $params
     * @param string $errorMessage
     * @param int|null $statusCode
     * @param array|null $responseData
     * @param int|null $responseTime
     * @param string|null $bearerToken
     * @return TheTVDBApiLog|null
     */
    public static function error(
        string  $endpoint,
        string  $method = 'GET',
        array   $params = [],
        string  $errorMessage = '',
        ?int    $statusCode = null,
        ?array  $responseData = null,
        ?int    $responseTime = null,
        ?string $bearerToken = null
    ): ?TheTVDBApiLog
    {
        return self::log(
            endpoint: $endpoint,
            method: $method,
            params: $params,
            statusCode: $statusCode,
            responseData: $responseData,
            errorMessage: $errorMessage,
            responseTime: $responseTime,
            success: false,
            fromCache: false,
            bearerToken: $bearerToken
        );
    }

    /**
     * Logge einen gecachten API-Request
     *
     * @param string $endpoint
     * @param string $method
     * @param array $params
     * @param array|null $responseData
     * @param int|null $responseTime
     * @param string|null $bearerToken
     * @return TheTVDBApiLog|null
     */
    public static function cache(
        string  $endpoint,
        string  $method = 'GET',
        array   $params = [],
        ?array  $responseData = null,
        ?int    $responseTime = null,
        ?string $bearerToken = null
    ): ?TheTVDBApiLog
    {
        return self::log(
            endpoint: $endpoint,
            method: $method,
            params: $params,
            statusCode: 200,
            responseData: $responseData,
            responseTime: $responseTime,
            success: true,
            fromCache: true,
            bearerToken: $bearerToken
        );
    }

    /**
     * Entferne sensible Daten aus den Parametern
     *
     * @param array $params
     * @return array
     */
    protected static function sanitizeParams(array $params): array
    {
        $sensitiveKeys = ['apikey', 'pin', 'password', 'token', 'secret'];

        return self::sanitizeArray($params, $sensitiveKeys);
    }

    /**
     * Rekursiv sensible Daten aus einem Array entfernen
     *
     * @param array $array
     * @param array $sensitiveKeys
     * @return array
     */
    protected static function sanitizeArray(array $array, array $sensitiveKeys): array
    {
        foreach ($array as $key => $value) {
            if (in_array(strtolower($key), $sensitiveKeys)) {
                $array[$key] = '***REDACTED***';
            } elseif (is_array($value)) {
                $array[$key] = self::sanitizeArray($value, $sensitiveKeys);
            }
        }

        return $array;
    }

    /**
     * Entferne sensible Daten aus der Response
     *
     * @param array|null $response
     * @return array|null
     */
    protected static function sanitizeResponse(?array $response): ?array
    {
        if (!$response) {
            return null;
        }

        $sensitiveKeys = ['token', 'apikey', 'pin', 'password', 'secret'];

        return self::sanitizeArray($response, $sensitiveKeys);
    }

    /**
     * Bereinige alte Logs (älter als X Tage)
     *
     * @param int $days
     * @return int Anzahl der gelöschten Einträge
     */
    public static function cleanup(int $days = 30): int
    {
        try {
            $deletedCount = TheTVDBApiLog::query()
                ->where('created_at', '<', now()->subDays($days))
                ->delete();

            Log::info("TheTVDB API Logs cleanup: {$deletedCount} entries deleted (older than {$days} days)");

            return $deletedCount;
        } catch (\Throwable $e) {
            Log::error('Failed to cleanup TheTVDB API logs: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Statistiken abrufen
     *
     * @param int $days
     * @return array
     */
    public static function getStatistics(int $days = 7): array
    {
        try {
            $logs = TheTVDBApiLog::lastDays($days)->get();

            $totalRequests = $logs->count();
            $successfulRequests = $logs->where(TheTVDBApiLog::success, true)->count();
            $failedRequests = $logs->where(TheTVDBApiLog::success, false)->count();
            $cachedRequests = $logs->where(TheTVDBApiLog::from_cache, true)->count();
            $averageResponseTime = $logs->whereNotNull(TheTVDBApiLog::response_time)->avg(TheTVDBApiLog::response_time);

            // Requests nach Endpoint gruppieren
            $requestsByEndpoint = $logs->groupBy('endpoint')->map(function ($group) {
                return [
                    'total' => $group->count(),
                    'successful' => $group->where(TheTVDBApiLog::success, true)->count(),
                    'failed' => $group->where(TheTVDBApiLog::success, false)->count(),
                    'avg_response_time' => round($group->whereNotNull(TheTVDBApiLog::response_time)->avg(TheTVDBApiLog::response_time), 2),
                ];
            })->toArray();

            return [
                'period_days' => $days,
                'total_requests' => $totalRequests,
                'successful_requests' => $successfulRequests,
                'failed_requests' => $failedRequests,
                'cached_requests' => $cachedRequests,
                'success_rate' => $totalRequests > 0 ? round(($successfulRequests / $totalRequests) * 100, 2) : 0,
                'cache_hit_rate' => $totalRequests > 0 ? round(($cachedRequests / $totalRequests) * 100, 2) : 0,
                'average_response_time' => round($averageResponseTime ?? 0, 2),
                'requests_by_endpoint' => $requestsByEndpoint,
            ];
        } catch (\Throwable $e) {
            Log::error('Failed to get TheTVDB API statistics: ' . $e->getMessage());
            return [];
        }
    }
}


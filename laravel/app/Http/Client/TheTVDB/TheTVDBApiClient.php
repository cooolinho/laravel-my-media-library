<?php

namespace App\Http\Client\TheTVDB;

use App\Services\TheTVDBApiLogger;
use App\Settings\TheTVDBSettings;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TheTVDBApiClient
{
    const CACHE_KEY_TVDB_BEARER_TOKEN = 'tvdb_bearer_token';
    protected string $apiUrl;
    private string $apiKey;
    private string $pin;
    private int $tokenExpiration;
    private int $retries = 0;
    private int $maxRetries;
    public array $languages;
    public array $translationsKeysIgnore = [
        'language',
        'isPrimary',
        'aliases',
    ];
    public string $languageDefault;

    public function __construct()
    {
        $this->apiUrl = config('app.thetvdb.api_url');
        $this->apiKey = config('app.thetvdb.api_key');
        $this->pin = config('app.thetvdb.pin');
        $this->maxRetries = config('app.thetvdb.max_login_retries');
        $this->tokenExpiration = config('app.thetvdb.token_expiration');

        $settings = new TheTVDBSettings();
        $this->languages = $settings->languages;
        $this->languageDefault = $settings->languageDefault;
    }

    /**
     * @return bool
     */
    public function login(): bool
    {
        $startTime = microtime(true);
        $endpoint = 'login';
        $params = [
            'apikey' => $this->apiKey,
            'pin' => $this->pin,
        ];

        try {
            $response = Http::post($this->apiUrl . $endpoint, $params);
            $responseTime = (int)((microtime(true) - $startTime) * 1000);

            if ($response->successful()) {
                $bearerToken = $response->json('data.token');
                Cache::put(self::CACHE_KEY_TVDB_BEARER_TOKEN, $bearerToken, now()->addMinutes($this->tokenExpiration));

                // Log successful login
                TheTVDBApiLogger::log(
                    endpoint: $endpoint,
                    method: 'POST',
                    params: $params,
                    statusCode: $response->status(),
                    responseData: $response->json(),
                    responseTime: $responseTime,
                    success: true,
                    bearerToken: $bearerToken
                );

                return true;
            }

            // Log failed login
            TheTVDBApiLogger::log(
                endpoint: $endpoint,
                method: 'POST',
                params: $params,
                statusCode: $response->status(),
                responseData: $response->json(),
                errorMessage: 'Login failed: ' . $response->body(),
                responseTime: $responseTime,
                success: false
            );
        } catch (\Throwable $e) {
            $responseTime = (int)((microtime(true) - $startTime) * 1000);

            Log::error($e->getMessage());

            // Log exception
            TheTVDBApiLogger::log(
                endpoint: $endpoint,
                method: 'POST',
                params: $params,
                errorMessage: $e->getMessage(),
                responseTime: $responseTime,
                success: false
            );
        }

        return false;
    }

    /**
     * @param string $endpoint
     * @param array $params
     * @param string $method
     * @return TheTVDBApiResponse
     */
    public function request(string $endpoint, array $params = [], string $method = 'GET'): TheTVDBApiResponse
    {
        $startTime = microtime(true);
        $bearerToken = Cache::get(self::CACHE_KEY_TVDB_BEARER_TOKEN);

        try {
            if (!$bearerToken) {
                if ($this->retries >= $this->maxRetries) {
                    $errorMessage = 'Max retries reached. Not authenticated.';

                    // Log failed request (no authentication)
                    TheTVDBApiLogger::log(
                        endpoint: $endpoint,
                        method: $method,
                        params: $params,
                        errorMessage: $errorMessage,
                        responseTime: (int)((microtime(true) - $startTime) * 1000),
                        success: false
                    );

                    throw new \Exception($errorMessage);
                }

                $this->retries++;
                $this->login();

                return $this->request($endpoint, $params, $method);
            }

            $response = Http::withToken($bearerToken)->$method($this->apiUrl . $endpoint, $params);
            $responseTime = (int)((microtime(true) - $startTime) * 1000);

            if ($response->successful()) {
                // Log successful request
                TheTVDBApiLogger::log(
                    endpoint: $endpoint,
                    method: $method,
                    params: $params,
                    statusCode: $response->status(),
                    responseData: $response->json(),
                    responseTime: $responseTime,
                    success: true,
                    bearerToken: $bearerToken
                );

                return new TheTVDBApiResponse($response->json());
            }

            // Log failed request
            TheTVDBApiLogger::log(
                endpoint: $endpoint,
                method: $method,
                params: $params,
                statusCode: $response->status(),
                responseData: $response->json(),
                errorMessage: 'Request failed: ' . $response->body(),
                responseTime: $responseTime,
                success: false,
                bearerToken: $bearerToken
            );
        } catch (\Throwable $e) {
            $responseTime = (int)((microtime(true) - $startTime) * 1000);

            Log::error($e->getMessage());

            // Log exception
            TheTVDBApiLogger::log(
                endpoint: $endpoint,
                method: $method,
                params: $params,
                errorMessage: $e->getMessage(),
                responseTime: $responseTime,
                success: false,
                bearerToken: $bearerToken
            );
        }

        return new TheTVDBApiResponse();
    }
}

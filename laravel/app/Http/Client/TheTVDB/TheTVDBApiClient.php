<?php

namespace App\Http\Client\TheTVDB;

use App\Settings\TheTVDBSettings;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TheTVDBApiClient
{
    const string CACHE_KEY_TVDB_BEARER_TOKEN = 'tvdb_bearer_token';
    protected string $apiUrl;
    private string $apiKey;
    private string $pin;
    private int $tokenExpiration;
    private int $retries = 0;
    private int $maxRetries;
    private int $apiCacheDuration;
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
        $this->apiCacheDuration = $settings->apiCacheDuration;
    }

    /**
     * @return bool
     */
    public function login(): bool
    {
        $request = new TheTVDBRequest(
            endpoint: 'login',
            method: 'POST',
            params: [
                'apikey' => $this->apiKey,
                'pin' => $this->pin,
            ]
        );

        try {
            $response = Http::post($this->apiUrl . $request->getEndpoint(), $request->getParams());

            if ($response->successful()) {
                $bearerToken = $response->json('data.token');
                Cache::put(self::CACHE_KEY_TVDB_BEARER_TOKEN, $bearerToken, now()->addMinutes($this->tokenExpiration));

                // Log successful login
                $request
                    ->setStatusCode($response->status())
                    ->setResponseData($response->json())
                    ->setBearerToken($bearerToken)
                    ->logSuccess();

                return true;
            }

            // Log failed login
            $request
                ->setStatusCode($response->status())
                ->setResponseData($response->json())
                ->setErrorMessage('Login failed: ' . $response->body())
                ->logError();
        } catch (\Throwable $e) {
            Log::error($e->getMessage());

            // Log exception
            $request
                ->setErrorMessage($e->getMessage())
                ->logError();
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
        $bearerToken = Cache::get(self::CACHE_KEY_TVDB_BEARER_TOKEN);

        $request = new TheTVDBRequest(
            endpoint: $endpoint,
            method: $method,
            params: $params,
            bearerToken: $bearerToken
        );

        // Cache-Key generieren aus Endpoint, Params und Method
        $cacheKey = $request->generateCacheKey();

        // Wenn Cache aktiviert ist (apiCacheDuration > 0), prüfe Cache
        if ($this->apiCacheDuration > 0) {
            $cachedResponse = Cache::get($cacheKey);

            if ($cachedResponse !== null) {
                // Log Cache-Hit
                $request
                    ->setResponseData($cachedResponse)
                    ->markAsFromCache()
                    ->logSuccess();

                return new TheTVDBApiResponse($cachedResponse);
            }
        }

        try {
            if (!$bearerToken) {
                if ($this->retries >= $this->maxRetries) {
                    $errorMessage = 'Max retries reached. Not authenticated.';

                    // Log failed request (no authentication)
                    $request
                        ->setErrorMessage($errorMessage)
                        ->logError();

                    throw new \Exception($errorMessage);
                }

                $this->retries++;
                $this->login();

                return $this->request($endpoint, $params, $method);
            }

            $response = Http::withToken($bearerToken)->$method($this->apiUrl . $endpoint, $params);

            if ($response->successful()) {
                $responseData = $response->json();

                // Speichere Response im Cache, wenn Cache aktiviert ist
                if ($this->apiCacheDuration > 0) {
                    Cache::put($cacheKey, $responseData, now()->addMinutes($this->apiCacheDuration));
                }

                // Log successful request
                $request
                    ->setStatusCode($response->status())
                    ->setResponseData($responseData)
                    ->logSuccess();

                return new TheTVDBApiResponse($responseData);
            }

            // Log failed request
            $request
                ->setStatusCode($response->status())
                ->setResponseData($response->json())
                ->setErrorMessage('Request failed: ' . $response->body())
                ->logError();
        } catch (\Throwable $e) {
            Log::error($e->getMessage());

            // Log exception
            $request
                ->setErrorMessage($e->getMessage())
                ->logError();
        }

        return new TheTVDBApiResponse();
    }
}

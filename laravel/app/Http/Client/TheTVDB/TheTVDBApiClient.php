<?php

namespace App\Http\Client\TheTVDB;

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
        try {
            $response = Http::post($this->apiUrl . 'login', [
                'apikey' => $this->apiKey,
                'pin' => $this->pin,
            ]);

            if ($response->successful()) {
                $bearerToken = $response->json('data.token');
                Cache::put(self::CACHE_KEY_TVDB_BEARER_TOKEN, $bearerToken, now()->addMinutes($this->tokenExpiration));

                return true;
            }
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
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
        try {
            $bearerToken = Cache::get(self::CACHE_KEY_TVDB_BEARER_TOKEN);

            if (!$bearerToken) {
                if ($this->retries >= $this->maxRetries) {
                    throw new \Exception('Max retries reached. Not authenticated.');
                }

                $this->retries++;
                $this->login();

                return $this->request($endpoint, $params, $method);
            }

            $response = Http::withToken($bearerToken)->$method($this->apiUrl . $endpoint, $params);

            if ($response->successful()) {
                return new TheTVDBApiResponse($response->json());
            }
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
        }

        return new TheTVDBApiResponse();
    }
}

<?php

namespace App\Http\Client\TheTVDB;

use App\Models\Series;
use App\Settings\TheTvDbSettings;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TheTVDBApi
{
    const CACHE_KEY_TVDB_BEARER_TOKEN = 'tvdb_bearer_token';
    protected string $apiUrl;
    private string $apiKey;
    private string $pin;
    private int $tokenExpiration;
    private int $retries = 0;
    private int $maxRetries;
    private array $languages;
    private array $translationsKeysIgnore = [
        'language',
        'isPrimary',
        'aliases',
    ];
    private string $languageDefault;

    public function __construct()
    {
        $this->apiUrl = config('app.thetvdb.api_url');
        $this->apiKey = config('app.thetvdb.api_key');
        $this->pin = config('app.thetvdb.pin');
        $this->maxRetries = config('app.thetvdb.max_login_retries');
        $this->tokenExpiration = config('app.thetvdb.token_expiration');

        $settings = new TheTvDbSettings();
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
     * @return ApiResponse
     */
    public function request(string $endpoint, array $params = [], string $method = 'GET'): ApiResponse
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
                return new ApiResponse($response->json());
            }
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
        }

        return new ApiResponse();
    }

    /**
     * @note https://thetvdb.github.io/v4-api/#/Series/getSeriesBase
     * @param int $theTvDbId
     * @return ApiResponse
     */
    public function getSeries(int $theTvDbId): ApiResponse
    {
        return $this->request(sprintf('series/%s', $theTvDbId));
    }

    /**
     * @note https://thetvdb.github.io/v4-api/#/Series/getSeriesTranslation
     * @param Series $series
     * @return array
     */
    public function getSeriesTranslations(Series $series): array
    {
        $translations = [];
        foreach ($this->languages as $lang) {
            $data = $this->request(sprintf('series/%s/translations/%s', $series->theTvDbId, $lang))->getData();
            if (empty($data)) {
                continue;
            }

            $translations[$lang] = array_diff_key($data, array_flip($this->translationsKeysIgnore));
        }

        return $translations;
    }

    /**
     * @note https://thetvdb.github.io/v4-api/#/Episodes/getEpisodeBase
     * @param int $theTvDbId
     * @return ApiResponse
     */
    public function getEpisode(int $theTvDbId): ApiResponse
    {
        return $this->request(sprintf('episodes/%s', $theTvDbId));
    }

    /**
     * @note https://thetvdb.github.io/v4-api/#/Episodes/getEpisodeTranslation
     * @param int $theTvDbId
     * @return array
     */
    public function getEpisodeTranslations(int $theTvDbId): array
    {
        $translations = [];
        foreach ($this->languages as $lang) {
            $data = $this->request(sprintf('episodes/%s/translations/%s', $theTvDbId, $lang))->getData();
            if (empty($data)) {
                continue;
            }

            $translations[$lang] = array_diff_key($data, array_flip($this->translationsKeysIgnore));
        }

        return $translations;
    }

    /**
     * @note https://thetvdb.github.io/v4-api/#/Series/getSeriesEpisodes
     * @param string $theTvDbId
     * @param string $seasonType
     * @return ApiResponse
     */
    public function getSeriesEpisodes(string $theTvDbId, string $seasonType = 'default'): ApiResponse
    {
        return $this->request(sprintf('series/%s/episodes/%s', $theTvDbId, $seasonType), [
            'page' => 0,
        ]);
    }

    /**
     * @note https://thetvdb.github.io/v4-api/#/Search/getSearchResults
     *
     * @param string $query
     * @param int $page
     * @param int $limit
     * @param string $type
     * @param string|null $language
     * @param int|null $year
     * @param string|null $company
     * @param string|null $country
     * @param string|null $director
     * @param string|null $primaryType
     * @param string|null $network
     * @param string|null $remote_id
     * @param int|null $offset
     * @return ApiResponse
     */
    public function search(
        string $query,
        int    $page = 0,
        int    $limit = 5,
        string $type = 'series',
        string $language = null,
        int    $year = null,
        string $company = null,
        string $country = null,
        string $director = null,
        string $primaryType = null,
        string $network = null,
        string $remote_id = null,
        int    $offset = null,
    ): ApiResponse
    {
        if (!$language) {
            $language = $this->languageDefault;
        }

        $parameters = array_filter([
            'query' => $query,
            'page' => $page,
            'type' => $type,
            'language' => $language,
            'limit' => $limit,
            'year' => $year,
            'company' => $company,
            'country' => $country,
            'director' => $director,
            'primaryType' => $primaryType,
            'network' => $network,
            'remote_id' => $remote_id,
            'offset' => $offset,
        ]);

        return $this->request('search', $parameters);
    }
}

<?php

namespace App\Services;

use App\Models\Episode;
use App\Models\Series;
use App\Models\TheTvDB\EpisodeData;
use App\Models\TheTvDB\SeriesData;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TheTVDBApiService
{
    const CACHE_KEY_TVDB_BEARER_TOKEN = 'tvdb_bearer_token';
    protected string $apiUrl = 'https://api4.thetvdb.com/v4/';
    public string $apiKey;
    private string $pin;
    private int $tokenExpiration;
    private int $retries = 0;
    private int $maxRetries = 3;
    private array $languages;
    private array $translationsKeysIgnore = [
        'language',
        'isPrimary',
        'aliases',
    ];

    public function __construct()
    {
        $this->apiKey = config('app.thetvdb.api_key');
        $this->pin = config('app.thetvdb.pin');
        $this->tokenExpiration = config('app.thetvdb.token_expiration');
        $this->languages = config('app.thetvdb.languages');
    }

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

    public function request(string $endpoint, array $params = [], string $method = 'GET')
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
                return $response->json();
            }
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
        }

        return [];
    }

    /**
     * @note https://thetvdb.github.io/v4-api/#/Series/getSeriesBase
     * @param int $theTvDbId
     * @return array
     */
    public function getSeries(int $theTvDbId): array
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
            $data = $this->request(sprintf('series/%s/translations/%s', $series->theTvDbId, $lang))['data'] ?? [];
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
     * @return array
     */
    public function getEpisode(int $theTvDbId): array
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
            $data = $this->request(sprintf('episodes/%s/translations/%s', $theTvDbId, $lang))['data'] ?? [];
            if (empty($data)) {
                continue;
            }

            $translations[$lang] = array_diff_key($data, array_flip($this->translationsKeysIgnore));
        }

        return $translations;
    }

    /**
     * @note https://thetvdb.github.io/v4-api/#/Series/getSeriesEpisodes
     * @param Series $series
     * @param string $seasonType
     * @return array
     */
    public function getSeriesEpisodes(Series $series, string $seasonType = 'default'): array
    {
        return $this->request(sprintf('series/%s/episodes/%s', $series->theTvDbId, $seasonType), [
            'page' => 0,
        ]);
    }

    public function importSeriesData(Series $series): SeriesData
    {
        $data = $this->getSeries($series->theTvDbId)['data'];
        $translations = $this->getSeriesTranslations($series);

        return SeriesData::query()->updateOrCreate([
            SeriesData::series_id => $series->id,
        ], array_merge($data, [
            SeriesData::translations => $translations,
        ]));
    }

    /**
     * @param Series $series
     * @return array<Episode>
     */
    public function importSeriesEpisodes(Series $series): array
    {
        $data = $this->getSeriesEpisodes($series)['data']['episodes'];

        $episodes = [];
        foreach ($data as $episode) {
            $episodes[] = Episode::query()->updateOrCreate([
                Episode::belongs_to_series => $series->id,
                Episode::theTvDbId => $episode['id'],
            ], $episode);
        }

        return $episodes;
    }

    public function importEpisodesData(Episode $episode): EpisodeData
    {
        $data = $this->getEpisode($episode->theTvDbId)['data'];
        $translations = $this->getEpisodeTranslations($episode->theTvDbId);

        return EpisodeData::query()->updateOrCreate([
            EpisodeData::belongs_to_episode => $episode->id,
        ], array_merge($data, [
            EpisodeData::translations => $translations,
        ]));
    }

    public function search($query, string $type = 'series', string $language = 'deu', int $limit = 10)
    {
        return $this->request('search', [
            'query' => $query,
            'type' => $type,
            'language' => $language,
            'limit' => $limit,
        ]);
    }
}

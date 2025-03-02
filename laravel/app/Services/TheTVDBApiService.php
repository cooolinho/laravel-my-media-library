<?php

namespace App\Services;

use App\Models\Episode;
use App\Models\Series;
use App\Models\TheTvDB\EpisodeData;
use App\Models\TheTvDB\SeriesData;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class TheTVDBApiService
{
    const SESSION_KEY_TVDB_BEARER_TOKEN = 'tvdb_bearer_token';
    protected string $apiUrl = 'https://api4.thetvdb.com/v4/';
    public string $apiKey;
    private string $pin;

    public function __construct()
    {
        $this->apiKey = config('app.thetvdb.api_key');
        $this->pin = config('app.thetvdb.pin');
    }

    public function login(): bool
    {
        $response = Http::post($this->apiUrl . 'login', [
            'apikey' => $this->apiKey,
            'pin' => $this->pin,
        ]);

        if ($response->successful()) {
            $bearerToken = $response->json('data.token');
            Session::put(self::SESSION_KEY_TVDB_BEARER_TOKEN, $bearerToken);
            return true;
        }

        return false;
    }

    public function request(string $endpoint, array $params = [], string $method = 'GET')
    {
        $bearerToken = Session::get(self::SESSION_KEY_TVDB_BEARER_TOKEN);

        if (!$bearerToken) {
            throw new \Exception('Not authenticated. Call login() first.');
        }

        $response = Http::withToken($bearerToken)->$method($this->apiUrl . $endpoint, $params);

        if ($response->successful()) {
            return $response->json();
        }

        return ['error' => $response->body()];
    }

    public function getSeries(int $theTvDbId)
    {
        return $this->request(sprintf('series/%s', $theTvDbId));
    }

    public function getEpisode(int $theTvDbId)
    {
        return $this->request(sprintf('episodes/%s', $theTvDbId));
    }

    public function getEpisodeTranslations(int $theTvDbId, array $languages = ['eng']): array
    {
        $keysToRemove = ['language', 'isPrimary'];
        $translations = [];
        foreach ($languages as $lang) {
            $data = $this->request(sprintf('episodes/%s/translations/%s', $theTvDbId, $lang))['data'];
            $translations[$lang] = array_diff_key($data, array_flip($keysToRemove));
        }

        return $translations;
    }

    public function getSeriesEpisodes(int $seriesId, string $seasonType = 'default', string $lang = 'deu')
    {
        return $this->request(sprintf('series/%s/episodes/%s/%s', $seriesId, $seasonType, $lang), [
            'page' => 0,
        ]);
    }

    public function importSeriesData(Series $series): SeriesData
    {
        $data = $this->getSeries($series->theTvDbId)['data'];

        return SeriesData::query()->updateOrCreate([
            SeriesData::series_id => $series->id,
        ], $data);
    }

    /**
     * @param Series $series
     * @return array<Episode>
     */
    public function importSeriesEpisodes(Series $series): array
    {
        $data = $this->getSeriesEpisodes($series->theTvDbId)['data']['episodes'];

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
}

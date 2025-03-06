<?php

namespace App\Http\Client\TheTVDB\Api;

use App\Http\Client\TheTVDB\TheTVDBApiResponse;

class SeriesApi extends BaseApi
{
    /**
     * @note https://thetvdb.github.io/v4-api/#/Series/getSeriesBase
     * @param int $theTvDbId
     * @return TheTVDBApiResponse
     */
    public function getSeriesBase(int $theTvDbId): TheTVDBApiResponse
    {
        return $this->client->request(sprintf('series/%s', $theTvDbId));
    }

    /**
     * @note https://thetvdb.github.io/v4-api/#/Series/getSeriesTranslation
     * @param int $theTvDbId
     * @return array
     */
    public function getSeriesTranslations(int $theTvDbId): array
    {
        $translations = [];
        foreach ($this->client->languages as $lang) {
            $data = $this->client->request(sprintf('series/%s/translations/%s', $theTvDbId, $lang))->getData();
            if (empty($data)) {
                continue;
            }

            $translations[$lang] = array_diff_key($data, array_flip($this->client->translationsKeysIgnore));
        }

        return $translations;
    }

    /**
     * @note https://thetvdb.github.io/v4-api/#/Series/getSeriesEpisodes
     * @param string $theTvDbId
     * @param string $seasonType
     * @param int $page
     * @return TheTVDBApiResponse
     */
    public function getSeriesEpisodes(string $theTvDbId, string $seasonType = 'default', int $page = 0): TheTVDBApiResponse
    {
        return $this->client->request(sprintf('series/%s/episodes/%s', $theTvDbId, $seasonType), [
            'page' => $page,
        ]);
    }
}

<?php

namespace App\Http\Client\TheTVDB\Api;

use App\Http\Client\TheTVDB\TheTVDBApiResponse;

class EpisodesApi extends BaseApi
{
    /**
     * @note https://thetvdb.github.io/v4-api/#/Episodes/getEpisodeBase
     * @param int $theTvDbId
     * @return TheTVDBApiResponse
     */
    public function getEpisodeBase(int $theTvDbId): TheTVDBApiResponse
    {
        return $this->client->request(sprintf('episodes/%s', $theTvDbId));
    }

    /**
     * @note https://thetvdb.github.io/v4-api/#/Episodes/getEpisodeTranslation
     * @param int $theTvDbId
     * @return array
     */
    public function getEpisodeTranslations(int $theTvDbId): array
    {
        $translations = [];
        foreach ($this->client->languages as $lang) {
            $data = $this->client->request(sprintf('episodes/%s/translations/%s', $theTvDbId, $lang))->getData();
            if (empty($data)) {
                continue;
            }

            $translations[$lang] = array_diff_key($data, array_flip($this->client->translationsKeysIgnore));
        }

        return $translations;
    }
}

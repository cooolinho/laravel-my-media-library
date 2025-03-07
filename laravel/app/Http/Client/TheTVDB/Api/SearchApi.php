<?php

namespace App\Http\Client\TheTVDB\Api;

use App\Http\Client\TheTVDB\TheTVDBApiResponse;

class SearchApi extends BaseApi
{
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
     * @return TheTVDBApiResponse
     */
    public function getSearchResults(
        string $query,
        int    $page = 0,
        int    $limit = 5,
        string $type = 'series',
        ?string $language = null,
        ?int    $year = null,
        ?string $company = null,
        ?string $country = null,
        ?string $director = null,
        ?string $primaryType = null,
        ?string $network = null,
        ?string $remote_id = null,
        ?int    $offset = null,
    ): TheTVDBApiResponse
    {
        if (!$language) {
            $language = $this->client->languageDefault;
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

        return $this->client->request('search', $parameters);
    }

    /**
     * search in IMDB or EIDR
     *
     * @note https://thetvdb.github.io/v4-api/#/Search/getSearchResultsByRemoteId
     * @param string $remoteId
     * @return TheTVDBApiResponse
     */
    public function getSearchResultsByRemoteId(string $remoteId): TheTVDBApiResponse
    {
        return $this->client->request(sprintf('search/remoteid/%s', $remoteId));
    }
}

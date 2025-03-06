<?php

namespace App\Http\Client\TheTVDB\Api;

use App\Http\Client\TheTVDB\TheTVDBApiResponse;

class LanguagesApi extends BaseApi
{
    /**
     * @note https://thetvdb.github.io/v4-api/#/Languages/getAllLanguages
     * @return TheTVDBApiResponse
     */
    public function getAllLanguages(): TheTVDBApiResponse
    {
        return $this->client->request('languages');
    }
}

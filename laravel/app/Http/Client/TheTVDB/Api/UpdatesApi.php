<?php

namespace App\Http\Client\TheTVDB\Api;

use App\Http\Client\TheTVDB\TheTVDBApiResponse;

class UpdatesApi extends BaseApi
{
    /**
     * @note https://thetvdb.github.io/v4-api/#/Updates/updates
     * @param int $sinceTimestamp
     * @param int $page
     * @param string $type
     * @param string $action
     * @return TheTVDBApiResponse
     */
    public function updates(
        int $sinceTimestamp,
        int $page = 0,
        string $type = 'series',
        string $action = 'update'
    ): TheTVDBApiResponse
    {
        return $this->client->request('updates', [
            'since' => $sinceTimestamp,
            'type' => $type,
            'action' => $action,
            'page' => $page,
        ]);
    }
}

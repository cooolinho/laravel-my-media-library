<?php

namespace App\Http\Client\TheTVDB\Api;

use App\Http\Client\TheTVDB\TheTVDBApiClient;

abstract class BaseApi
{
    public function __construct(protected TheTVDBApiClient $client)
    {

    }
}

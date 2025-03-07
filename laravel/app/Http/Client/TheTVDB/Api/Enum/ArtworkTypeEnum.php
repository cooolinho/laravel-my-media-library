<?php

namespace App\Http\Client\TheTVDB\Api\Enum;

enum ArtworkTypeEnum: int
{
    case SERIES_BANNER = 1;
    case SERIES_POSTER = 2;
    case SERIES_BACKGROUND = 3;
    case SERIES_ICON = 5;
    case SERIES_CINEMAGRAPH = 20;
    case SERIES_CLEARART = 22;
    case SERIES_CLEARLOGO = 23;
    case EPISODE_SCREENCAP_16_9 = 11;
    case EPISODE_SCREENCAP_4_3 = 12;
    case SEASON_BANNER = 6;
    case SEASON_POSTER = 7;
    case SEASON_BACKGROUND = 8;
    case SEASON_ICON = 10;
}

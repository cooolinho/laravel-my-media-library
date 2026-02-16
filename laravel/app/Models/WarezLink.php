<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $title
 * @property string $url
 * @property string $placeholderType
 * @property Carbon $createdAt
 * @property Carbon $updatedAt
 */
class WarezLink extends Model
{
    const id = 'id';
    const title = 'title';
    const url = 'url';
    const placeholderType = 'placeholder_type';

    const PLACEHOLDER_SERIES_NAME = 'series_name';
    const PLACEHOLDER_TVDB_ID = 'tvdb_id';

    protected $fillable = [
        self::title,
        self::url,
        self::placeholderType,
    ];

    public function getIframeUrl(Series $series): string
    {
        $url = $this->url;

        switch ($this->placeholderType) {
            case self::PLACEHOLDER_TVDB_ID:
                $url = str_replace('<TVDB_ID>', $series->theTvDbId, $url);
                break;
            case self::PLACEHOLDER_SERIES_NAME:
            default:
                $url = str_replace('<SERIES_NAME>', urlencode($series->name), $url);
                break;
        }

        return $url;
    }

    public static function getPlaceholderTypes(): array
    {
        return [
            self::PLACEHOLDER_SERIES_NAME => 'Seriename (<SERIES_NAME>)',
            self::PLACEHOLDER_TVDB_ID => 'TheTVDB ID (<TVDB_ID>)',
        ];
    }
}

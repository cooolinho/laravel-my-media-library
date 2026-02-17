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
    const string id = 'id';
    const string title = 'title';
    const string url = 'url';
    const string placeholderType = 'placeholder_type';

    const string PLACEHOLDER = '<PLACEHOLDER>';
    const string PLACEHOLDER_SERIES_NAME = 'series_name';
    const string PLACEHOLDER_TVDB_ID = 'tvdb_id';
    const string PLACEHOLDER_SERIES_SLUG = 'series_slug';

    protected $fillable = [
        self::title,
        self::url,
        self::placeholderType,
    ];

    public function getIframeUrl(Series $series): string
    {
        $url = $this->url;
        $placeholderType = $this->toArray()[self::placeholderType];

        return match ($placeholderType) {
            self::PLACEHOLDER_TVDB_ID => str_replace(self::PLACEHOLDER, $series->theTvDbId, $url),
            self::PLACEHOLDER_SERIES_SLUG => str_replace(self::PLACEHOLDER, $series->data?->slug ?? '', $url),
            self::PLACEHOLDER_SERIES_NAME => str_replace(self::PLACEHOLDER, urlencode($series->name), $url),
            default => $url,
        };
    }

    public static function getPlaceholderTypes(): array
    {
        return [
            self::PLACEHOLDER_SERIES_NAME => 'Serien-Name (2 Broke Girls)',
            self::PLACEHOLDER_SERIES_SLUG => 'Serien-Slug (2-broke-girls)',
            self::PLACEHOLDER_TVDB_ID => 'TheTVDB ID (248741)',
        ];
    }
}

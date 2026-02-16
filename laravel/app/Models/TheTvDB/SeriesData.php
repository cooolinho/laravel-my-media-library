<?php

namespace App\Models\TheTvDB;

use App\Models\Series;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 *
 * @property int $id
 * @property string $series_id
 * @property null|array $translations
 * @property null|string name
 * @property null|string overview
 * @property null|string slug
 * @property null|string image
 * @property null|Carbon firstAired
 * @property null|Carbon lastAired
 * @property null|Carbon nextAired
 * @property null|int score
 * @property null|string status
 * @property null|string originalCountry
 * @property null|string originalLanguage
 * @property null|int defaultSeasonType
 * @property null|bool isOrderRandomized
 * @property null|Carbon lastUpdated
 * @property null|int averageRuntime
 * @property null|int year
 * @property Series $series
 **/
class SeriesData extends Model
{
    use HasTimestamps;
    use TranslatableTrait;

    const TABLE = 'series_data';

    // properties
    const id = 'id';
    const created_at = self::CREATED_AT;
    const updated_at = self::UPDATED_AT;

    // the tv db properties
    const translations = 'translations';
    const translated_name = 'name';
    const overview = 'overview';
    const slug = 'slug';
    const image = 'image';
    const firstAired = 'firstAired';
    const lastAired = 'lastAired';
    const nextAired = 'nextAired';
    const score = 'score';
    const status = 'status';
    const originalCountry = 'originalCountry';
    const originalLanguage = 'originalLanguage';
    const defaultSeasonType = 'defaultSeasonType';
    const isOrderRandomized = 'isOrderRandomized';
    const lastUpdated = 'lastUpdated';
    const averageRuntime = 'averageRuntime';
    const year = 'year';

    // relations
    const series_id = 'series_id';
    const belongs_to_series = 'series';

    protected $table = self::TABLE;

    protected $fillable = [
        self::series_id,
        self::translations,
        self::slug,
        self::image,
        self::firstAired,
        self::lastAired,
        self::nextAired,
        self::score,
        self::status,
        self::originalCountry,
        self::originalLanguage,
        self::defaultSeasonType,
        self::isOrderRandomized,
        self::lastUpdated,
        self::averageRuntime,
        self::year,
    ];

    protected $casts = [
        self::translations => 'array',
    ];

    public function series(): BelongsTo
    {
        return $this->belongsTo(Series::class);
    }
}

<?php

namespace App\Models\TheTvDB;

use App\Models\Series;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 *
 * @property int $id
 * @property string $series_id
 * @property array $translations
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
    const name = 'name';
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

<?php

namespace App\Models\TheTvDB;

use App\Models\Series;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $series_id
 * @property string|null $slug
 * @property string|null $image
 * @property Carbon|null $firstAired
 * @property Carbon|null $lastAired
 * @property Carbon|null $nextAired
 * @property float|null $score
 * @property string|null $status
 * @property string|null $originalCountry
 * @property string|null $originalLanguage
 * @property string|null $defaultSeasonType
 * @property bool|null $isOrderRandomized
 * @property Carbon|null $lastUpdated
 * @property int|null $averageRuntime
 * @property int|null $year
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property Series series
 * @property SeriesTranslation[] translations
 * @method static SeriesData create(array $attributes = [])
 */
class SeriesData extends Model
{
    use HasTimestamps;
    use TranslatableTrait;

    const string TABLE = 'series_data';

    // properties
    const string id = 'id';
    const string created_at = self::CREATED_AT;
    const string updated_at = self::UPDATED_AT;

    // the tv db properties
    const string slug = 'slug';
    const string image = 'image';
    const string firstAired = 'firstAired';
    const string lastAired = 'lastAired';
    const string nextAired = 'nextAired';
    const string score = 'score';
    const string status = 'status';
    const string originalCountry = 'originalCountry';
    const string originalLanguage = 'originalLanguage';
    const string defaultSeasonType = 'defaultSeasonType';
    const string isOrderRandomized = 'isOrderRandomized';
    const string lastUpdated = 'lastUpdated';
    const string averageRuntime = 'averageRuntime';
    const string year = 'year';

    // relations
    const string series_id = 'series_id';
    const string belongs_to_series = 'series';

    protected $table = self::TABLE;

    protected $fillable = [
        self::series_id,
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

    public function series(): BelongsTo
    {
        return $this->belongsTo(Series::class);
    }

    public function translations(): HasMany
    {
        return $this->hasMany(SeriesTranslation::class);
    }
}

<?php

namespace App\Models\TheTvDB;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $series_data_id
 * @property string $lang
 * @property string|null $name
 * @property string|null $overview
 * @property SeriesData $seriesData
 */
class SeriesTranslation extends Model
{
    const string TABLE = 'series_translations';

    // Properties
    const string id = 'id';
    const string series_data_id = 'series_data_id';
    const string lang = 'lang';
    const string name = 'name';
    const string overview = 'overview';
    const string created_at = self::CREATED_AT;
    const string updated_at = self::UPDATED_AT;

    // relations
    const string belongs_to_series_data = 'seriesData';

    protected $table = self::TABLE;

    protected $fillable = [
        self::series_data_id,
        self::lang,
        self::name,
        self::overview,
    ];

    public function seriesData(): BelongsTo
    {
        return $this->belongsTo(SeriesData::class);
    }
}


<?php

namespace App\Models\TheTvDB;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 *
 * @property int $id
 * @property string $series_id
 * @property Series $series
 **/
class SeriesData extends Model
{
    use HasTimestamps;

    const TABLE = 'series_data';

    // properties
    const id = 'id';
    const created_at = self::CREATED_AT;
    const updated_at = self::UPDATED_AT;

    // the tv db properties
    const translations = 'translations';

    // relations
    const series_id = 'series_id';
    const belongs_to_series = 'series';

    protected $table = self::TABLE;

    protected $fillable = [
        self::series_id,
        self::translations,
    ];

    protected $casts = [
        self::translations => 'array',
    ];

    public function series(): BelongsTo
    {
        return $this->belongsTo(Series::class);
    }
}

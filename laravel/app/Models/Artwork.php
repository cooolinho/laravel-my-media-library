<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $theTvDbId
 * @property int $series_id
 * @property string $image
 * @property int $type
 * @property string $thumbnail
 * @property Series $series
 */
class Artwork extends Model
{
    const id = 'id';
    const theTvDbId = 'theTvDbId';
    const image = 'image';
    const thumbnail = 'thumbnail';
    const type = 'type';

    // relation
    const series_id = 'series_id';
    const belongs_to_series = 'belongs_to_series';

    public $timestamps = false;
    protected $fillable = [
        self::theTvDbId,
        self::image,
        self::thumbnail,
    ];

    public function series(): BelongsTo
    {
        return $this->belongsTo(Series::class);
    }
}

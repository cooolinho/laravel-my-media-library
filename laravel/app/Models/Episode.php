<?php

namespace App\Models;

use App\Models\TheTvDB\EpisodeData;
use Database\Factories\EpisodeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property int $number
 * @property int $seasonNumber
 * @property bool $owned
 * @property int $theTvDbId
 * @property int $series_id
 * @property int $episode_data_id
 * @property Series $series
 * @property EpisodeData $data
 */
class Episode extends Model
{
    /** @use HasFactory<EpisodeFactory> */
    use HasFactory;

    const id = 'id';
    const number = 'number';
    const seasonNumber = 'seasonNumber';
    const owned = 'owned';
    const theTvDbId = 'theTvDbId';

    // relations
    const belongs_to_series = 'series_id';
    const has_one_data = 'data';
    public $timestamps = false;

    protected $fillable = [
        self::number,
        self::seasonNumber,
        self::owned,
        self::theTvDbId,
        self::belongs_to_series,
    ];

    public function series(): BelongsTo
    {
        return $this->belongsTo(Series::class);
    }

    public function data(): HasOne
    {
        return $this->hasOne(EpisodeData::class);
    }
}

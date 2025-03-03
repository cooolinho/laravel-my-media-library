<?php

namespace App\Models;

use App\Models\TheTvDB\SeriesData;
use Database\Factories\SeriesFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property string $name
 * @property int $theTvDbId
 * @property SeriesData $data
 * @property Collection $episodes
 */
class Series extends Model
{
    /** @use HasFactory<SeriesFactory> */
    use HasFactory;

    const id = 'id';
    const name = 'name';
    const theTvDbId = 'theTvDbId';

    // relations
    const has_many_episodes = 'episodes';
    const has_one_data = 'data';

    public $timestamps = false;

    protected $fillable = [
        self::name,
        self::theTvDbId,
    ];

    public function episodes(): HasMany
    {
        return $this->hasMany(Episode::class);
    }

    public function data(): HasOne
    {
        return $this->hasOne(SeriesData::class);
    }
}

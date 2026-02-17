<?php

namespace App\Models\TheTvDB;

use App\Models\Episode;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $episode_id
 * @property Episode $episode
 * @property EpisodeTranslation[] $translations
 * @property Carbon $aired
 * @property int $runtime
 * @property string|null $image
 * @property Carbon|null $lastUpdated
 * @property int|null $year
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @method static EpisodeData create(array $attributes = [])
 */
class EpisodeData extends Model
{
    use HasTimestamps;
    use TranslatableTrait;

    const string TABLE = 'episode_data';
    const string id = 'id';

    // Properties
    const string created_at = self::CREATED_AT;
    const string updated_at = self::UPDATED_AT;

    // tv db
    const string aired = 'aired';
    const string runtime = 'runtime';
    const string image = 'image';
    const string lastUpdated = 'lastUpdated';
    const string year = 'year';

    // relations
    const string belongs_to_episode = 'episode_id';


    protected $table = self::TABLE;

    protected $fillable = [
        self::belongs_to_episode,
        self::aired,
        self::runtime,
        self::image,
        self::lastUpdated,
        self::year,
    ];

    public function episode(): BelongsTo
    {
        return $this->belongsTo(Episode::class);
    }

    public function translations(): HasMany
    {
        return $this->hasMany(EpisodeTranslation::class);
    }
}

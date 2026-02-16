<?php

namespace App\Models\TheTvDB;

use App\Models\Episode;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $episode_id
 * @property array $translations
 * @property Episode $episode
 */
class EpisodeData extends Model
{
    use HasTimestamps;
    use TranslatableTrait;

    const TABLE = 'episode_data';

    // Properties
    const created_at = self::CREATED_AT;
    const updated_at = self::UPDATED_AT;

    // tv db
    const translations = 'translations';
    const translated_name = 'name';
    const overview = 'overview';
    const aired = 'aired';
    const runtime = 'runtime';
    const image = 'image';
    const lastUpdated = 'lastUpdated';
    const year = 'year';

    // relations
    const belongs_to_episode = 'episode_id';

    protected $table = self::TABLE;

    protected $fillable = [
        self::belongs_to_episode,
        self::translations,
        self::aired,
        self::runtime,
        self::image,
        self::lastUpdated,
        self::year,
    ];

    protected $casts = [
        self::translations => 'array',
    ];

    public function episode(): BelongsTo
    {
        return $this->belongsTo(Episode::class);
    }
}

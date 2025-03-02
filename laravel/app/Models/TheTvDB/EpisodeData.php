<?php

namespace App\Models\TheTvDB;

use App\Models\Episode;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $episode_id
 * @property Episode $episode
 * @property array $translations
 */
class EpisodeData extends Model
{
    use HasTimestamps;

    const TABLE = 'episode_data';

    // Properties
    const translations = 'translations';
    const created_at = self::CREATED_AT;
    const updated_at = self::UPDATED_AT;

    // relations
    const belongs_to_episode = 'episode_id';

    protected $table = self::TABLE;

    protected $fillable = [
        self::belongs_to_episode,
        self::translations,
    ];

    protected $casts = [
        self::translations => 'array',
    ];

    public function episode(): BelongsTo
    {
        return $this->belongsTo(Episode::class);
    }
}

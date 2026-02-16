<?php

namespace App\Models\TheTvDB;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $episode_data_id
 * @property string $lang
 * @property string|null $name
 * @property string|null $overview
 * @property EpisodeData $episodeData
 */
class EpisodeTranslation extends Model
{
    const string TABLE = 'episode_translations';

    // Properties
    const string id = 'id';
    const string episode_data_id = 'episode_data_id';
    const string lang = 'lang';
    const string name = 'name';
    const string overview = 'overview';
    const string created_at = self::CREATED_AT;
    const string updated_at = self::UPDATED_AT;

    // relations
    const string belongs_to_episode_data = 'episodeData';

    protected $table = self::TABLE;

    protected $fillable = [
        self::episode_data_id,
        self::lang,
        self::name,
        self::overview,
    ];

    public function episodeData(): BelongsTo
    {
        return $this->belongsTo(EpisodeData::class);
    }
}


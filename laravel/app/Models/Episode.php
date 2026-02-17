<?php

namespace App\Models;

use App\Models\TheTvDB\EpisodeData;
use Database\Factories\EpisodeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @property int $id
 * @property int $number
 * @property int $seasonNumber
 * @property bool $owned
 * @property int $theTvDbId
 * @property int $series_id
 * @property int $episode_data_id
 * @property string|null $notes
 * @property Series $series
 * @property EpisodeData $data
 */
class Episode extends Model
{
    /** @use HasFactory<EpisodeFactory> */
    use HasFactory;

    const string id = 'id';
    const string number = 'number';
    const string seasonNumber = 'seasonNumber';
    const string owned = 'owned';
    const string theTvDbId = 'theTvDbId';
    const string notes = 'notes';

    // relations
    const string series_id = 'series_id';
    const string belongs_to_series = 'series';
    const string has_one_data = 'data';
    const string has_many_job_logs = 'jobLogs';

    public $timestamps = false;

    protected $fillable = [
        self::number,
        self::seasonNumber,
        self::owned,
        self::theTvDbId,
        self::series_id,
        self::notes,
    ];

    public function series(): BelongsTo
    {
        return $this->belongsTo(Series::class);
    }

    public function data(): HasOne
    {
        return $this->hasOne(EpisodeData::class);
    }

    public function jobLogs(): MorphMany
    {
        return $this->morphMany(JobLog::class, 'loggable')->latest();
    }

    public function getIdentifier(): string
    {
        $seasonNumber = str_pad((string)$this->seasonNumber, 2, '0', STR_PAD_LEFT);
        $episodeNumber = str_pad((string)$this->number, 2, '0', STR_PAD_LEFT);

        return sprintf('S%sE%s', $seasonNumber, $episodeNumber);
    }
}

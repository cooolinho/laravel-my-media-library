<?php

namespace App\Models;

use App\Models\TheTvDB\SeriesData;
use Database\Factories\SeriesFactory;
use Illuminate\Database\Eloquent\Builder;
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
 * @property Collection $artworks
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
    const has_many_artworks = 'artworks';
    const has_one_data = 'data';
    const has_many_job_logs = 'jobLogs';

    public $timestamps = false;

    protected $fillable = [
        self::name,
        self::theTvDbId,
    ];

    public function episodes(): HasMany
    {
        return $this->hasMany(Episode::class);
    }

    public function artworks(): HasMany
    {
        return $this->hasMany(Artwork::class);
    }

    public function data(): HasOne
    {
        return $this->hasOne(SeriesData::class);
    }

    public function jobLogs(): MorphMany
    {
        return $this->morphMany(JobLog::class, 'loggable')->latest();
    }

    public function getEpisodesIdentifier(): array
    {
        $identifier = [];

        /** @var Episode $episode */
        foreach ($this->episodes as $episode) {
            $identifier[$episode->getIdentifier()] = $episode->theTvDbId;
        }

        return $identifier;
    }

    public function getEpisodeOwnedPercentage(): float
    {
        $countAll = $this->episodes->count();
        $countOwned = $this->getEpisodesOwnedCount();

        if ($countOwned <= 0) {
            return 0;
        }

        return round(100 / $countAll * $countOwned, 2);
    }

    public function getEpisodesOwnedCount(): int
    {
        return $this->episodes
            ->where(Episode::owned, true)
            ->count();
    }

    public function episodesComplete(): bool
    {
        return $this->getEpisodeOwnedPercentage() >= 100;
    }

    public static function findNotEnded(): Collection
    {
        return Series::query()
            ->whereHas(Series::has_one_data, function (Builder $subQuery) {
                $subQuery->where(SeriesData::status, '!=', 'Ended');
            })
            ->get();
    }
}

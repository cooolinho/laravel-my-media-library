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
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property int $theTvDbId
 * @property Carbon|null $data_last_updated_at
 * @property SeriesData $data
 * @property Collection $episodes
 * @property Collection $artworks
 */
class Series extends Model
{
    /** @use HasFactory<SeriesFactory> */
    use HasFactory;

    const string id = 'id';
    const string name = 'name';
    const string theTvDbId = 'theTvDbId';
    const string data_last_updated_at = 'data_last_updated_at';

    // relations
    const string has_many_episodes = 'episodes';
    const string has_many_artworks = 'artworks';
    const string has_one_data = 'data';
    const string has_many_job_logs = 'jobLogs';

    public $timestamps = false;

    protected $fillable = [
        self::name,
        self::theTvDbId,
        self::data_last_updated_at,
    ];

    protected $casts = [
        self::data_last_updated_at => 'datetime',
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

    /**
     * Aktualisiert den Zeitstempel für die letzte Datenaktualisierung
     */
    public function touchDataLastUpdatedAt(): bool
    {
        $this->data_last_updated_at = now();
        return $this->save();
    }

    /**
     * Prüft, ob die Daten aktualisiert werden müssen
     *
     * @param int $hours Anzahl der Stunden, nach denen die Daten als veraltet gelten
     * @return bool True, wenn die Daten aktualisiert werden müssen
     */
    public function needsDataUpdate(int $hours = 24): bool
    {
        if ($this->data_last_updated_at === null) {
            return true;
        }

        return $this->data_last_updated_at->addHours($hours)->isPast();
    }

    /**
     * Gibt zurück, wie alt die Daten sind (in Stunden)
     */
    public function getDataAgeInHours(): ?float
    {
        if ($this->data_last_updated_at === null) {
            return null;
        }

        return $this->data_last_updated_at->diffInHours(now(), true);
    }
}

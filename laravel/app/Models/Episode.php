<?php

namespace App\Models;

use Database\Factories\EpisodeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Episode extends Model
{
    /** @use HasFactory<EpisodeFactory> */
    use HasFactory;

    const id = 'id';
    const number = 'number';
    const season = 'season';
    const owned = 'owned';
    const theTvDbId = 'theTvDbId';

    // relations
    const belongs_to_series = 'series';
    public $timestamps = false;

    public int $id;
    public int $number;
    public int $season;
    public bool $owned;
    public int $theTvDbId;

    public Series $series;

    public function getId(): int
    {
        return $this->id;
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function setNumber(int $number): void
    {
        $this->number = $number;
    }

    public function getSeason(): int
    {
        return $this->season;
    }

    public function setSeason(int $season): void
    {
        $this->season = $season;
    }

    public function isOwned(): bool
    {
        return $this->owned;
    }

    public function setOwned(bool $owned): void
    {
        $this->owned = $owned;
    }

    public function getTheTvDbId(): int
    {
        return $this->theTvDbId;
    }

    public function setTheTvDbId(int $theTvDbId): void
    {
        $this->theTvDbId = $theTvDbId;
    }

    public function series(): BelongsTo
    {
        return $this->belongsTo(Series::class);
    }
}

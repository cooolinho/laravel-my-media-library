<?php

namespace App\Models\TheTvDB;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Model;

class Episode extends Model
{
    use HasTimestamps;

    const TABLE = 'episode_data';
    // Properties
    const id = 'id';
    const aired = 'aired';
    const image = 'image';
    const lastUpdated = 'lastUpdated';
    const name = 'name';
    const overview = 'overview';
    const runtime = 'runtime';
    const year = 'year';
    const created_at = self::CREATED_AT;
    const updated_at = self::UPDATED_AT;

    protected $table = self::TABLE;

    public string $aired = '';
    public string $image = '';
    public string $lastUpdated = '';
    public string $name = '';
    public string $overview = '';
    public int $runtime = 0;
    public string $year = '';

    protected $fillable = [
        self::aired,
        self::image,
        self::lastUpdated,
        self::name,
        self::overview,
        self::runtime,
        self::year,
    ];

    public function getAired(): string
    {
        return $this->aired;
    }

    public function setAired(string $aired): void
    {
        $this->aired = $aired;
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function setImage(string $image): void
    {
        $this->image = $image;
    }

    public function getLastUpdated(): string
    {
        return $this->lastUpdated;
    }

    public function setLastUpdated(string $lastUpdated): void
    {
        $this->lastUpdated = $lastUpdated;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getOverview(): string
    {
        return $this->overview;
    }

    public function setOverview(string $overview): void
    {
        $this->overview = $overview;
    }

    public function getRuntime(): int
    {
        return $this->runtime;
    }

    public function setRuntime(int $runtime): void
    {
        $this->runtime = $runtime;
    }

    public function getYear(): string
    {
        return $this->year;
    }

    public function setYear(string $year): void
    {
        $this->year = $year;
    }
}

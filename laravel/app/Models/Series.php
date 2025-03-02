<?php

namespace App\Models;

use Database\Factories\SeriesFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Series extends Model
{
    /** @use HasFactory<SeriesFactory> */
    use HasFactory;

    const id = 'id';
    const name = 'name';
    const theTvDbId = 'theTvDbId';

    // relations
    const has_many_episodes = 'episodes';
    public $timestamps = false;

    public int $id;
    public string $name;
    public int $theTvDbId;

    protected $fillable = [
        self::name,
        self::theTvDbId,
    ];

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getTheTvDbId(): int
    {
        return $this->theTvDbId;
    }

    public function setTheTvDbId(int $theTvDbId): void
    {
        $this->theTvDbId = $theTvDbId;
    }

    public function episodes(): HasMany
    {
        return $this->hasMany(Episode::class);
    }
}

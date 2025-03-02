<?php

namespace App\Models\TheTvDB;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Model;

class Series extends Model
{
    use HasTimestamps;
    const TABLE = 'series_data';

    // properties
    const id = 'id';
    const firstAired = 'firstAired';
    const image = 'image';
    const lastAired = 'lastAired';
    const lastUpdated = 'lastUpdated';
    const name = 'name';
    const nextAired = 'nextAired';
    const slug = 'slug';
    const year = 'year';
    const created_at = self::CREATED_AT;
    const updated_at = self::UPDATED_AT;

    protected $table = self::TABLE;

    protected $fillable = [
        self::firstAired,
        self::image,
        self::lastAired,
        self::lastUpdated,
        self::name,
        self::nextAired,
        self::slug,
        self::year,
    ];

    public int $id;
    public string $firstAired;
    public string $image = '';
    public string $lastAired = '';
    public string $lastUpdated = '';
    public string $name = '';
    public string $nextAired = '';
    public string $slug = '';
    public string $year = '';
}

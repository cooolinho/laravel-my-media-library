<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $title
 * @property string $url
 * @property Carbon $createdAt
 * @property Carbon $updatedAt
 */
class WarezLink extends Model
{
    const id = 'id';
    const title = 'title';
    const url = 'url';

    protected $fillable = [
        self::title,
        self::url,
    ];

    public function getIframeUrl(string $seriesName): string
    {
        return str_replace('<SERIES_NAME>', $seriesName, $this->url);
    }
}

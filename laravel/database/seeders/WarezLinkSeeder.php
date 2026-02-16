<?php

namespace Database\Seeders;

use App\Models\WarezLink;
use Illuminate\Database\Seeder;

class WarezLinkSeeder extends Seeder
{
    private static array $default = [
        [
            WarezLink::title => 'Serienjunkies.org',
            WarezLink::url => 'https://serienjunkies.org/serie/search?q=<SERIES_NAME>',
            WarezLink::placeholderType => WarezLink::PLACEHOLDER_SERIES_NAME,
        ],
        [
            WarezLink::title => 'TheTVDB (by ID)',
            WarezLink::url => 'https://thetvdb.com/dereferrer/series/<TVDB_ID>',
            WarezLink::placeholderType => WarezLink::PLACEHOLDER_TVDB_ID,
        ],
        [
            WarezLink::title => 'IMDb Suche',
            WarezLink::url => 'https://www.imdb.com/find?q=<SERIES_NAME>&s=tt&ttype=tv',
            WarezLink::placeholderType => WarezLink::PLACEHOLDER_SERIES_NAME,
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        WarezLink::query()
            ->upsert(self::$default, WarezLink::title);
    }
}

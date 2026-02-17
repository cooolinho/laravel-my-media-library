<?php

namespace Database\Seeders;

use App\Models\WarezLink;
use Illuminate\Database\Seeder;

class WarezLinkSeeder extends Seeder
{
    private static array $default = [
        [
            WarezLink::title => 'Serienjunkies.org',
            WarezLink::url => 'https://serienjunkies.org/serie/search?q=' . WarezLink::PLACEHOLDER,
            WarezLink::placeholderType => WarezLink::PLACEHOLDER_SERIES_NAME,
        ],
        [
            WarezLink::title => 'TheTVDB (by ID)',
            WarezLink::url => 'https://thetvdb.com/dereferrer/series/' . WarezLink::PLACEHOLDER,
            WarezLink::placeholderType => WarezLink::PLACEHOLDER_TVDB_ID,
        ],
        [
            WarezLink::title => 'IMDb Suche',
            WarezLink::url => 'https://www.imdb.com/find?q=' . WarezLink::PLACEHOLDER . '&s=tt&ttype=tv',
            WarezLink::placeholderType => WarezLink::PLACEHOLDER_SERIES_NAME,
        ],
        [
            WarezLink::title => 'Serienjunkies (by Slug)',
            WarezLink::url => 'https://serienjunkies.org/serie/' . WarezLink::PLACEHOLDER,
            WarezLink::placeholderType => WarezLink::PLACEHOLDER_SERIES_SLUG,
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

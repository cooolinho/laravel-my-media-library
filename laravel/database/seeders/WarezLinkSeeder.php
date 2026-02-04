<?php

namespace Database\Seeders;

use App\Models\WarezLink;
use Illuminate\Database\Seeder;

class WarezLinkSeeder extends Seeder
{
    private static array $default = [
        [
            WarezLink::title => 'serienjunkies.org',
            WarezLink::url => 'https://serienjunkies.org/serie/search?q=<SERIES_NAME>',
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

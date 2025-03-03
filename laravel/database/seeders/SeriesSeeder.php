<?php

namespace Database\Seeders;

use App\Models\Series;
use Illuminate\Database\Seeder;

class SeriesSeeder extends Seeder
{
    private static array $default = [
        ['2 Broke Girls', 248741],
        ['Better Call Saul', 273181],
        ['Breaking Bad', 81189],
        ['Die Simpsons', 71663],
        ['Game of Thrones', 121361],
        ['King of Queens', 73641],
        ['Narcos', 282670],
        ['Prison Break', 360115],
        ['South Park', 75897],
        ['Squid Game', 383275],
        ['The Big Bang Theory', 80379],
        ['The Walking Dead', 153021],
        ['Two and a Half Man', 72227],
        ['Weeds', 74845],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (self::$default as [$name, $theTvDbId]) {
            Series::factory()->create([
                Series::name => $name,
                Series::theTvDbId => $theTvDbId,
            ]);
        }
    }
}

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
        ['South Park', 75897],
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

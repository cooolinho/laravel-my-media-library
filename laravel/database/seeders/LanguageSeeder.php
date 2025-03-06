<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    private static array $default = [
        [
            "id" => "deu",
            "name" => "German",
            "nativeName" => "Deutsch",
        ],
        [
            "id" => "eng",
            "name" => "English",
            "nativeName" => "English",
        ],
        [
            "id" => "spa",
            "name" => "Spanish",
            "nativeName" => "español",
        ],
        [
            "id" => "fra",
            "name" => "French",
            "nativeName" => "français",
        ],
    ];


    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Language::query()->upsert(self::$default, Language::id);
    }
}

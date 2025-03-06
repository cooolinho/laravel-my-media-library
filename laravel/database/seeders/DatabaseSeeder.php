<?php

namespace Database\Seeders;

use App\Models\ContactPerson;
use App\Models\Credential;
use App\Models\Link;
use App\Models\MailBox;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Database\Seeder;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            LanguageSeeder::class,
            SeriesSeeder::class,
        ]);
    }
}

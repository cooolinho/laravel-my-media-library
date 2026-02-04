<?php

namespace Database\Seeders;

use App\Models\ContactPerson;
use App\Models\Credential;
use App\Models\Link;
use App\Models\MailBox;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // create admin user
        User::factory()->create([
            User::name => 'Admin User',
            User::email => 'admin@example.com',
        ]);
    }
}

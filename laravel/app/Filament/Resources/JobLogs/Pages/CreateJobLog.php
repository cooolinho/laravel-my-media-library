<?php

namespace App\Filament\Resources\JobLogs\Pages;

use App\Filament\Resources\JobLogs\JobLogResource;
use Filament\Resources\Pages\CreateRecord;

class CreateJobLog extends CreateRecord
{
    protected static string $resource = JobLogResource::class;
}

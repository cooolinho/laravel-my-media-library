<?php

namespace App\Jobs;

use App\Models\Job;
use Illuminate\Database\Eloquent\Collection;

abstract class AbstractBaseJob implements JobInterface
{
    public static function all(): Collection
    {
        return Job::findByJob(static::class);
    }
}

<?php

namespace App\Jobs;

use Illuminate\Database\Eloquent\Collection;

interface JobInterface
{
    public static function all(): Collection;
}

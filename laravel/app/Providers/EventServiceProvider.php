<?php

namespace App\Providers;

use App\Listeners\HandleSettingsSaved;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Spatie\LaravelSettings\Events\SettingsSaved;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        SettingsSaved::class => [
            HandleSettingsSaved::class,
        ],
    ];
}

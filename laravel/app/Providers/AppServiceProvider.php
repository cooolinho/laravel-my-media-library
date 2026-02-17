<?php

namespace App\Providers;

use App\Models\Episode;
use App\Models\Series;
use App\Observers\EpisodeObserver;
use App\Observers\SeriesObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Series::observe(SeriesObserver::class);
        Episode::observe(EpisodeObserver::class);
    }
}

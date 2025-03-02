<?php

namespace App\Http\Controllers;

use App\Jobs\SeriesDataJob;
use App\Jobs\SeriesEpisodesJob;
use App\Models\Series;
use App\Services\TheTVDBApiService;

class SeriesController extends Controller
{
    public function __construct(private TheTVDBApiService $theTVDBApiService)
    {
        $this->theTVDBApiService->login();
    }

    public function index()
    {
        $series = Series::query()->firstOrCreate([
            Series::name => 'Breaking Bad',
            Series::theTvDbId => 81189,
        ]);

        SeriesDataJob::dispatch($series);
        SeriesEpisodesJob::dispatch($series);

        return view('series.index');
    }
}

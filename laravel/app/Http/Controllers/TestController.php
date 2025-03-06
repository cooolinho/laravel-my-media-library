<?php

namespace App\Http\Controllers;

use App\Jobs\SeriesEpisodesJob;
use App\Models\Series;

class TestController extends Controller
{
    public function index()
    {
        $series = Series::find(3);
        SeriesEpisodesJob::dispatch($series);
        dd($series);
        return view('series.index');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Series;

class SeriesController extends Controller
{
    public function index()
    {
        $series = Series::query()->firstOrCreate([
            Series::name => 'Breaking Bad',
            Series::theTvDbId => 81189,
        ]);

        return view('series.index', [
            'series' => $series
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Series;
use App\Services\ImportDataService;

class TestController extends Controller
{
    public function index(ImportDataService $service)
    {
        $service->importSeriesArtworks(Series::all()->first());
        return view('series.index');
    }
}

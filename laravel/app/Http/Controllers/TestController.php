<?php

namespace App\Http\Controllers;

use App\Services\ImportDataService;

class TestController extends Controller
{
    public function index(ImportDataService $service)
    {
        return view('series.index');
    }
}

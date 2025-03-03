<?php

namespace App\Http\Controllers;

use App\Models\Series;
use App\Services\TheTVDBApiService;
use Illuminate\Http\Request;

class SeriesController extends Controller
{
    public function __construct(private TheTVDBApiService $theTVDBApiService)
    {

    }

    public function index()
    {
        $series = Series::query()->where([
            Series::theTvDbId => 273181,
        ])->first();

        return view('series.index', [
            'series' => $series
        ]);
    }

    public function search(Request $request)
    {
        $query = $request->input('q');

        $results = $this->theTVDBApiService->search($query)['data'] ?? [];
        $suggestions = [];

        foreach ($results as $result) {
            $name = $result['translations']['deu'] ?? $result['translations']['eng'];
            $theTvDbId = $result['tvdb_id'];
            $actionNewParams = [
                Series::name => $name,
                Series::theTvDbId => $theTvDbId,
            ];
            $suggestions[] = [
                'image' => $result['image_url'],
                'thumbnail' => $result['thumbnail'],
                'name' => $name,
                'first_air_time' => $result['first_air_time'],
                'overview' => $result['overviews']['deu'] ?? $result['overviews']['eng'],
                'tvdb_id' => $theTvDbId,
                'year' => $result['year'],
                'slug' => $result['slug'],
                'status' => $result['status'],
                'action_new' => url('/admin/series/create?' . http_build_query($actionNewParams)),
            ];
        }

        return response()->json($suggestions);
    }
}

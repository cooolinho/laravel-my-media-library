<?php

use Illuminate\Support\Facades\Route;

Route::name('api.')->group(function () {
    Route::post('/search', [\App\Http\Controllers\SeriesController::class, 'search'])->name('search');
});

<?php

use Illuminate\Support\Facades\Route;

//Route::get('/', function () {
//    return redirect('/admin');
//});

Route::get('/test', [\App\Http\Controllers\TestController::class, 'index']);

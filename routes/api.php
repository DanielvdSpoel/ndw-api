<?php

use App\Http\Controllers\CharacteristicController;
use App\Http\Controllers\SiteController;
use App\Jobs\CollectSitesFromNDWJob;
use App\Jobs\CollectSpeedsFromNDWJob;
use Illuminate\Support\Facades\Route;

//Route::get('/', function () {
//    CollectSpeedsFromNDWJob::dispatch();
//    return view('welcome');
//});

Route::apiResource('sites', SiteController::class)->only(['index', 'show']);
Route::apiResource('sites.characteristics', CharacteristicController::class)->only(['index', 'show']);
Route::get('/test', function () {
   CollectSitesFromNDWJob::dispatch();
});

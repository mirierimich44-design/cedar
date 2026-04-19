<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // BI Dashboard API
    Route::get('/bi/insights', [\App\Http\Controllers\BIDashboardController::class, 'getInsights']);
    Route::post('/bi/ask', [\App\Http\Controllers\BIDashboardController::class, 'askQuestion']);
    Route::get('/bi/predictions', [\App\Http\Controllers\BIDashboardController::class, 'getPredictions']);
});

Route::post('/sync/receive', [\App\Http\Controllers\SyncController::class, 'receiveFromLocal']);

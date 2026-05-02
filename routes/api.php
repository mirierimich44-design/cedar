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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/sync/receive', [\App\Http\Controllers\SyncController::class, 'receiveFromLocal']);

// ─── Mobile POS App ───────────────────────────────────────────────────────────
Route::prefix('mobile')->group(function () {
    Route::post('/login', [\App\Http\Controllers\MobilePosController::class, 'login']);

    Route::middleware('auth:api')->group(function () {
        Route::post('/logout',              [\App\Http\Controllers\MobilePosController::class, 'logout']);
        Route::get('/pos-details',          [\App\Http\Controllers\MobilePosController::class, 'posDetails']);
        Route::get('/products',             [\App\Http\Controllers\MobilePosController::class, 'products']);
        Route::post('/sale',                [\App\Http\Controllers\MobilePosController::class, 'createSale']);
        Route::get('/payment-types',        [\App\Http\Controllers\MobilePosController::class, 'paymentTypes']);
        Route::get('/expense-categories',   [\App\Http\Controllers\MobilePosController::class, 'expenseCategories']);
        Route::post('/expense',             [\App\Http\Controllers\MobilePosController::class, 'createExpense']);
    });
});

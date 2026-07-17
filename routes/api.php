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

    // BI Dashboard API - Moved to web.php for session auth
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
        Route::get('/till-summary',         [\App\Http\Controllers\MobilePosController::class, 'tillSummary']);
        Route::post('/close-till',          [\App\Http\Controllers\MobilePosController::class, 'closeTill']);
        Route::get('/recent-sales',         [\App\Http\Controllers\MobilePosController::class, 'recentSales']);

        // Admin-only routes
        Route::get('/admin/dashboard',        [\App\Http\Controllers\MobilePosController::class, 'adminDashboard']);
        Route::get('/admin/stock-alerts',     [\App\Http\Controllers\MobilePosController::class, 'adminStockAlerts']);
        Route::get('/admin/staff-activity',   [\App\Http\Controllers\MobilePosController::class, 'adminStaffActivity']);
        Route::get('/admin/sales-report',     [\App\Http\Controllers\MobilePosController::class, 'adminSalesReport']);
        Route::get('/admin/expenses-summary', [\App\Http\Controllers\MobilePosController::class, 'adminExpensesSummary']);
        Route::post('/admin/stock-transfer',  [\App\Http\Controllers\MobilePosController::class, 'adminStockTransfer']);
        Route::get('/owner-pack',             [\App\Http\Controllers\MobilePosController::class, 'ownerPack']);
        Route::get('/day-close-summary',      [\App\Http\Controllers\MobilePosController::class, 'ownerPack']);
    });
});

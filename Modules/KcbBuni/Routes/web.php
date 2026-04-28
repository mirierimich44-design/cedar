<?php

use Modules\KcbBuni\Http\Controllers\KcbBuniGatewayController;

Route::middleware(['setData', 'auth', 'SetSessionData', 'language', 'timezone', 'AdminSidebarMenu', 'CheckUserLogin'])->group(function () {
    Route::prefix('kcb-buni')->name('kcb-buni.')->group(function () {
        Route::get('/settings',              [KcbBuniGatewayController::class, 'settings'])->name('settings');
        Route::post('/settings',             [KcbBuniGatewayController::class, 'saveSettings'])->name('settings.save');
        Route::post('/test-connection',      [KcbBuniGatewayController::class, 'testConnection'])->name('test');
        Route::get('/get-balance',           [KcbBuniGatewayController::class, 'getBalance'])->name('get-balance');
        Route::get('/transactions',          [KcbBuniGatewayController::class, 'transactions'])->name('transactions');
    });
});

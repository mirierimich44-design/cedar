<?php

use Modules\Pesapal\Http\Controllers\PesapalGatewayController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['setData', 'auth', 'SetSessionData', 'language', 'timezone', 'AdminSidebarMenu', 'CheckUserLogin'])->group(function () {
    Route::prefix('pesapal')->name('pesapal.')->group(function () {
        Route::get('/settings',              [PesapalGatewayController::class, 'settings'])->name('settings');
        Route::post('/settings',             [PesapalGatewayController::class, 'saveSettings'])->name('settings.save');
        Route::post('/test-connection',      [PesapalGatewayController::class, 'testConnection'])->name('test');
        Route::post('/register-ipn',         [PesapalGatewayController::class, 'registerIpn'])->name('register-ipn');
        Route::post('/initiate-payment',     [PesapalGatewayController::class, 'initiatePayment'])->name('initiate-payment');
        Route::post('/check-payment-status', [PesapalGatewayController::class, 'checkPaymentStatus'])->name('check-status');
        Route::get('/transactions',          [PesapalGatewayController::class, 'transactions'])->name('transactions');
        Route::get('/daily-summary',         [PesapalGatewayController::class, 'dailySummary'])->name('daily-summary');
    });
});

// Pesapal Webhooks (public, no auth — called by Pesapal)
Route::get('/pesapal/ipn',      [PesapalGatewayController::class, 'ipnCallback'])->name('pesapal.ipn');
Route::get('/pesapal/callback', [PesapalGatewayController::class, 'paymentCallback'])->name('pesapal.customer-callback');

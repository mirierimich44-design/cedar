<?php

use Illuminate\Support\Facades\Route;
use Modules\Parcel\Http\Controllers\BookingController;
use Modules\Parcel\Http\Controllers\DispatchController;
use Modules\Parcel\Http\Controllers\TrackingController;
use Modules\Parcel\Http\Controllers\ParcelController;

/*
|--------------------------------------------------------------------------
| Parcel Module Web Routes
|--------------------------------------------------------------------------
*/

// ── Public: Parcel Tracking (no auth) ──────────────────────────────────────
Route::get('/track/{waybill}', [TrackingController::class, 'show'])->name('parcel.track.show');

// ── Authenticated Parcel Routes ────────────────────────────────────────────
Route::middleware(['web', 'auth', 'SetSessionData', 'language', 'timezone', 'AdminSidebarMenu'])
    ->prefix('parcel')
    ->name('parcel.')
    ->group(function () {

        // Dashboard / Index
        Route::get('/', [ParcelController::class, 'index'])->name('index');

        // ── Booking ──────────────────────────────────────────────────────
        Route::get('/booking/create', [BookingController::class, 'create'])->name('booking.create');
        Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');

        // ── Parcel Detail ─────────────────────────────────────────────
        Route::get('/{id}', [ParcelController::class, 'show'])->name('show');

        // ── Stations ──────────────────────────────────────────────────
        Route::get('/stations', [ParcelController::class, 'stationIndex'])->name('stations.index');
        Route::post('/stations', [ParcelController::class, 'storeStation'])->name('stations.store');

        // ── Dispatch / Status Updates ─────────────────────────────────
        Route::post('/dispatch/update-status', [DispatchController::class, 'updateStatus'])->name('dispatch.update_status');
        Route::post('/dispatch/bulk-update-status', [DispatchController::class, 'bulkUpdateStatus'])->name('dispatch.bulk_update_status');

    });

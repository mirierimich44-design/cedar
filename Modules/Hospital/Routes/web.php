<?php

use Illuminate\Support\Facades\Route;
use Modules\Hospital\Http\Controllers\LabController;
use Modules\Hospital\Http\Controllers\MortuaryController;
use Modules\Hospital\Http\Controllers\PatientController;
use Modules\Hospital\Http\Controllers\VisitController;

/*
|--------------------------------------------------------------------------
| Hospital Module – Web Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['web', 'auth', 'language', 'AdminSidebarMenu'])
    ->prefix('hospital')
    ->name('hospital.')
    ->group(function () {

        // ── Patients ──────────────────────────────────────────────────────
        Route::get('/patients',           [PatientController::class, 'index'])->name('patients.index');
        Route::get('/patients/create',    [PatientController::class, 'create'])->name('patients.create');
        Route::post('/patients',          [PatientController::class, 'store'])->name('patients.store');
        Route::get('/patients/{id}',      [PatientController::class, 'show'])->name('patients.show');
        Route::get('/patients/{id}/edit', [PatientController::class, 'edit'])->name('patients.edit');
        Route::put('/patients/{id}',      [PatientController::class, 'update'])->name('patients.update');

        // ── Visits ────────────────────────────────────────────────────────
        Route::get('/visits',                   [VisitController::class, 'index'])->name('visits.index');
        Route::get('/visits/create',            [VisitController::class, 'create'])->name('visits.create');
        Route::post('/visits',                  [VisitController::class, 'store'])->name('visits.store');
        Route::get('/visits/{id}',              [VisitController::class, 'show'])->name('visits.show');
        Route::post('/visits/{id}/status',      [VisitController::class, 'updateStatus'])->name('visits.updateStatus');
        Route::post('/visits/{id}/triage',      [VisitController::class, 'updateTriage'])->name('visits.updateTriage');

        // ── Lab Orders ────────────────────────────────────────────────────
        Route::get('/lab-orders',             [LabController::class, 'index'])->name('lab.index');
        Route::post('/lab-orders',            [LabController::class, 'store'])->name('lab.store');
        Route::post('/lab-orders/{id}/result',[LabController::class, 'updateResult'])->name('lab.updateResult');

        // ── Mortuary ──────────────────────────────────────────────────────
        Route::get('/mortuary',             [MortuaryController::class, 'index'])->name('mortuary.index');
        Route::get('/mortuary/create',      [MortuaryController::class, 'create'])->name('mortuary.create');
        Route::post('/mortuary',            [MortuaryController::class, 'store'])->name('mortuary.store');
        Route::post('/mortuary/{id}/release',[MortuaryController::class, 'release'])->name('mortuary.release');
    });

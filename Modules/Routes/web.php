<?php

use Illuminate\Support\Facades\Route;
use Modules\MadeToOrder\Http\Controllers\MadeOrderController;
use Modules\MadeToOrder\Http\Controllers\MaterialController;
use Modules\MadeToOrder\Http\Controllers\InstallController;

Route::group(['prefix'=>'made-to-order','middleware'=>['web','auth']], function(){
    Route::get('/', [MadeOrderController::class, 'index'])->name('made_to_order.index');
    Route::get('/create', [MadeOrderController::class, 'create'])->name('made_to_order.create');
    Route::post('/', [MadeOrderController::class, 'store'])->name('made_to_order.store');
    Route::get('/{id}', [MadeOrderController::class, 'show'])->name('made_to_order.show');

    // materials
    Route::get('/materials', [MaterialController::class, 'index'])->name('made_to_order.materials.index');
    Route::get('/materials/create', [MaterialController::class, 'create'])->name('made_to_order.materials.create');
    Route::post('/materials', [MaterialController::class, 'store'])->name('made_to_order.materials.store');

    // installer
    Route::get('/install', [InstallController::class, 'index'])->name('made_to_order.install.index');
    Route::post('/install/run', [InstallController::class, 'install'])->name('made_to_order.install.run');
});

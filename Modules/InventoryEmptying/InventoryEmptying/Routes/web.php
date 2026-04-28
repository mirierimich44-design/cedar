<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::group(['middleware' => 
//     ['web', 'authh', 'auth', 'SetSessionData', 'language', 'timezone','AdminSidebarMenu'], 
//     'prefix' => 'inventoryemptying'],
Route::middleware('web', 'authh', 'auth', 'SetSessionData', 'language', 'timezone', 'AdminSidebarMenu')->group(function () {
    Route::prefix('inventoryemptying')->group(function() {
        Route::resource('/inventoryemptying', Modules\InventoryEmptying\Http\Controllers\InventoryEmptyingController::class)->only('index','store','show');

    Route::get('install', [Modules\InventoryEmptying\Http\Controllers\InstallController::class,'index']);
    Route::post('install', [Modules\InventoryEmptying\Http\Controllers\InstallController::class,'install']);
    Route::get('install/uninstall', [Modules\InventoryEmptying\Http\Controllers\InstallController::class,'uninstall']);
    Route::get('install/update', [Modules\InventoryEmptying\Http\Controllers\InstallController::class,'update']);
    

    }); 
}); 

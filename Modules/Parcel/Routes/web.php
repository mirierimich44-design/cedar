<?php

use Illuminate\Support\Facades\Route;

Route::prefix('parcel')->group(function() {
    Route::get('/', 'ParcelController@index');
});

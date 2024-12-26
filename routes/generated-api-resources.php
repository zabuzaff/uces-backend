<?php

use Illuminate\Support\Facades\Route;

//start-generated-resources
Route::resource('booking', App\Http\Controllers\BookingApiController::class);
Route::resource('driver', App\Http\Controllers\DriverApiController::class);
Route::resource('receipt', App\Http\Controllers\ReceiptApiController::class);
//end-generated-resources

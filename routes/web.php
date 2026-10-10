<?php

use App\Http\Controllers\Api\SensorDataController;
use App\Http\Middleware\ValidateApiKey;
use Illuminate\Support\Facades\Route;

// Welcome page
Route::get('/', function () {
    return view('welcome');
});

// API endpoint untuk ESP32
Route::post('/sensor-data', SensorDataController::class)
    ->middleware(ValidateApiKey::class)
    ->name('api.sensor-data');

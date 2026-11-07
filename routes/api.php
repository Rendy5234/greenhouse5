<?php

use App\Http\Controllers\Api\SensorDataController;
use App\Http\Middleware\ValidateApiKey;
use Illuminate\Support\Facades\Route;

Route::middleware(ValidateApiKey::class)->group(function () {
    Route::post('/sensor-data', SensorDataController::class)
        ->name('api.sensor-data');
});

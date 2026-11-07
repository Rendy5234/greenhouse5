<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SensorDataRequest;
use App\Models\Configuration;
use App\Models\SensorReading;
use Illuminate\Http\JsonResponse;

class SensorDataController extends Controller
{
    /**
     * Simpan data sensor dari ESP32 dan kembalikan konfigurasi threshold.
     * Response format: { "status": "success", "config": { "temp_on": 30.0, "temp_off": 28.0, "soil_dry": 2500, "soil_wet": 1500 } }
     */
    public function __invoke(SensorDataRequest $request): JsonResponse
    {
        // Ambil konfigurasi threshold terbaru untuk snapshot
        $config = [
            'temp_on' => Configuration::getValue('temp_on'),
            'temp_off' => Configuration::getValue('temp_off'),
            'soil_dry' => Configuration::getValue('soil_dry'),
            'soil_wet' => Configuration::getValue('soil_wet'),
        ];

        // Simpan data sensor ke database dengan snapshot threshold
        SensorReading::create(array_merge(
            $request->safe()->all(),
            [
                'temp_on_used' => $config['temp_on'],
                'temp_off_used' => $config['temp_off'],
                'soil_dry_used' => $config['soil_dry'],
                'soil_wet_used' => $config['soil_wet'],
            ]
        ));

        // Return response sesuai spec (hanya status & config)
        return response()->json([
            'status' => 'success',
            'config' => $config,
        ], 201);
    }
}

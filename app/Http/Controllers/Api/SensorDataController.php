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
        $reading = SensorReading::create(array_merge(
            $request->safe()->all(),
            [
                'temp_on_used' => $config['temp_on'],
                'temp_off_used' => $config['temp_off'],
                'soil_dry_used' => $config['soil_dry'],
                'soil_wet_used' => $config['soil_wet'],
            ]
        ));

        // Return response
        return response()->json([
            'status' => 'success',
            'message' => 'Data sensor berhasil disimpan',
            'timestamp' => now()->toISOString(),
            'reading_id' => $reading->id,
            'config' => $config,
        ]);
    }
}

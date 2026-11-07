<?php

namespace Database\Seeders;

use App\Models\SensorReading;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SensorReadingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        $readings = [];

        // Generate data for the last 24 hours, one reading every 10 minutes = 144 readings
        for ($i = 143; $i >= 0; $i--) {
            $timestamp = $now->copy()->subMinutes($i * 10);

            // Simulate daily temperature cycle: cooler at night, warmer at day
            $hour = $timestamp->hour;
            $baseTemp = 22 + 8 * sin(deg2rad(($hour - 6) * 15)); // peak ~30°C at 14:00, min ~22°C at 06:00
            $tempVariation = fake()->randomFloat(1, -1.5, 1.5);

            // Humidity inversely related to temperature
            $baseHumidity = 80 - 20 * sin(deg2rad(($hour - 6) * 15)); // peak ~80% at night, min ~60% at day
            $humidityVariation = fake()->randomFloat(1, -5, 5);

            // Soil moisture slowly decreases during day, increases at night (simulated watering)
            $baseSoilMoisture = 45 + 15 * sin(deg2rad(($hour - 6) * 15 + 90)); // phase shifted
            $soilVariation = fake()->randomFloat(1, -3, 3);

            // Relay states based on thresholds
            $tempOn = 30.0;
            $tempOff = 28.0;
            $soilDry = 30;
            $soilWet = 70;

            $fanOn = ($baseTemp + $tempVariation) >= $tempOn;
            $pumpOn = ($baseSoilMoisture + $soilVariation) <= $soilDry;

            // LED mode: 0=off, 1=auto, 2=on (simulate day/night cycle)
            $ledMode = ($hour >= 6 && $hour < 18) ? 1 : 0;

            $readings[] = [
                'suhu' => round($baseTemp + $tempVariation, 1),
                'kelembapan_udara' => round(clamp($baseHumidity + $humidityVariation, 30, 95), 1),
                'kelembapan_tanah' => round(clamp($baseSoilMoisture + $soilVariation, 10, 90), 1),
                'relay_fan' => $fanOn,
                'relay_pump' => $pumpOn,
                'led_mode' => $ledMode,
                'temp_on_used' => $tempOn,
                'temp_off_used' => $tempOff,
                'soil_dry_used' => $soilDry,
                'soil_wet_used' => $soilWet,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];
        }

        // Insert in chunks for performance
        foreach (array_chunk($readings, 50) as $chunk) {
            SensorReading::insert($chunk);
        }

        $this->command->info('SensorReading seeder: '.count($readings).' records created for last 24 hours.');
    }
}

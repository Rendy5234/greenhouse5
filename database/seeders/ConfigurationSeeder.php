<?php

namespace Database\Seeders;

use App\Models\Configuration;
use Illuminate\Database\Seeder;

class ConfigurationSeeder extends Seeder
{
    /**
     * Jalankan seeder untuk mengisi konfigurasi default.
     */
    public function run(): void
    {
        $configs = [
            [
                'key' => 'temp_on',
                'value' => '30.0',
                'type' => 'float',
                'label' => 'Suhu Kipas Nyala (°C)',
                'description' => 'Threshold suhu ketika kipas ventilasi harusnyala. Saat suhu melebihi nilai ini, kipas akan otomatis aktif.',
            ],
            [
                'key' => 'temp_off',
                'value' => '28.0',
                'type' => 'float',
                'label' => 'Suhu Kipas Mati (°C)',
                'description' => 'Threshold suhu ketika kipas ventilasi harusmati. Saat suhu turun di bawah nilai ini, kipas akan otomatis nonaktif.',
            ],
            [
                'key' => 'soil_dry',
                'value' => '2500',
                'type' => 'integer',
                'label' => 'ADC Tanah Kering (Pompa Nyala)',
                'description' => 'Nilai ADC dari potensiometer tanah ketika tanah dianggapkering. Saat nilai melebihi ini, pompa irigasi akan aktif.',
            ],
            [
                'key' => 'soil_wet',
                'value' => '1500',
                'type' => 'integer',
                'label' => 'ADC Tanah Basah (Pompa Mati)',
                'description' => 'Nilai ADC dari potensiometer tanah ketika tanah dianggapbasah. Saat nilai turun di bawah ini, pompa irigasi akan nonaktif.',
            ],
        ];

        foreach ($configs as $config) {
            Configuration::updateOrCreate(
                ['key' => $config['key']],
                $config
            );
        }
    }
}

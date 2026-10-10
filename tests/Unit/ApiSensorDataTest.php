<?php

namespace Tests\Unit;

use App\Models\Configuration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiSensorDataTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Configuration::firstOrCreate(['key' => 'temp_on'], ['value' => 30.0, 'type' => 'float', 'label' => 'Suhu Nyala', 'description' => 'Threshold suhu untuk menyalakan kipas']);
        Configuration::firstOrCreate(['key' => 'temp_off'], ['value' => 28.0, 'type' => 'float', 'label' => 'Suhu Mati', 'description' => 'Threshold suhu untuk mematikan kipas']);
        Configuration::firstOrCreate(['key' => 'soil_dry'], ['value' => 2500, 'type' => 'integer', 'label' => 'Tanah Kering', 'description' => 'Threshold kelembapan tanah untuk menyalakan pompa']);
        Configuration::firstOrCreate(['key' => 'soil_wet'], ['value' => 1500, 'type' => 'integer', 'label' => 'Tanah Basah', 'description' => 'Threshold kelembapan tanah untuk mematikan pompa']);
    }

    public function test_endpoint_sensor_data_dapat_diakses_dengan_api_key_yang_valid(): void
    {
        config(['app.api_key' => 'test-key-123']);
        $response = $this->postJson('/sensor-data', [
            'suhu' => 32.5,
            'kelembapan_udara' => 75.2,
            'kelembapan_tanah' => 2800,
            'relay_fan' => true,
            'relay_pump' => false,
            'led_mode' => 'kipas',
        ], ['X-API-Key' => 'test-key-123']);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Data sensor berhasil disimpan',
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'timestamp',
                'reading_id',
                'config' => [
                    'temp_on',
                    'temp_off',
                    'soil_dry',
                    'soil_wet',
                ],
            ]);

        $this->assertDatabaseHas('sensor_readings', [
            'suhu' => 32.5,
            'kelembapan_udara' => 75.2,
            'kelembapan_tanah' => 2800,
        ]);
    }

    public function test_endpoint_sensor_data_menolak_request_tanpa_api_key(): void
    {
        $response = $this->postJson('/sensor-data', ['suhu' => 32.5]);

        $response->assertStatus(401)
            ->assertJson([
                'status' => 'error',
                'message' => 'API key tidak ditemukan',
            ]);
    }

    public function test_endpoint_sensor_data_menolak_request_dengan_api_key_yang_salah(): void
    {
        config(['app.api_key' => 'test-key-123']);
        $response = $this->postJson('/sensor-data', ['suhu' => 32.5], ['X-API-Key' => 'wrong-key']);

        $response->assertStatus(403)
            ->assertJson([
                'status' => 'error',
                'message' => 'API key tidak valid',
            ]);
    }

    public function test_validasi_field_suhu_wajib_diisi(): void
    {
        config(['app.api_key' => 'test-key-123']);
        $response = $this->postJson('/sensor-data', [
            'kelembapan_udara' => 75.0,
            'kelembapan_tanah' => 2800,
        ], ['X-API-Key' => 'test-key-123']);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['suhu']);
    }

    public function test_validasi_suhu_dalam_rentang_0_sampai_60(): void
    {
        config(['app.api_key' => 'test-key-123']);
        $response = $this->postJson('/sensor-data', [
            'suhu' => 70.0,
            'kelembapan_udara' => 75.0,
            'kelembapan_tanah' => 2800,
        ], ['X-API-Key' => 'test-key-123']);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['suhu']);
    }

    public function test_field_relay_dan_led_mode_bersifat_opsional(): void
    {
        config(['app.api_key' => 'test-key-123']);
        $response = $this->postJson('/sensor-data', [
            'suhu' => 32.5,
            'kelembapan_udara' => 75.0,
            'kelembapan_tanah' => 2800,
        ], ['X-API-Key' => 'test-key-123']);

        $response->assertStatus(200);
    }

    public function test_response_mengembalikan_konfigurasi_threshold_yang_benar(): void
    {
        config(['app.api_key' => 'test-key-123']);
        $response = $this->postJson('/sensor-data', [
            'suhu' => 32.5,
            'kelembapan_udara' => 75.0,
            'kelembapan_tanah' => 2800,
        ], ['X-API-Key' => 'test-key-123']);

        $response->assertJson([
            'config' => [
                'temp_on' => 30.0,
                'temp_off' => 28.0,
                'soil_dry' => 2500,
                'soil_wet' => 1500,
            ],
        ]);
    }
}

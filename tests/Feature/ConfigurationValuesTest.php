<?php

namespace Tests\Feature;

use App\Models\Configuration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ConfigurationValuesTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_values_mengembalikan_semua_key_dalam_satu_query(): void
    {
        Configuration::firstOrCreate(['key' => 'temp_on'], ['value' => 30.0, 'type' => 'float', 'label' => 'Suhu Nyala']);
        Configuration::firstOrCreate(['key' => 'temp_off'], ['value' => 28.0, 'type' => 'float', 'label' => 'Suhu Mati']);
        Configuration::firstOrCreate(['key' => 'soil_dry'], ['value' => 2500, 'type' => 'integer', 'label' => 'Tanah Kering']);
        Configuration::firstOrCreate(['key' => 'soil_wet'], ['value' => 1500, 'type' => 'integer', 'label' => 'Tanah Basah']);

        DB::enableQueryLog();

        $values = Configuration::getValues([
            'temp_on',
            'temp_off',
            'soil_dry',
            'soil_wet',
        ]);

        $this->assertCount(1, DB::getQueryLog());

        $this->assertSame([
            'temp_on' => 30.0,
            'temp_off' => 28.0,
            'soil_dry' => 2500,
            'soil_wet' => 1500,
        ], $values);
    }

    public function test_get_values_mengembalikan_default_untuk_key_yang_tidak_ada(): void
    {
        Configuration::firstOrCreate(['key' => 'temp_on'], ['value' => 30.0, 'type' => 'float', 'label' => 'Suhu Nyala']);

        $values = Configuration::getValues(['temp_on', 'tidak_ada'], default: 0);

        $this->assertSame(30.0, $values['temp_on']);
        $this->assertSame(0, $values['tidak_ada']);
    }

    public function test_get_values_mengembalikan_null_untuk_key_yang_tidak_ada_tanpa_default(): void
    {
        $values = Configuration::getValues(['tidak_ada']);

        $this->assertArrayHasKey('tidak_ada', $values);
        $this->assertNull($values['tidak_ada']);
    }
}

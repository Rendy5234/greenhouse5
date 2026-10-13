<?php

namespace Database\Factories;

use App\Models\SensorReading;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SensorReading>
 */
class SensorReadingFactory extends Factory
{
    /**
     * Definisi state default factory.
     */
    public function definition(): array
    {
        return [
            'suhu' => $this->faker->randomFloat(2, 25, 35),
            'kelembapan_udara' => $this->faker->randomFloat(2, 60, 90),
            'kelembapan_tanah' => $this->faker->numberBetween(0, 4095),
            'relay_fan' => $this->faker->boolean,
            'relay_pump' => $this->faker->boolean,
            'led_mode' => $this->faker->randomElement(['normal', 'kipas', 'pompa', 'kritis']),
            'temp_on_used' => $this->faker->optional()->randomFloat(2, 20, 35),
            'temp_off_used' => $this->faker->optional()->randomFloat(2, 20, 35),
            'soil_dry_used' => $this->faker->optional()->numberBetween(0, 100),
            'soil_wet_used' => $this->faker->optional()->numberBetween(0, 100),
        ];
    }
}

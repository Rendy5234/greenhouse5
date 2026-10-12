<?php

namespace Database\Factories;

use App\Models\Configuration;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Configuration>
 */
class ConfigurationFactory extends Factory
{
    /**
     * Definisi state default factory.
     */
    public function definition(): array
    {
        return [
            'uuid' => Str::uuid()->getBytes(),
            'key' => $this->faker->unique()->word,
            'value' => $this->faker->randomFloat(2, 0, 100),
            'type' => 'float',
            'label' => $this->faker->words(3, true),
            'description' => $this->faker->sentence,
        ];
    }
}

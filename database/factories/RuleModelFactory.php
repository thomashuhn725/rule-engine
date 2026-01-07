<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RuleModel>
 */
class RuleModelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->slug(3),
            'value_1_type' => 'reference',
            'value_1' => 'data.field',
            'comparitor' => '==',
            'value_2_type' => 'static',
            'value_2' => fake()->word(),
            'category' => fake()->word(),
        ];
    }
}

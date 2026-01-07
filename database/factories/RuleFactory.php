<?php

namespace Database\Factories;

use App\Models\Comparitor;
use App\Models\ReferenceValue;
use App\Models\StaticValue;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Rule>
 */
class RuleFactory extends Factory
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
            'value_1_type' => 'reference_value',
            'value_1_id' => ReferenceValue::factory(),
            'comparitor_id' => Comparitor::factory(),
            'value_2_type' => 'static_value',
            'value_2_id' => StaticValue::factory(),
        ];
    }

    public function withReferenceValues(): static
    {
        return $this->state(fn (array $attributes) => [
            'value_1_type' => 'reference_value',
            'value_1_id' => ReferenceValue::factory(),
            'value_2_type' => 'reference_value',
            'value_2_id' => ReferenceValue::factory(),
        ]);
    }

    public function withStaticValues(): static
    {
        return $this->state(fn (array $attributes) => [
            'value_1_type' => 'static_value',
            'value_1_id' => StaticValue::factory(),
            'value_2_type' => 'static_value',
            'value_2_id' => StaticValue::factory(),
        ]);
    }
}

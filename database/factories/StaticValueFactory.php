<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StaticValue>
 */
class StaticValueFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'value' => fake()->word(),
        ];
    }

    public function withValue(mixed $value): static
    {
        return $this->state(fn (array $attributes) => [
            'value' => $value,
        ]);
    }
}

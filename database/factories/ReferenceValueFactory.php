<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ReferenceValue>
 */
class ReferenceValueFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'node' => fake()->words(3, true),
        ];
    }

    public function withNode(string $node): static
    {
        return $this->state(fn (array $attributes) => [
            'node' => $node,
        ]);
    }
}

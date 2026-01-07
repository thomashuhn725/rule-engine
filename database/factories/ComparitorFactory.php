<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comparitor>
 */
class ComparitorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'symbol' => fake()->unique()->randomElement(['==', '>', '<', '&&', '||', '~', '!', '===']),
        ];
    }

    public function equals(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'equals',
            'symbol' => '==',
        ]);
    }

    public function strict(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'strict',
            'symbol' => '===',
        ]);
    }

    public function greater(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'greater',
            'symbol' => '>',
        ]);
    }

    public function less(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'less',
            'symbol' => '<',
        ]);
    }

    public function not(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'not',
            'symbol' => '!',
        ]);
    }

    public function all(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'all',
            'symbol' => '&&',
        ]);
    }

    public function any(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'any',
            'symbol' => '||',
        ]);
    }

    public function regex(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'regex',
            'symbol' => '~',
        ]);
    }
}

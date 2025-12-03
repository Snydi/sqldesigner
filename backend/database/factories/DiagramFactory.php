<?php

namespace Database\Factories;

use App\Models\Diagram;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Diagram>
 */
class DiagramFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'schema' => null,
            'script' => null,
            'user_id' => User::factory(),
        ];
    }
}
<?php

namespace Database\Factories;

use App\Models\Diagram;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DiagramVisitorFactory extends Factory
{
    public function definition(): array
    {
        return [
            'diagram_id' => Diagram::factory(),
            'user_id'    => User::factory(),
            'status'     => 'pending',
            'access'     => null,
        ];
    }
}

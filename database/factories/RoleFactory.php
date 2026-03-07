<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoleFactory extends Factory
{
    protected $model = Role::class;

    public function definition(): array
    {
        return [
            // Seed foundational RBAC (Role-Based Access Control) definitions
            'name' => $this->faker->unique()->randomElement(['user', 'admin', 'moderator']),
            'description' => $this->faker->sentence(6),
        ];
    }
}
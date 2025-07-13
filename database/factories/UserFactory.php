<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'role' => 'user',
            'bio' => $this->faker->sentence(),
            'study_field' => $this->faker->word(),
            'reputation_points' => rand(0, 500),
            'avatar' => null,
            'remember_token' => Str::random(10),
        ];
    }
}

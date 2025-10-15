<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class NotificationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id ?? User::factory(),
            'type' => $this->faker->randomElement(['comment', 'reply', 'upvote']),
            'message' => $this->faker->sentence(),
            'link' => $this->faker->url(),
            'is_read' => $this->faker->boolean(30), // 30% read
        ];
    }
}

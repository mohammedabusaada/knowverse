<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Post;
use App\Models\Comment;

class ReportFactory extends Factory
{
    public function definition(): array
    {
        $targetTypes = [
            Post::class,
            Comment::class,
        ];

        $targetType = $this->faker->randomElement($targetTypes);
        $targetId = $targetType::inRandomOrder()->first()->id ?? null;

        return [
            'user_id' => User::inRandomOrder()->first()->id ?? User::factory(),
            'target_type' => $targetType,
            'target_id' => $targetId,
            'reason' => $this->faker->sentence(),
        ];
    }
}

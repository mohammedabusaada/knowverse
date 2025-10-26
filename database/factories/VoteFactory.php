<?php

namespace Database\Factories;

use App\Models\Vote;
use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Database\Eloquent\Factories\Factory;

class VoteFactory extends Factory
{
    protected $model = Vote::class;

    public function definition(): array
    {
        // Choose target: post or comment
        $targetType = $this->faker->randomElement([Post::class, Comment::class]);
        $target = $targetType::inRandomOrder()->first();

        return [
            'user_id' => User::inRandomOrder()->value('id') ?? User::factory(),
            'target_id' => $target->id,
            'target_type' => $targetType,
            'value' => $this->faker->randomElement([1, -1]),
            'created_at' => now()->format('Y-m-d H:i:s'),
        ];
    }
}

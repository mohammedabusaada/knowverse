<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    protected $model = Comment::class;

    public function definition(): array
    {
        return [
            'post_id' => Post::inRandomOrder()->value('id') ?? Post::factory(),
            'user_id' => User::inRandomOrder()->value('id') ?? User::factory(),
            'parent_id' => null, // Will handle replies in the seeder
            'body' => $this->faker->paragraph(rand(1, 3)),
            'upvote_count' => $this->faker->numberBetween(0, 100),
            'downvote_count' => $this->faker->numberBetween(0, 20),
            'created_at' => $this->faker->dateTimeThisYear(),
            'updated_at' => now(),
        ];
    }
}

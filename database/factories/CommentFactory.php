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
            // Ensure post_id and user_id exist or create them
            'post_id' => Post::inRandomOrder()->value('id') ?? Post::factory(),
            'user_id' => User::inRandomOrder()->value('id') ?? User::factory(),
            
            'parent_id' => null, // Will be handled for replies in the Seeder
            'body' => $this->faker->paragraph(rand(1, 3)),
            
            // New moderation fields
            'is_hidden' => $this->faker->boolean(5), // 5% chance to be hidden
            'spam_score' => $this->faker->numberBetween(0, 2), // Start with a low spam score
            
            'upvote_count' => $this->faker->numberBetween(0, 100),
            'downvote_count' => $this->faker->numberBetween(0, 20),
            'created_at' => $this->faker->dateTimeThisYear(),
            'updated_at' => now(),
        ];
    }
}
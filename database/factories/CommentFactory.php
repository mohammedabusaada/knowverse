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
            // Maintain referential integrity by explicitly linking to existing or newly generated entities
            'post_id' => Post::inRandomOrder()->value('id') ?? Post::factory(),
            'user_id' => User::inRandomOrder()->value('id') ?? User::factory(),
            
            // Relational hierarchy (threaded replies) is orchestrated at the Seeder level
            'parent_id' => null, 
            'body' => $this->faker->paragraph(rand(1, 3)),
            
            // Simulate realistic moderation states with weighted probabilities
            'is_hidden' => $this->faker->boolean(5), // 5% probability of being administratively concealed
            'spam_score' => $this->faker->numberBetween(0, 2), // Baseline heuristic spam score
            
            // Seed realistic platform engagement metrics
            'upvote_count' => $this->faker->numberBetween(0, 100),
            'downvote_count' => $this->faker->numberBetween(0, 20),
            'created_at' => $this->faker->dateTimeThisYear(),
            'updated_at' => now(),
        ];
    }
}
<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        $statuses = ['draft', 'published', 'archived'];

        return [
            'user_id' => User::inRandomOrder()->value('id') ?? User::factory(),
            'title' => $this->faker->sentence(6),
            'body' => $this->faker->paragraphs(rand(3, 6), true),
            'image' => $this->faker->optional(0.3)->imageUrl(640, 480, 'education', true),
            'status' => $this->faker->randomElement($statuses),
            'best_comment_id' => null, // handled later if needed
            'view_count' => $this->faker->numberBetween(0, 2000),
            'upvote_count' => $this->faker->numberBetween(0, 300),
            'downvote_count' => $this->faker->numberBetween(0, 50),
            'created_at' => $this->faker->dateTimeThisYear(),
            'updated_at' => now(),
        ];
    }
}

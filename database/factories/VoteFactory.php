<?php

namespace Database\Factories;

use App\Models\{Vote, User, Post, Comment};
use Illuminate\Database\Eloquent\Factories\Factory;

class VoteFactory extends Factory
{
    protected $model = Vote::class;

    public function definition(): array
    {
        // Polymorphic Target Selection: Votes can be cast on either Discussions or Responses
        $targetType = $this->faker->randomElement([Post::class, Comment::class]);
        $target = $targetType::inRandomOrder()->first();

        return [
            'user_id' => User::inRandomOrder()->value('id') ?? User::factory(),
            'target_id' => $target->id,
            'target_type' => $targetType,
            
            // Vote weight mapping: 1 for Endorsement (Upvote), -1 for Objection (Downvote)
            'value' => $this->faker->randomElement([1, -1]),
            'created_at' => now()->format('Y-m-d H:i:s'),
        ];
    }
}
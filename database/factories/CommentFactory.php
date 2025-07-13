<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Post;
use App\Models\User;

class CommentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'post_id' => \App\Models\Post::inRandomOrder()->first()->id ?? \App\Models\Post::factory(),
            'user_id' => \App\Models\User::inRandomOrder()->first()->id ?? \App\Models\User::factory(),
            'parent_id' => null,
            'body' => $this->faker->sentence(),
        ];
    }
}

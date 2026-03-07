<?php

namespace Database\Factories;

use App\Models\{UserActivity, User, Post, Comment};
use Illuminate\Database\Eloquent\Factories\Factory;

class UserActivityFactory extends Factory
{
    protected $model = UserActivity::class;

    public function definition(): array
    {
        // Define the universe of trackable domain events
        $actions = [
            'post_created',
            'comment_created',
            'vote_up',
            'vote_down',
            'vote_removed',
            'authors_pick_selected',
            'reputation_changed',
            'login',
            'logout',
        ];

        $action = $this->faker->randomElement($actions);
        $target = null;

        // Polymorphic resolution: Attach the audit trail to the corresponding database entity
        if (str_contains($action, 'post')) {
            $target = Post::inRandomOrder()->first();
        } elseif (str_contains($action, 'comment') || str_contains($action, 'vote')) {
            $target = Comment::inRandomOrder()->first();
        }

        return [
            'user_id'     => User::inRandomOrder()->first()->id,
            'action'      => $action,
            'target_id'   => $target?->id,
            'target_type' => $target ? get_class($target) : null,
            'details'     => $this->faker->sentence(),
            'created_at'  => $this->faker->dateTimeBetween('-30 days'),
        ];
    }
}
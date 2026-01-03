<?php

namespace Database\Factories;

use App\Models\UserActivity;
use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserActivityFactory extends Factory
{
    protected $model = UserActivity::class;

    public function definition(): array
    {
        $actions = [
            'post_created',
            'comment_created',
            'vote_up',
            'vote_down',
            'vote_removed',
            'best_answer_selected',
            'reputation_changed',
            'login',
            'logout',
        ];

        $action = $this->faker->randomElement($actions);

        $target = null;
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

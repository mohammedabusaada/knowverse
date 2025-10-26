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
        $actions = ['post', 'comment', 'vote', 'follow', 'report', 'login', 'logout'];
        $action = $this->faker->randomElement($actions);

        $target_id = null;
        $target_type = null;
        $details = null;

        if (in_array($action, ['post', 'comment', 'vote', 'report'])) {
            $target_type = $this->faker->randomElement([Post::class, Comment::class]);
            $target = $target_type::inRandomOrder()->first();
            $target_id = $target->id;
            $details = "Action: $action on " . class_basename($target_type) . " #$target_id";
        }

        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'action' => $action,
            'target_id' => $target_id,
            'target_type' => $target_type,
            'details' => $details,
            'created_at' => now()->format('Y-m-d H:i:s'),
        ];
    }
}

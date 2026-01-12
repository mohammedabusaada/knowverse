<?php

namespace Database\Factories;

use App\Models\Notification;
use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationFactory extends Factory
{

    protected $model = Notification::class;
    
    public function definition(): array
    {
        $types = ['post_commented', 'post_voted', 'user_followed', 'system_alert'];
        $type = $this->faker->randomElement($types);

        $actor = User::inRandomOrder()->first() ?? User::factory()->create();
        
        $targetId = null;
        $targetType = null;

        // Handle polymorphic target assignment based on event type
        if (in_array($type, ['post_commented', 'post_voted'])) {
            $morphTarget = $this->faker->randomElement([Post::class, Comment::class]);
            $target = $morphTarget::inRandomOrder()->first();

            if ($target) {
                $targetId = $target->id;
                $targetType = $morphTarget;
            }
        } elseif ($type === 'user_followed') {
            $targetId = $actor->id;
            $targetType = User::class;
        }

        $isRead = $this->faker->boolean(70);

        return [
            'user_id' => User::inRandomOrder()->first()->id ?? User::factory(),
            'actor_id' => $actor->id,
            'target_id' => $targetId,
            'target_type' => $targetType,
            'type' => $type,
            'message' => $this->faker->sentence(),
            'is_read' => $isRead,
            'read_at' => $isRead ? $this->faker->dateTimeBetween('-1 month', 'now') : null,
            'created_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'updated_at' => now(),
        ];
    }
}
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
        $types = ['comment', 'vote', 'follow', 'system'];
        $type = $this->faker->randomElement($types);

        // --- Actor and recipient (memory-efficient, safe) ---
        $actor = User::inRandomOrder()->first();
        $recipient = User::where('id', '!=', $actor->id)->inRandomOrder()->first() ?? $actor;

        // --- Initialize related content ---
        $related_id = null;
        $related_type = null;

        if (in_array($type, ['comment', 'vote'])) {
            $targetType = $this->faker->randomElement([Post::class, Comment::class]);
            $target = $targetType::inRandomOrder()->first();

            if ($target) {
                $related_id = $target->id;
                $related_type = $targetType;
            }
        } elseif ($type === 'follow') {
            $related_id = $actor->id;
            $related_type = User::class;
        }

        return [
            'user_id' => $recipient->id,
            'actor_id' => $actor->id,
            'related_content_id' => $related_id,
            'related_content_type' => $related_type,
            'type' => $type,
            'message' => $this->faker->sentence(),
            'is_read' => $this->faker->boolean(70),
            'created_at' => now()->format('Y-m-d H:i:s'),
            'updated_at' => now()->format('Y-m-d H:i:s'),
        ];
    }
}

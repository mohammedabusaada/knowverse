<?php

namespace Database\Factories;

use App\Models\Notification;
use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Tag;
use App\Enums\NotificationType;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    public function definition(): array
    {
        // Pick a random enum case directly
        $type = $this->faker->randomElement(NotificationType::cases());

        $actor = User::inRandomOrder()->first() ?? User::factory()->create();
        
        $targetId = null;
        $targetType = null;

        // Logic based on the Enum Value
        switch ($type) {
            case NotificationType::POST_COMMENTED:
            case NotificationType::POST_UPVOTED:
            case NotificationType::POST_DOWNVOTED:
                $target = Post::inRandomOrder()->first();
                $targetId = $target?->id;
                $targetType = Post::class;
                break;

            case NotificationType::COMMENT_REPLIED:
            case NotificationType::COMMENT_UPVOTED:
            case NotificationType::COMMENT_DOWNVOTED:
                $target = Comment::inRandomOrder()->first();
                $targetId = $target?->id;
                $targetType = Comment::class;
                break;

            case NotificationType::USER_FOLLOWED:
                $targetId = User::inRandomOrder()->value('id');
                $targetType = User::class;
                break;

            case NotificationType::TAG_FOLLOWED:
                $targetId = Tag::inRandomOrder()->value('id');
                $targetType = Tag::class;
                break;
        }

        $isRead = $this->faker->boolean(70);

        return [
            'user_id'     => User::inRandomOrder()->value('id') ?? User::factory(),
            'actor_id'    => $actor->id,
            'target_id'   => $targetId,
            'target_type' => $targetType,
            'type'        => $type, // Laravel will handle the Enum casting
            'message'     => $this->faker->sentence(),
            'is_read'     => $isRead,
            'read_at'     => $isRead ? $this->faker->dateTimeBetween('-1 month', 'now') : null,
            'created_at'  => $this->faker->dateTimeBetween('-1 month', 'now'),
            'updated_at'  => now(),
        ];
    }
}
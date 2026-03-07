<?php

namespace Database\Factories;

use App\Models\{Notification, User, Post, Comment, Tag};
use App\Enums\NotificationType;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    public function definition(): array
    {
        // Dynamically resolve a valid notification state from the backed Enum
        $type = $this->faker->randomElement(NotificationType::cases());
        $actor = User::inRandomOrder()->first() ?? User::factory()->create();
        
        $targetId = null;
        $targetType = null;

        // Polymorphic Target Resolution: Map the notification type to the appropriate Eloquent model
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

        // Simulate realistic user engagement (e.g., 70% read rate)
        $isRead = $this->faker->boolean(70);

        return [
            'user_id'     => User::inRandomOrder()->value('id') ?? User::factory(),
            'actor_id'    => $actor->id,
            'target_id'   => $targetId,
            'target_type' => $targetType,
            'type'        => $type, // Utilize Eloquent's native Enum casting for persistence
            'message'     => $this->faker->sentence(),
            'is_read'     => $isRead,
            'read_at'     => $isRead ? $this->faker->dateTimeBetween('-1 month', 'now') : null,
            'created_at'  => $this->faker->dateTimeBetween('-1 month', 'now'),
            'updated_at'  => now(),
        ];
    }
}
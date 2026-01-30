<?php

namespace App\Presenters;

use App\Models\Notification;
use App\Models\Post;
use App\Models\Comment;
use App\Enums\NotificationType;
use Illuminate\Support\Str;

class NotificationPresenter
{
    public function __construct(
        protected Notification $notification
    ) {}

    /**
     * Human readable message.
     */
    public function message(): string
    {
        $actor = $this->actorName();

        return match ($this->notification->type) {

            // -------------------------
            // Comments
            // -------------------------
            NotificationType::POST_COMMENTED =>
            "{$actor} commented on your post",

            NotificationType::COMMENT_REPLIED =>
            "{$actor} replied to your comment",

            // -------------------------
            // Votes
            // -------------------------
            NotificationType::POST_UPVOTED =>
            "{$actor} upvoted your post",

            NotificationType::POST_DOWNVOTED =>
            "{$actor} downvoted your post",

            NotificationType::COMMENT_UPVOTED =>
            "{$actor} upvoted your comment",

            NotificationType::COMMENT_DOWNVOTED =>
            "{$actor} downvoted your comment",

            // -------------------------
            // Best Answer
            // -------------------------
            NotificationType::BEST_ANSWER_RECEIVED =>
            "Your comment was marked as best answer",

            // -------------------------
            // Follow
            // -------------------------
            NotificationType::USER_FOLLOWED =>
            "{$actor} started following you",

            NotificationType::TAG_FOLLOWED =>
            "{$actor} started following your tag",

            // -------------------------
            // System / fallback
            // -------------------------
            default =>
            $this->notification->message
                ?? Str::headline($this->notification->type),
        };
    }

    /**
     * URL user should go to when clicking.
     */
    public function url(): ?string
    {
        $target = $this->notification->target;

        return match (true) {

            $target instanceof Post =>
            route('posts.show', $target),

            $target instanceof Comment =>
            route('posts.show', $target->post) . "#comment-{$target->id}",

            default => null,
        };
    }

    /**
     * Actor display name.
     */
    protected function actorName(): string
    {
        return $this->notification->actor
            ? $this->notification->actor->username
            : 'System';
    }

    public function icon(): string
    {
        return match ($this->notification->type) {

            // Comments
            NotificationType::POST_COMMENTED,
            NotificationType::COMMENT_REPLIED => '💬',


            // Votes
            NotificationType::POST_UPVOTED,
            NotificationType::COMMENT_UPVOTED => '👍',

            NotificationType::POST_DOWNVOTED,
            NotificationType::COMMENT_DOWNVOTED => '👎',

            // Best answer
            NotificationType::BEST_ANSWER_RECEIVED => '🏆',


            // Follow
            NotificationType::USER_FOLLOWED => '👤',
            NotificationType::TAG_FOLLOWED => '🏷️',


            // System / fallback
            default => '🔔',
        };
    }
}

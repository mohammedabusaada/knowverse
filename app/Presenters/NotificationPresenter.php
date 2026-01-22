<?php

namespace App\Presenters;

use App\Models\Notification;
use App\Models\Post;
use App\Models\Comment;
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
            'post_commented' =>
            "{$actor} commented on your post",

            'comment_replied' =>
            "{$actor} replied to your comment",

            // -------------------------
            // Votes
            // -------------------------
            'post_voted_up' =>
            "{$actor} upvoted your post",

            'post_voted_down' =>
            "{$actor} downvoted your post",

            'comment_voted_up' =>
            "{$actor} upvoted your comment",

            'comment_voted_down' =>
            "{$actor} downvoted your comment",

            // -------------------------
            // Best Answer
            // -------------------------
            'best_answer_received' =>
            "Your comment was marked as best answer",

            'best_answer_awarded' =>
            "You awarded a best answer",

            // -------------------------
            // Follow
            // -------------------------
            'user_followed' =>
            "{$actor} started following you",

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
            'post_commented',
            'comment_replied' => '💬',

            // Votes
            'post_voted_up',
            'comment_voted_up' => '👍',

            'post_voted_down',
            'comment_voted_down' => '👎',

            // Best answer
            'best_answer_received',
            'best_answer_awarded' => '🏆',

            // Follow
            'user_followed' => '👤',

            // System / fallback
            default => '🔔',
        };
    }
}

<?php

namespace App\Presenters;

use App\Models\Notification;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Report;
use App\Enums\NotificationType;
use Illuminate\Support\Str;

class NotificationPresenter
{
    public function __construct(protected Notification $notification) {}

    public function message(): string
    {
        $actor = $this->actorName();

        return match ($this->notification->type) {
            NotificationType::REPORT_RESOLVED => 
                "Action was taken on a report you submitted. Thanks for keeping the community safe!",

            NotificationType::CONTENT_REMOVED => 
                "Your content was hidden for violating community guidelines.",

            NotificationType::POST_COMMENTED => "{$actor} commented on your post",
            NotificationType::COMMENT_REPLIED => "{$actor} replied to your comment",
            NotificationType::POST_UPVOTED => "{$actor} upvoted your post",
            NotificationType::POST_DOWNVOTED => "{$actor} downvoted your post",
            NotificationType::COMMENT_UPVOTED => "{$actor} upvoted your comment",
            NotificationType::COMMENT_DOWNVOTED => "{$actor} downvoted your comment",
            NotificationType::BEST_ANSWER_RECEIVED => "Your comment was marked as the best answer!",
            NotificationType::USER_FOLLOWED => "{$actor} started following you",
            NotificationType::TAG_FOLLOWED => "{$actor} followed a tag you created",

            default => $this->notification->message ?? Str::headline($this->notification->type->value),
        };
    }

    public function url(): ?string
    {
        $target = $this->notification->target;

        if (!$target) {
            return route('notifications.index');
        }

        return match (true) {
            $target instanceof Post => route('posts.show', $target),
            $target instanceof Comment => $target->post 
                ? route('posts.show', $target->post) . "#comment-{$target->id}"
                : route('notifications.index'),
            $target instanceof Report => route('notifications.index'),
            default => route('notifications.index'),
        };
    }

    protected function actorName(): string
    {
        return $this->notification->actor ? $this->notification->actor->username : 'System';
    }

    public function icon(): string
    {
        return match ($this->notification->type) {
            NotificationType::REPORT_RESOLVED => '✅',
            NotificationType::CONTENT_REMOVED => '⚠️',
            NotificationType::POST_COMMENTED, NotificationType::COMMENT_REPLIED => '💬',
            NotificationType::POST_UPVOTED, NotificationType::COMMENT_UPVOTED => '👍',
            NotificationType::POST_DOWNVOTED, NotificationType::COMMENT_DOWNVOTED => '👎',
            NotificationType::BEST_ANSWER_RECEIVED => '🏆',
            NotificationType::USER_FOLLOWED => '👤',
            NotificationType::TAG_FOLLOWED => '🏷️',
            default => '🔔',
        };
    }
}
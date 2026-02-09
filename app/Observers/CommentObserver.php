<?php

namespace App\Observers;

use App\Models\Comment;
use App\Services\ActivityService;
use App\Services\NotificationService;
use App\Services\ContentFilter;
use App\Enums\NotificationType;
use Illuminate\Validation\ValidationException;

class CommentObserver
{
    public function __construct(protected ContentFilter $filter) {}

    /**
     * Check content before saving (Create or Update)
     */
    public function saving(Comment $comment): void
{
    if ($error = $this->filter->getValidationError($comment->body)) {
        throw ValidationException::withMessages(['body' => $error]);
    }
}

    /**
     * Comment created.
     */
    public function created(Comment $comment): void
    {
        $author = $comment->user;
        ActivityService::commentCreated($author, $comment);
        $author->addReputation('comment_created', null, $comment);

        $notificationService = app(NotificationService::class);
        $postAuthor = $comment->post->user;

        if ($postAuthor->id !== $author->id) {
            $notificationService->notify(
                recipient: $postAuthor,
                type: NotificationType::POST_COMMENTED,
                actor: $author,
                target: $comment
            );
        }

        if ($comment->parent) {
            $parentAuthor = $comment->parent->user;
            if ($parentAuthor->id !== $author->id && $parentAuthor->id !== $postAuthor->id) {
                $notificationService->notify(
                    recipient: $parentAuthor,
                    type: NotificationType::COMMENT_REPLIED,
                    actor: $author,
                    target: $comment
                );
            }
        }
    }

    /**
     * Comment soft deleted.
     */
    public function deleting(Comment $comment): void
    {
        $author = $comment->user;
        $author->removeReputation('comment_created', $comment);

        if ($comment->post?->best_comment_id === $comment->id) {
            $author->removeReputation('best_answer_received', $comment);
            $comment->post->user->removeReputation('best_answer_awarded', $comment);
        }

        foreach ($comment->votes as $vote) {
            $author->removeReputation(
                $vote->value === 1 ? 'comment_upvoted' : 'comment_downvoted',
                $comment
            );
        }
    }
}
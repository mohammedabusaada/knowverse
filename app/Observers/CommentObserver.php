<?php

namespace App\Observers;

use App\Models\Comment;
use App\Services\ActivityService;
use App\Services\NotificationService;
use App\Support\NotificationType;

class CommentObserver
{
    /**
     * Comment created.
     */
    public function created(Comment $comment): void
    {
        $author = $comment->user;

        // ----------------------------
        // Activity
        // ----------------------------
        ActivityService::commentCreated($author, $comment);

        // ----------------------------
        // Reputation
        // ----------------------------
        $author->addReputation(
            'comment_created',
            null,
            $comment
        );

        $notificationService = app(NotificationService::class);

        $postAuthor = $comment->post->user;

        // ----------------------------
        // Notify post author
        // ----------------------------
        if ($postAuthor->id !== $author->id) {
            $notificationService->notify(
                recipient: $postAuthor,
                type: NotificationType::POST_COMMENTED,
                actor: $author,
                target: $comment
            );
        }

        // ----------------------------
        // Notify parent comment author (reply)
        // ----------------------------
        if ($comment->parent) {
            $parentAuthor = $comment->parent->user;

            if (
                $parentAuthor->id !== $author->id &&
                $parentAuthor->id !== $postAuthor->id
            ) {
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

        // Undo: comment creation reputation
        $author->removeReputation(
            'comment_created',
            $comment
        );

        // Undo: best answer effects
        if ($comment->post?->best_comment_id === $comment->id) {

            // Comment author
            $author->removeReputation(
                'best_answer_received',
                $comment
            );

            // Post author
            $comment->post->user->removeReputation(
                'best_answer_awarded',
                $comment
            );
        }

        // Undo: votes reputation
        foreach ($comment->votes as $vote) {
            $author->removeReputation(
                $vote->value === 1
                    ? 'comment_voted_up'
                    : 'comment_voted_down',
                $comment
            );
        }
    }
}

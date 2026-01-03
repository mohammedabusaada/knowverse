<?php

namespace App\Observers;

use App\Models\Comment;
use App\Services\ActivityService;

class CommentObserver
{
    /**
     * Comment created.
     */
    public function created(Comment $comment): void
    {
        $author = $comment->user;

        ActivityService::commentCreated($author, $comment);

        $author->addReputation(
            'comment_created',
            null,
            $comment
        );
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

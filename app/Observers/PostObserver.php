<?php

namespace App\Observers;

use App\Models\Post;
use App\Services\ActivityService;

class PostObserver
{
    /**
     * Post created.
     */
    public function created(Post $post): void
    {
        $author = $post->user;

        // ----------------------------
        // Activity
        // ----------------------------
        ActivityService::postCreated($author, $post);

        // ----------------------------
        // Reputation
        // ----------------------------
        $author->addReputation(
            'post_created',
            null,
            $post
        );
    }

    /**
     * Post soft deleted.
     */
    public function deleting(Post $post): void
    {
        $author = $post->user;

        // Undo: post creation reputation
        $author->removeReputation(
            'post_created',
            $post
        );

        // Undo: best answer effects
        if ($post->bestComment) {

            // Post author
            $author->removeReputation(
                'best_answer_awarded',
                $post->bestComment
            );

            // Comment author
            $post->bestComment->user->removeReputation(
                'best_answer_received',
                $post->bestComment
            );
        }

        // Undo: votes reputation
        foreach ($post->votes as $vote) {
            $author->removeReputation(
                $vote->value === 1
                    ? 'post_upvoted'
                    : 'post_downvoted',
                $post
            );
        }
    }
}

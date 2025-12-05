<?php

namespace App\Observers;

use App\Models\Post;

class PostObserver
{
    public function deleting(Post $post)
    {
        $author = $post->user;

        // Undo: post_created
        $author->removeReputation('post_created', $post);

        // Undo: best answer awarded
        if ($post->best_comment_id) {
            // Undo for post author
            $author->removeReputation('best_answer_awarded', $post->bestComment);

            // Undo for comment author
            $post->bestComment->user->removeReputation('best_answer_received', $post->bestComment);
        }

        // Undo: votes on post
        foreach ($post->votes as $vote) {
            if ($vote->value === 1) {
                $post->user->removeReputation('post_upvoted', $post);
            } elseif ($vote->value === -1) {
                $post->user->removeReputation('post_downvoted', $post);
            }
        }
    }
}

<?php

namespace App\Observers;

use App\Models\Comment;

class CommentObserver
{
    public function deleting(Comment $comment)
    {
        $author = $comment->user;

        // Undo: comment_created
        $author->removeReputation('comment_created', $comment);

        // Undo: best answer received
        if ($comment->post->best_comment_id === $comment->id) {

            // Undo reputation for comment author
            $author->removeReputation('best_answer_received', $comment);

            // Undo reputation for post author
            $comment->post->user->removeReputation('best_answer_awarded', $comment);
        }

        // Undo: votes on comment
        foreach ($comment->votes as $vote) {
            if ($vote->value === 1) {
                $author->removeReputation('comment_upvoted', $comment);
            } elseif ($vote->value === -1) {
                $author->removeReputation('comment_downvoted', $comment);
            }
        }
    }
}

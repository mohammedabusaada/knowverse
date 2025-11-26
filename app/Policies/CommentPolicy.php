<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;

class CommentPolicy
{
    /**
     * User can update their own comment or admin can update any comment.
     */
    public function update(User $user, Comment $comment): bool
    {
        return $user->id === $comment->user_id || $user->isAdmin();
    }

    /**
     * User can delete their own comment or admin can delete any comment.
     */
    public function delete(User $user, Comment $comment): bool
    {
        return $user->id === $comment->user_id || $user->isAdmin();
    }

    public function markBest(User $user, Comment $comment): bool
{
    // Only the author of the post can select a best comment
    return $comment->post->user_id === $user->id;
}

public function unmarkBest(User $user, Comment $comment): bool
{
    return $comment->post->user_id === $user->id;
}

}

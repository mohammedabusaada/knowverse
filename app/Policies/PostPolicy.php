<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

/**
 * Enforces authorization logic for core scholarly discussions.
 */
class PostPolicy
{
    /**
     * Allows public viewing of the discussion.
     */
    public function view(?User $user, Post $post): bool
    {
        return true;
    }

    /**
     * Write Access: Restricts creation to registered and authenticated scholars only.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Modification Access: Restricts modifications to the original author or the moderation team
     */
    public function update(User $user, Post $post): bool
    {
        return $user->id === $post->user_id || $user->canModerate();
    }

    /**
     * Deletion Access: Restricts deletion to the original author or the moderation team.
     */
    public function delete(User $user, Post $post): bool
    {
        return $user->id === $post->user_id || $user->canModerate();
    }
}
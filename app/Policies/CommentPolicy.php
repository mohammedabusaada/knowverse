<?php

namespace App\Policies;

use App\Models\{Comment, User};

/**
 * Enforces authorization logic for scholarly responses.
 * Protects content mutation and governs the "Author's Pick" reputation economy.
 */
class CommentPolicy
{
/**
     * Determines whether a user can modify a specific response.
     * Grants access exclusively to the original author or the moderation team.
     */
    public function update(User $user, Comment $comment): bool
    {
        return $user->id === $comment->user_id || $user->canModerate();
    }

/**
     * Determines whether a user can delete a specific response.
     * Restricts destructive actions to the owner or administrative personnel.
     */
    public function delete(User $user, Comment $comment): bool
    {
        return $user->id === $comment->user_id || $user->canModerate();
    }

/**
     * GOVERNANCE & ANTI-FARMING SECURITY:
     * 1. Grants the discussion owner the authority to highlight a response as the 'Author's Pick'.
     * 2. Strictly prohibits self-awarding to prevent reputation point manipulation.
     */
    public function markBest(User $user, Comment $comment): bool
    {
        return $comment->post->user_id === $user->id && $comment->user_id !== $user->id;
    }

    /**
     * Retraction Policy: Only the original discussion owner can revoke the 'Author's Pick' designation.
     */
    public function unmarkBest(User $user, Comment $comment): bool
    {
        return $comment->post->user_id === $user->id;
    }
}
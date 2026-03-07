<?php

namespace App\Policies;

use App\Models\User;

/**
 * Governs access control for the platform's social graph and networking features.
 */
class FollowPolicy
{
    /**
     * Authorization guard to prevent self-referential graph relationships.
     */
    public function follow(User $user, User $target): bool
    {
        return $user->id !== $target->id;
    }

    /**
     * Resolves visibility clearance for a scholar's network (Followers/Following).
     * Implements a hierarchical permission check.
     */
    public function viewLists(User $user, User $target): bool
    {
        // 1. Identity Verification: Users maintain full access to their own network graphs
        if ($user->id === $target->id) return true;

        // 2. Administrative Override: Moderators & Admins possess global visibility
        if ($user->canModerate()) return true;

        // 3. Privacy Preferences: Fall back to the target user's configured social privacy settings
        return (bool) $target->public_follow_lists;
    }
}

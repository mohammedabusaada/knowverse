<?php

namespace App\Policies;

use App\Models\User;

class FollowPolicy
{
    public function follow(User $user, User $target): bool
    {
        return $user->id !== $target->id;
    }

    public function viewLists(User $user, User $target): bool
    {
        // 1. Owners can see their own lists
        if ($user->id === $target->id) return true;

        // 2. Admins can see everything
        if ($user->isAdmin()) return true;

        // 3. Otherwise, respect the toggle
        return (bool) $target->public_follow_lists;
    }
}

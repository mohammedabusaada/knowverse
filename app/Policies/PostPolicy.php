<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    /**
     * Allow viewing by anyone (even guests).
     */
    public function view(?User $user, Post $post): bool
    {
        return true;
    }

    /**
     * Only authenticated users can create.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Users can update their own posts or admins can update all.
     */
    public function update(User $user, Post $post): bool
    {
        return $user->id === $post->user_id || $user->role_id === 1; // admin role = 1?
    }

    /**
     * Users can delete their own posts or admins can delete all.
     */
    public function delete(User $user, Post $post): bool
    {
        return $user->id === $post->user_id || $user->role_id === 1;
    }
}

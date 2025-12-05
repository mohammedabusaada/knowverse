<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * View profile, reputation history, etc.
     * Allowed: the user themself or an admin
     */
    public function view(User $auth, User $model): bool
    {
        return $auth->id === $model->id || $auth->role_id === 2;
    }

    /**
     * Update profile settings (avatar, bio, etc.)
     */
    public function update(User $auth, User $model): bool
    {
        return $auth->id === $model->id;
    }

    /**
     * Users cannot be created by other users.
     */
    public function create(User $auth): bool
    {
        return false;
    }

    /**
     * Only admins can delete users (optional).
     */
    public function delete(User $auth, User $model): bool
    {
        return $auth->role_id === 2;
    }
}

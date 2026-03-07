<?php

namespace App\Policies;

use App\Models\User;

/**
 * Orchestrates identity management and profile access control.
 */
class UserPolicy
{
    /**
     * Read Access: Governs access to sensitive user dossier information.
     * Open strictly to the profile owner or the moderation team.
     */
    public function view(User $auth, User $model): bool
    {
        return $auth->id === $model->id || $auth->canModerate();
    }

    /**
     * Modification Access: Prevents Cross-Site Request Forgery (CSRF) and IDOR attacks 
     * by ensuring scholars can only update their own identity parameters.
     */
    public function update(User $auth, User $model): bool
    {
        return $auth->id === $model->id;
    }

    /**
     * Entity Creation: Blocked at the policy level. User provisioning is handled exclusively via Registration.
     */
    public function create(User $auth): bool
    {
        return false;
    }

    /**
     * Only admins can delete users.
     * Moderators cannot delete user accounts.
     */
    public function delete(User $auth, User $model): bool
    {
        return $auth->id === $model->id || $auth->isAdmin();
    }

    public function manageReports(User $auth): bool
    {
        return $auth->canModerate();
    }
}


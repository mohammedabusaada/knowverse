<?php

namespace App\Policies;

use App\Models\Notification;
use App\Models\User;

/**
 * Enforces data isolation for user communication.
 * Guarantees that notification payloads are strictly accessible only to their intended recipients.
 */
class NotificationPolicy
{
    /**
     * Read Access: Verify payload ownership.
     */
    public function view(User $user, Notification $notification): bool
    {
        return $notification->user_id === $user->id;
    }

    /**
     * User can update (mark as read) their own notifications.
     */
    public function update(User $user, Notification $notification): bool
    {
        return $notification->user_id === $user->id;
    }

    /**
     * User can delete their own notifications.
     */
    public function delete(User $user, Notification $notification): bool
    {
        return $notification->user_id === $user->id;
    }

    /**
     * Gateway Authorization.
     * Note: Returns true as data isolation is handled downstream via Eloquent query scoping.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }
}

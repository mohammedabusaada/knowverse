<?php

namespace App\Policies;

use App\Models\Notification;
use App\Models\User;

class NotificationPolicy
{
    /**
     * User can view their own notifications.
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

    public function viewAny(User $user): bool
    {
        return true;
    }
}

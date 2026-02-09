<?php

namespace App\Services;

use App\Models\User;
use App\Enums\NotificationType;

class NotificationPreferenceService
{
    /**
     * Core logic to check if a user should receive a specific notification.
     */
    public function shouldNotify(User $user, NotificationType $type): bool
    {
        // 1. Mandatory notifications always bypass user preferences
        if ($type->isMandatory()) {
            return true;
        }

        // 2. Check user's specific setting in the database
        return $user->notificationEnabled($type);
    }
}
<?php

namespace App\Services;

use App\Models\User;
use App\Enums\NotificationType;

class NotificationPreferenceService
{
    public function shouldNotify(User $user, NotificationType $type): bool
    {
        // Pass the ->value (string) to the user model helper
        return $user->notificationEnabled($type->value);
    }
}

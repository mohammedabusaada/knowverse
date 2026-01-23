<?php

namespace App\Services;
use App\Models\User;


class NotificationPreferenceService
{
     public function shouldNotify(User $user, string $type): bool
    {
        return $user->notificationEnabled($type);
    }
}

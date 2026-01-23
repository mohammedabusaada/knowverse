<?php

namespace App\Policies;

use App\Models\NotificationPreference;
use App\Models\User;
use Illuminate\Auth\Access\Response;


class NotificationPreferencePolicy
{
        public function update(User $user, NotificationPreference $preference): bool
    {
        return $user->id === $preference->user_id;
    }
}

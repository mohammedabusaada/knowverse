<?php

namespace App\Policies;

use App\Models\NotificationPreference;
use App\Models\User;
use Illuminate\Auth\Access\Response;

/**
 * Secures user configuration states.
 * Ensures scholars can exclusively mutate their own notification delivery preferences.
 */
class NotificationPreferencePolicy
{
        public function update(User $user, NotificationPreference $preference): bool
    {
        return $user->id === $preference->user_id;
    }
}

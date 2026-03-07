<?php

namespace App\Observers;

use App\Models\User;
use App\Models\NotificationPreference;

/**
 * Oversees User lifecycle and default configuration provisioning. [cite: 37]
 */
class UserObserver
{
/**
     * Initializes default Notification Preferences upon account provisioning. [cite: 38]
     * Ensures every new scholar has a standard experience from the first login. [cite: 39, 40]
     */
   public function created(User $user): void
{
    foreach (config('notification-preferences.categories') as $type => $data) {
        NotificationPreference::firstOrCreate(
            [
                'user_id' => $user->id,
                'type'    => $type,
            ],
            [
                'enabled' => $data['default'] ?? true,
            ]
        );
    }
}

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        //
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }

}

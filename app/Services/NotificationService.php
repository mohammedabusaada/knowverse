<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class NotificationService
{
    /**
     * Create a notification.
     *
     * @param  User        $recipient
     * @param  string      $type
     * @param  User|null   $actor
     * @param  Model|null  $target
     * @param  string|null $message
     */
    public function notify(
        User $recipient,
        string $type,
        ?User $actor = null,
        ?Model $target = null,
        ?string $message = null
    ): ?Notification {
        // --------------------------------------------------------------
        // 1. Prevent self-notifications
        // --------------------------------------------------------------
        if ($actor && $actor->id === $recipient->id) {
            return null;
        }

        // --------------------------------------------------------------
        // 2. Preferences check (stub for now)
        // --------------------------------------------------------------
        if (! $this->shouldNotify($recipient, $type)) {
            return null;
        }

        // --------------------------------------------------------------
        // 3. Create notification
        // --------------------------------------------------------------
        return Notification::create([
            'user_id' => $recipient->id,
            'actor_id' => $actor?->id,
            'type' => $type,
            'message' => $message,
            'target_id' => $target?->getKey(),
            'target_type' => $target ? get_class($target) : null,
        ]);
    }

    /**
     * Determine whether the user should receive this notification.
     * (Will be fully implemented in notification-preferences branch)
     */
    protected function shouldNotify(User $user, string $type): bool
    {
        return true; // temporary default
    }

    /**
     * Mark all notifications as read for a user.
     */
    public function markAllAsRead(User $user): int
    {
        return Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }
}

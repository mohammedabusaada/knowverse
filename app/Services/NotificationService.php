<?php

namespace App\Services;

use App\Enums\NotificationType;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class NotificationService
{
    public function __construct(
        protected NotificationPreferenceService $preferences
    ) {}

    /**
     * Create and send a notification to a user.
     * * @param User $recipient The user receiving the notification
     * @param NotificationType $type The Enum case (e.g., NotificationType::SYSTEM)
     * @param User|null $actor The user who triggered the action
     * @param Model|null $target The related model (Post, Comment, etc.)
     * @param string|null $message Optional custom text override
     * @return Notification|null
     */
    public function notify(
        User $recipient,
        NotificationType $type,
        ?User $actor = null,
        ?Model $target = null,
        ?string $message = null
    ): ?Notification {

        // --------------------------------------------------------------
        // 1. Prevent self-notifications
        // --------------------------------------------------------------
        // We don't want to notify a user about their own actions.
        if ($actor && $actor->id === $recipient->id) {
            return null;
        }

        // --------------------------------------------------------------
        // 2. Identification & Preferences Check
        // --------------------------------------------------------------
        // If the type is mandatory (defined in the Enum), we skip the user settings check.
        $isMandatory = $type->isMandatory();

        if (!$isMandatory && !$this->preferences->shouldNotify($recipient, $type)) {
            return null;
        }

        // --------------------------------------------------------------
        // 3. Create notification record
        // --------------------------------------------------------------
        return Notification::create([
            'user_id'     => $recipient->id,
            'actor_id'    => $actor?->id,
            'type'        => $type, // Laravel casts this to string 'system', 'post_commented', etc.
            'message'     => $message,
            'target_id'   => $target?->getKey(),
            'target_type' => $target ? get_class($target) : null,
        ]);
    }

    /**
     * Mark all unread notifications as read for a specific user.
     *
     * @param User $user
     * @return int Number of rows updated
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

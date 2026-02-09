<?php

namespace App\Services;

use App\Enums\NotificationType;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class NotificationService
{
    public function __construct(
        protected NotificationPreferenceService $preferences
    ) {}

    /**
     * Create and send a notification to a user.
     */
    public function notify(
        User $recipient,
        NotificationType $type,
        ?User $actor = null,
        ?Model $target = null,
        ?string $message = null
    ): ?Notification {

        // 1. Prevent self-notifications
        if ($actor && $actor->id === $recipient->id) {
            return null;
        }

        // 2. Preferences Check
        if (!$type->isMandatory() && !$this->preferences->shouldNotify($recipient, $type)) {
            return null;
        }

        // 3. Handle "Deduplication" for specific types (e.g., Votes)
        // We don't want to create a new row every time someone clicks upvote/downvote
        if ($this->shouldDeDuplicate($type)) {
            $existing = Notification::where([
                'user_id'     => $recipient->id,
                'type'        => $type,
                'target_id'   => $target?->getKey(),
                'target_type' => $target ? get_class($target) : null,
            ])->first();

            if ($existing) {
                $existing->update([
                    'actor_id' => $actor?->id, // Update to the latest person who interacted
                    'is_read'  => false,       // Pop it back up as unread
                    'created_at' => now(),     // Bring to top of list
                ]);
                return $existing;
            }
        }

        // 4. Create notification record
        return Notification::create([
            'user_id'     => $recipient->id,
            'actor_id'    => $actor?->id,
            'type'        => $type,
            'message'     => $message,
            'target_id'   => $target?->getKey(),
            'target_type' => $target ? get_class($target) : null,
            'is_read'     => false,
        ]);
    }

    /**
     * Determine which notification types should not be duplicated.
     */
    protected function shouldDeDuplicate(NotificationType $type): bool
    {
        return in_array($type, [
            NotificationType::POST_UPVOTED,
            NotificationType::POST_DOWNVOTED,
            NotificationType::COMMENT_UPVOTED,
            NotificationType::COMMENT_DOWNVOTED,
            NotificationType::USER_FOLLOWED,
        ]);
    }

    /**
     * Mark all unread notifications as read.
     */
    public function markAllAsRead(User $user): int
    {
        return Notification::where('user_id', $user->id)
            ->unread()
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }
}
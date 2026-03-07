<?php

namespace App\Services;

use App\Enums\NotificationType;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

/**
 * Centralized Notification Dispatcher.
 * Orchestrates multi-channel alerts (Database & WebSockets) while enforcing user privacy 
 * preferences and preventing notification fatigue (Deduplication).
 */
class NotificationService
{
    public function __construct(
        protected NotificationPreferenceService $preferences
    ) {}

    /**
     * Dispatch an alert, subject to deduplication and privacy policy constraints.
     */
    public function notify(
        User $recipient,
        NotificationType $type,
        ?User $actor = null,
        ?Model $target = null,
        ?string $message = null
    ): ?Notification {

        // 1. Guard: Silent termination if the actor and recipient are identical (Self-action)
        if ($actor && $actor->id === $recipient->id) {
            return null;
        }

        // 2. Guard: Respect explicit opt-out preferences (unless it is a mandatory system alert)
        if (!$type->isMandatory() && !$this->preferences->shouldNotify($recipient, $type)) {
            return null;
        }

        // 3. Optimization: Deduplicate high-frequency events (e.g., toggling upvotes rapidly)
        if ($this->shouldDeDuplicate($type)) {
            $existing = Notification::where([
                'user_id'     => $recipient->id,
                'type'        => $type,
                'target_id'   => $target?->getKey(),
                'target_type' => $target ? get_class($target) : null,
            ])->first();

            if ($existing) {
                // Surface the existing notification back to the top of the inbox feed
                $existing->update([
                    'actor_id'   => $actor?->id,
                    'is_read'    => false,
                    'created_at' => now(), 
                ]);
                return $existing;
            }
        }

        // 4. Persistence: Create a new definitive database record
        $notification = Notification::create([
            'user_id'     => $recipient->id,
            'actor_id'    => $actor?->id,
            'type'        => $type,
            'message'     => $message,
            'target_id'   => $target?->getKey(),
            'target_type' => $target ? get_class($target) : null,
            'is_read'     => false,
        ]);

        // 5. Real-Time Delivery: Broadcast to connected WebSocket clients
        try {
            event(new \App\Events\RealTimeNotification($notification));
        } catch (\Exception $e) {
            // Graceful Degradation: Log failure silently to prevent the main HTTP request from crashing 
            // if the external socket server (e.g., Reverb/Pusher) goes offline.
            Log::warning('RealTimeNotification Broadcast Failed: ' . $e->getMessage());
        }

        return $notification;
    }

    /**
     * Identifies actions that require state updates (upsert) rather than continuous row creation.
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
     * Bulk operational utility to clear the user's active inbox state.
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
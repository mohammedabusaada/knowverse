<?php

namespace App\Events;

use App\Models\Notification;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Facilitates real-time data transmission from the backend to the client UI.
 * Implementing 'ShouldBroadcastNow' bypasses the standard queue delay, 
 * ensuring immediate WebSocket delivery to the recipient.
 */
class RealTimeNotification implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $notification;

    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
    }

    /**
     * Authenticates and binds the broadcast event to a strictly private user channel.
     * Prevents unauthorized listeners from intercepting sensitive notification payloads.
     * * @return array
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('notifications.' . $this->notification->user_id),
        ];
    }

    /**
     * Defines the exact data structure transmitted to the client-side JavaScript (e.g., Alpine.js/Echo).
     * Only essential presentation data is exposed to minimize payload size over the socket connection.
     * * @return array
     */
    public function broadcastWith(): array
    {
        return [
            'id'         => $this->notification->id,
            'message'    => $this->notification->presenter()->message(), 
            'type'       => $this->notification->type,
            'url'        => route('notifications.visit', $this->notification),
            'created_at' => now()->diffForHumans(),
        ];
    }
}
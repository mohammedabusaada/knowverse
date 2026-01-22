<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Display user's notifications.
     */
    public function index(Request $request)
    {
        // Authorization intent: user viewing their notifications
        // (implicit, but explicit is better)
        $this->authorize('viewAny', Notification::class);

        $notifications = $request->user()
            ->notifications()
            ->latest()
            ->paginate(15);

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Mark a single notification as read.
     */
    public function read(Notification $notification)
    {
        $this->authorize('update', $notification);

        $notification->markAsRead();

        return back();
    }

    /**
     * Mark all notifications as read.
     */
    public function readAll(Request $request)
    {
        $user = $request->user();

        // Explicit authorization for bulk update
        // We authorize against a representative model instance
        $this->authorize('update', new Notification([
            'user_id' => $user->id,
        ]));

        $user->notifications()
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return back();
    }


    /**
     * Visit a notification:
     * - authorize
     * - mark as read
     * - redirect to target
     */
    public function visit(Notification $notification)
    {
        $this->authorize('update', $notification);

        if (! $notification->is_read) {
            $notification->markAsRead();
        }

        $url = $notification->presenter()->url();

        return $url
            ? redirect($url)
            : redirect()->route('notifications.index');
    }


    /**
     * Delete all notifications for the user.
     */
    public function clear(Request $request)
    {
        $request->user()
            ->notifications()
            ->delete();

        return back();
    }
}

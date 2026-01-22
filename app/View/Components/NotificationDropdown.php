<?php

namespace App\View\Components;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class NotificationDropdown extends Component
{
    public $notifications;
    public $unreadCount;

    public function __construct()
    {
        if (! Auth::check()) {
            $this->notifications = collect();
            $this->unreadCount = 0;
            return;
        }

        $userId = Auth::id();

        $this->unreadCount = Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->count();

        $this->notifications = Notification::where('user_id', $userId)
            ->with(['actor', 'target'])
            ->latest()
            ->limit(5)
            ->get();
    }

    public function render()
    {
        return view('components.notification-dropdown');
    }
}

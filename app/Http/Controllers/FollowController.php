<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Services\NotificationService;
use App\Enums\NotificationType;

class FollowController extends Controller
{
    /**
     * Toggles the follow status between the authenticated user and a target scholar.
     * Includes logical constraints to prevent self-following and interactions with suspended accounts.
     */
    public function toggle(User $user, NotificationService $notificationService)
    {
        // Guard Clause 1: Prevent a user from following their own account
        if (Auth::id() === $user->id) {
            return back()->with('error', 'Operation aborted: You cannot follow your own profile.');
        }

        // Guard Clause 2: Enforce platform suspension policies
        if ($user->is_banned) {
            return back()->with('error', 'This account is currently suspended and cannot be followed.');
        }

        /** * Execute the toggle operation on the many-to-many relationship pivot table.
         * The result array identifies if a record was 'attached' (followed) or 'detached' (unfollowed).
         */
        $result = Auth::user()->following()->toggle($user->id);
        $isFollowing = count($result['attached']) > 0;

        // Dispatch a real-time notification strictly upon establishing a NEW relationship
        if ($isFollowing) {
            try {
                $notificationService->notify(
                    recipient: $user,
                    type: NotificationType::USER_FOLLOWED,
                    actor: Auth::user(),
                    message: Auth::user()->display_name . ' started following your academic updates.'
                );
            } catch (\Exception $e) {
                // Fail gracefully: Log WebSocket/Queue failures silently to avoid disrupting the UX
                \Illuminate\Support\Facades\Log::warning('Follow Notification dispatch failed: ' . $e->getMessage());
            }
        }

        return back();
    }
}
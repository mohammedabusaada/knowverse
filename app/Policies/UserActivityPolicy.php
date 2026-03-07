<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserActivity;
use App\Support\ActivityVisibility;

/**
 * Contextual Authorization Policy for the Audit Trail.
 * Dynamically resolves viewing permissions based on the inherent privacy level of the specific action.
 */
class UserActivityPolicy
{
    public function view(?User $viewer, UserActivity $activity): bool
    {
        $visibility = ActivityVisibility::for($activity->action);

        // Public visibility allows anyone (even guests) to view
        if ($visibility === 'public') {
            return true;
        }

        // The owner of the activity can always view their own history
        if ($viewer && $viewer->id === $activity->user_id) {
            return true;
        }

        // Space reserved for future 'followers-only' logic
        if ($visibility === 'followers') {
            return false; 
        }

        // Default Deny: Failsafe for private actions (e.g., voting dynamics)
        return false;
    }
}
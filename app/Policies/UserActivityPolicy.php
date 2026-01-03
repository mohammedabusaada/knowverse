<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserActivity;
use App\Support\ActivityVisibility;

class UserActivityPolicy
{
    public function view(?User $viewer, UserActivity $activity): bool
    {
        $visibility = ActivityVisibility::for($activity->action);

        // Public = everyone
        if ($visibility === 'public') {
            return true;
        }

        // Owner can always see
        if ($viewer && $viewer->id === $activity->user_id) {
            return true;
        }

        // Future: followers
        if ($visibility === 'followers') {
            return false; // placeholder
        }

        return false;
    }
}

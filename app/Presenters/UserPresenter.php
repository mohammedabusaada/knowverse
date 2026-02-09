<?php

namespace App\Presenters;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserPresenter
{
    public function __construct(protected User $user) {}

    public function relationshipBadge(): ?string
    {
        if (!Auth::check() || Auth::id() === $this->user->id) return null;

        $authUser = Auth::user();
        $theyFollowMe = $this->user->following()->where('followed_id', $authUser->id)->exists();
        
        return $theyFollowMe ? 'Follows You' : null;
    }
}
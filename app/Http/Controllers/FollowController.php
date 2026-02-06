<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

use App\Http\Requests\FollowUserRequest;

class FollowController extends Controller
{
      public function follow(FollowUserRequest $request, User $user)
    {
        Auth::user()->following()->syncWithoutDetaching([$user->id]);
        return back()->with('success', 'The user has been successfully followed');
    }

    public function unfollow(User $user)
    {
        Auth::user()->following()->detach($user->id);
        return back()->with('success', 'The user has been unfollowed');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class FollowController extends Controller
{
    public function toggle(User $user)
    {
        if (Auth::id() === $user->id) {
            return back()->with('error', 'You cannot follow yourself.');
        }

        // Toggle automatically attaches if not present, and detaches if it is.
        Auth::user()->following()->toggle($user->id);

        return back();
    }
}
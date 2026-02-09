<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Rules\ReservedUsername;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProfileController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display the user's profile overview.
     */
    public function show(User $user)
    {
        // Enterprise Eager Loading: load counts in one query
        $user->loadCount(['posts', 'allComments', 'followers', 'following']);
        
        return view('profile.show', compact('user'));
    }

    /**
     * Show the profile edit form.
     */
    public function edit()
    {
        return view('profile.edit', [
            'user' => Auth::user()
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validated();

        // Handle File Upload via Storage Service logic
        if ($request->hasFile('profile_picture')) {
            if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            $validated['profile_picture'] = $request->file('profile_picture')->store('avatars', 'public');
        }

        $user->update($validated);

        return redirect()
            ->route('profile.show', $user->username)
            ->with('status', 'Profile updated successfully.');
    }

    /**
     * Display the followers list with privacy handling.
     */
    public function followers(User $user)
    {
        $user->loadCount(['posts', 'allComments', 'followers', 'following']);

        
        $canView = Auth::check() && (Auth::id() === $user->id || Auth::user()->can('viewLists', $user));

        $followers = $canView 
            ? $user->followers()->paginate(20) 
            : new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20);

        return view('profile.followers', [
            'user' => $user,
            'followers' => $followers,
            'isPrivate' => !$canView
        ]);
    }

    /**
     * Display the following list with privacy handling.
     */
    public function following(User $user)
{
    $user->loadCount(['posts', 'allComments', 'followers', 'following']);

    $canView = Auth::check() && (Auth::id() === $user->id || Auth::user()->can('viewLists', $user));

    // Fetch Followed People
    $followingUsers = $canView 
        ? $user->following()->paginate(20, ['*'], 'people_page') 
        : new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20);

    // Fetch Followed Tags
    $followingTags = $canView 
        ? $user->followedTags()->withCount('posts')->paginate(20, ['*'], 'tags_page') 
        : new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20);

    return view('profile.following', [
        'user' => $user,
        'following' => $followingUsers, // Keep for backward compatibility if needed
        'followingTags' => $followingTags,
        'isPrivate' => !$canView
    ]);
}
}
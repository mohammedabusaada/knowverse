<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Requests\ProfileUpdateRequest;
use League\CommonMark\CommonMarkConverter;

class ProfileController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display the scholar's profile overview and academic biography.
     * Type-hinting User ensures automatic 404 handling via Route Model Binding.
     */
    public function show(User $user)
    {
        // Eager load relationship counts to prevent N+1 queries in the UI
        $user->loadCount(['posts', 'allComments', 'followers', 'following']);
        
        $parsedBio = null;
        
        // Convert Markdown Biography to safe HTML for scholarly presentation
        if ($user->bio) {
            $converter = new CommonMarkConverter([
                'html_input' => 'strip',
                'allow_unsafe_links' => false,
            ]);
            $parsedBio = $converter->convert($user->bio)->getContent();
        }

        return view('profile.show', compact('user', 'parsedBio'));
    }

    /**
     * Display the list of users following this scholar, respecting privacy settings.
     */
    public function followers(User $user)
    {
        $user->loadCount(['posts', 'allComments', 'followers', 'following']);

        // Honor Moderator/Admin privileges to bypass privacy restrictions 
        // during moderation or investigations.
        $canView = $user->public_follow_lists || 
                  (Auth::check() && (Auth::id() === $user->id || Auth::user()->canModerate()));
        $followers = $canView
            ? $user->followers()->paginate(20)
            : new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20);

        return view('profile.followers', [
            'user'      => $user,
            'followers' => $followers,
            'isPrivate' => !$canView,
        ]);
    }

    /**
     * Display the list of scholars and topics this user follows.
     */
    public function following(User $user)
    {
        $user->loadCount(['posts', 'allComments', 'followers', 'following']);

        // Honor Moderator/Admin privileges
        $canView = $user->public_follow_lists || 
                  (Auth::check() && (Auth::id() === $user->id || Auth::user()->canModerate()));

        // Separate pagination instances for Scholars and Topics to avoid query conflicts
        $followingUsers = $canView
            ? $user->following()->paginate(20, ['*'], 'people_page')
            : new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20);

        $followingTags = $canView
            ? $user->followedTags()->withCount('posts')->paginate(20, ['*'], 'tags_page')
            : new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20);

        return view('profile.following', [
            'user'           => $user,
            'following'      => $followingUsers,
            'followingTags'  => $followingTags,
            'isPrivate'      => !$canView,
        ]);
    }

    /**
     * Show the profile configuration form for the authenticated user.
     */
    public function edit()
    {
        return view('profile.edit', [
            'user' => Auth::user()
        ]);
    }

    /**
     * Process profile updates including avatar management and asset lifecycle.
     */
    public function update(ProfileUpdateRequest $request)
    {
        $user = $request->user();
        $data = $request->validated();

        // Security: Explicitly forbid email modification to maintain identity persistence
        unset($data['email']);

        // Handle profile picture upload and infrastructure cleanup
        if ($request->hasFile('profile_picture')) {
            if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            $data['profile_picture'] = $request->file('profile_picture')->store('avatars', 'public');
        }

        $user->update($data);

        return redirect()
            ->route('profile.show', $user->username)
            ->with('status', 'Identity profile successfully updated.');
    }

    /**
     * Suspend the account temporarily (Deactivation).
     * The account remains in the database but is hidden from public views.
     */
    public function deactivate(Request $request)
    {
        $request->validate(['password' => ['required', 'current_password']]);

        $user = $request->user();

        // Ensuring policies (Defense in Depth
        $this->authorize('delete', $user);

        Auth::logout();        
        $user->delete(); // Soft delete serves as the 'deactivated' state

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('status', 'Your account has been deactivated. You can return at any time.');
    }

    /**
     * Terminate the user account permanently (GDPR Compliance).
     * Overrides soft deletes to purge all associated data.
     */
    public function destroy(Request $request)
    {
        $request->validate(['password' => ['required', 'current_password']]);

        $user = $request->user();
        // Ensuring policies (Defense in Depth)
        $this->authorize('delete', $user);

        Auth::logout();
        
        // Permanent eradication from the database
        $user->forceDelete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('status', 'Your account has been permanently deleted.');
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Public profile page: /@username
     */
    public function show(string $username)
    {
        $user = User::where('username', $username)->firstOrFail();

        return view('profiles.show', compact('user'));
    }

    /**
     * Edit the authenticated user profile.
     */
    public function edit()
    {
        $user = Auth::user();

        return view('profiles.edit', compact('user'));
    }

    /**
     * Update the authenticated user's profile.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'full_name'       => ['required', 'string', 'max:255'],
            'academic_title'  => ['nullable', 'string', 'max:255'],
            'bio'             => ['nullable', 'string', 'max:2000'],
            'profile_picture' => ['nullable', 'image', 'max:2048'],
        ]);

        // Handle profile picture upload correctly
        if ($request->hasFile('profile_picture')) {
            // delete previous one if exists
            if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            // store new file
            $validated['profile_picture'] = $request->file('profile_picture')
                ->store('avatars', 'public');
        }

        $user->update($validated);

        return redirect()
            ->route('profiles.show', $user->username) // FIXED
            ->with('status', 'Profile updated successfully.');
    }
}

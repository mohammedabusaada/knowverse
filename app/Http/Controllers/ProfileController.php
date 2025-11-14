<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Show the authenticated user's profile.
     */
    public function show()
    {
        $user = Auth::user();
        return view('profiles.show', compact('user'));
    }

    /**
     * Show edit profile page.
     */
    public function edit()
    {
        $user = Auth::user();
        return view('profiles.edit', compact('user'));
    }

    /**
     * Update the profile.
     */
    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Use fields that exist in your User model
        $validated = $request->validate([
            'full_name'       => ['required', 'string', 'max:255'],
            'academic_title'  => ['nullable', 'string', 'max:255'],
            'bio'             => ['nullable', 'string', 'max:2000'],
            'profile_picture' => ['nullable', 'image', 'max:2048'],
        ]);

        // Handle avatar upload
        if ($request->hasFile('profile_picture')) {

            if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            $validated['profile_picture'] = $request->file('profile_picture')
                ->store('avatars', 'public');
        }

        $user->update($validated);

        return redirect()
            ->route('profile.show')
            ->with('status', 'Profile updated successfully.');
    }
}

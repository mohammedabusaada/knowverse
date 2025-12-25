<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Rules\ReservedUsername;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Display the public profile page: /{username}
     */
    public function show(User $user)
    {
        $user->loadCount(['posts', 'allComments', 'followers']);
        return view('profiles.show', compact('user'));
    }

    /**
     * Show the form for editing the profile.
     */
    public function edit()
    {
        return view('profiles.edit', [
            'user' => Auth::user()
        ]);
    }

    /**
     * Update the profile.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'full_name'       => ['required', 'string', 'max:255'],
            'academic_title'  => ['nullable', 'string', 'max:255'],
            'bio'             => ['nullable', 'string', 'max:2000'],
            'profile_picture' => ['nullable', 'image', 'max:2048'],
            'username'        => [
                'sometimes', 
                'string', 
                'max:50', 
                'alpha_dash:ascii', 
                Rule::unique('users')->ignore($user->id),
                new ReservedUsername(),
            ],
        ]);

        if ($request->hasFile('profile_picture')) {
            if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            $validated['profile_picture'] = $request->file('profile_picture')->store('avatars', 'public');
        }

        $user->update($validated);

        return redirect()
            ->route('profiles.show', $user->username)
            ->with('status', 'Profile updated successfully.');
    }
}
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\ReservedUsername;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Sanitize input
        $request->merge([
            'username' => trim($request->username),
            'email' => trim($request->email),
        ]);

        // Validate the request, enforcing strict email DNS validation and global password rules
        $request->validate([
            'username' => [
                'required', 'string', 'max:255', 'unique:users', 'alpha_dash:ascii',
                new ReservedUsername(),
            ],
            'full_name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email:rfc,dns', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'username' => $request->username,
            'full_name' => $request->full_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => 1,
        ]);

        event(new Registered($user));

        // Redirect to login page instead of auto-logging in to enforce email verification
        return redirect()->route('login')->with('status', 'Account created! We have sent a verification link to your email. Please verify before logging in.');
    }
}
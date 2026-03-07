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
 * Implements multi-layered validation and sanitization to ensure data integrity and platform security.
 */
    public function store(Request $request): RedirectResponse
    {
        // 1. Data Normalization: Sanitize inputs to prevent archival inconsistencies
        $request->merge([
            'username'  => strtolower(trim($request->username)),
            'email'     => strtolower(trim($request->email)),
            'full_name' => $request->full_name ? trim($request->full_name) : null,
        ]);

        // 2. Strict Validation Protocols
        $request->validate([
            'username' => [
                'required', 'string', 'min:4', 'max:30', 'unique:users', 'alpha_dash:ascii', 
                'regex:/^(?!.*(.)\1\1).+$/', // Anti-Spam: Blocks repetitive character sequences
                'regex:/[a-zA-Z]/',        // Prevents hijacking system-level slugs
                new ReservedUsername(), 
            ],
            'full_name' => [
                'required', 
                'string', 
                'min:3',
                'max:255'
            ],
            'email' => [
                'required', 
                'string', 
                'lowercase', 
                'email:rfc',
                'max:255', 
                'unique:users',
                'not_regex:/^([^@]+)\1+@/', // Prevents repetitive patterns before @ (e.g., aaaa@domain.com)
            ],
            'password' => [
                'required', 
                'confirmed', 
                Rules\Password::defaults()
            ],
        ], [
            // Custom messages for a professional user experience
            'username.regex' => 'Username format is non-compliant or contains too many repetitive characters.',
            'email.email'    => 'Please provide a valid, verifiable academic or personal email address.',
            'email.dns'      => 'The email domain provided could not be verified by our DNS protocols.',
        ]);

        // 3. Persist the scholar record with immutable role assignment
        $user = User::create([
            'username'  => $request->username,
            'full_name' => $request->full_name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'role_id'   => 1, 
        ]);

        // 4. Trigger the Registered event to initiate email verification protocols
        event(new Registered($user));

        return redirect()
            ->route('login')
            ->with('status', 'Account created! We have sent a verification link to your email. Please verify before logging in.');
    }
}
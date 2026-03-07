<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

/**
 * Manages the lifecycle of an authenticated session.
 * Features automated reactivation for returning scholars and strict ingress filtering for banned accounts.
 */
    public function store(LoginRequest $request): RedirectResponse
    {
        // 1. Ingress Check: Intercept deactivated accounts (soft-deleted) for restoration
        $user = User::withTrashed()->where('email', $request->email)->first();

        if ($user && $user->trashed() && Hash::check($request->password, $user->password)) {
            $user->restore(); // Restore data and visibility across the platform
            session()->flash('status', 'Welcome back! Your account has been successfully reactivated.');
        }

        // 2. Standard authentication handshake
        $request->authenticate();

        // 3. Security Firewall: Verify if the account is currently under administrative suspension
        if ($request->user()->is_banned) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'email' => 'Your account has been suspended due to community guidelines violations.',
            ]);
        }

        // 4. Role-based Ingress: Route administrators directly to the management dashboard
        $request->session()->regenerate();
        if ($request->user()->canModerate()) {
            return redirect()->intended(route('admin.dashboard', absolute: false));
        }

        return redirect()->intended(route('home', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     * Clears all session data and redirects to the logout confirmation page.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('logout.view');
    }
}
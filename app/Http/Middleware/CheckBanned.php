<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

/**
 * Global Security Interceptor for Suspended Accounts.
 * Monitors every incoming request to ensure that banned users are 
 * immediately disconnected and prevented from accessing protected resources.
 */
class CheckBanned
{
    /**
     * Handle an incoming request.
     * Evaluates the 'is_banned' state and executes an immediate session termination if true.
     * * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Check for an active authenticated session and a positive ban flag
        if (Auth::check() && Auth::user()->is_banned) {
            
            // 1. Terminate the current authentication session [cite: 5]
            Auth::logout();

            // 2. Invalidate the existing session and regenerate the CSRF token 
            // to mitigate session hijacking risks [cite: 6]
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // 3. Redirect to the ingress point (login) with a localized error message [cite: 7]
            return redirect()->route('login')->with('error', 'Your account has been suspended by the administration.');
        }

        return $next($request);
    }
}
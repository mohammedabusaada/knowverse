<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;


class IsAdmin
{
     public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Privilege Verification: Permit only System Administrators (2) and Moderators (3)
        if (!$user || !in_array($user->role_id, [2, 3])) {
            abort(403, 'Unauthorized. Admins and Moderators only.');
        }

        return $next($request);
    }
}

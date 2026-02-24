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

        // Allow admin (2), and moderator (3)
        if (!$user || !in_array($user->role_id, [2, 3])) {
            abort(403, 'Unauthorized. Admins and Moderators only.');
        }

        return $next($request);
    }
}

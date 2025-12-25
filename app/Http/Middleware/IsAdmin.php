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

        if  (!$user || $user->role_id !== 2) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}

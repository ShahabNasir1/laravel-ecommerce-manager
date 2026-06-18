<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class isAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // Native framework check. Uses cached user object in memory.
        if (Auth::check() && Auth::user()->user_type === 'admin') {
            return $next($request);
        }

        abort(403, 'Unauthorized action. Admin access required.');
    }
}
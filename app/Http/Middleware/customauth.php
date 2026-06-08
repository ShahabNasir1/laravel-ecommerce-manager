<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class customauth
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $pathurl = $request->path();

        // CHECK 1: If user is logged in and tries to access the 'login' page, redirect to home
        if ($pathurl == 'login' && Session::has('user')) {
            return redirect('/');
        }

        // CHECK 2: If a guest tries to access protected pages (anything other than login)
        if ($pathurl != 'login' && !Session::has('user')) {
            return redirect('/login');
        }

        return $next($request);
    }
}
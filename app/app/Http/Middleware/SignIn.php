<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SignIn
{
    /**
     * Used to display a "You must sign in to access this page" to
     * unauthenticated users who try to access protected pages.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (is_null(auth()->user())) {
            return redirect(route('sign-in'));
        }

        return $next($request);
    }
}

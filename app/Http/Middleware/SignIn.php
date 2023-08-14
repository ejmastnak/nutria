<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SignIn
{
    /**
     * Used to display a "You must sign in to access this page" to
     * unauthenticated users who try to access protected pages.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function handle(Request $request, Closure $next, ...$guards): Response
    {
        if (is_null(auth()->user())) {
            throw new AuthenticationException('Unauthenticated.', $guards, route('sign-in'));
        }
        return $next($request);
    }
}

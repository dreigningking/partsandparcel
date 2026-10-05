<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailIsVerified
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user &&
            $user instanceof MustVerifyEmail &&
            ! $user->hasVerifiedEmail()) {
            
            // Allow access to verification routes and logout
            if ($request->routeIs('verification.notice', 'logout') || $request->is('verify-email', 'logout')) {
                return $next($request);
            }

            return $request->expectsJson()
                ? abort(403, 'Your email address is not verified.')
                : redirect()->route('verification.notice');
        }

        return $next($request);
    }
}

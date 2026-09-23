<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            return redirect()->guest(route('login'));
        }

        if (! $request->user()->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access. Administrator privileges required.');
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$permissions
     */
    public function handle(Request $request, Closure $next, ...$permissions): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->guest(route('login'));
        }

        if (! $user->isAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access. Administrator privileges required.');
        }

        // Super Administrator has unrestricted root access across all administrative areas
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // Check if user has any of the requested permissions
        foreach ($permissions as $permission) {
            // Support comma-separated permissions in route definition
            $parts = explode(',', $permission);
            foreach ($parts as $perm) {
                $trimmed = trim($perm);
                if ($trimmed !== '' && $user->hasPermission($trimmed)) {
                    return $next($request);
                }
            }
        }

        abort(403, 'Unauthorized. You do not have permission to access this administrative resource.');
    }
}

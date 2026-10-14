<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect()->guest(route('login'));
        }

        // If no roles specified, simply ensure authenticated
        if (empty($roles)) {
            return $next($request);
        }

        // Support comma-separated strings as well as varargs (e.g. 'admin,moderator')
        $allowedRoles = [];
        foreach ($roles as $role) {
            foreach (explode(',', $role) as $r) {
                $trimmed = trim($r);
                if ($trimmed !== '') {
                    $allowedRoles[] = $trimmed;
                }
            }
        }

        // Super Admin has all admin and moderator capabilities
        if ($user->role === 'super_admin') {
            return $next($request);
        }

        if (! in_array($user->role, $allowedRoles, true)) {
            abort(403, 'Access denied. You do not have the required permissions.');
        }

        return $next($request);
    }
}

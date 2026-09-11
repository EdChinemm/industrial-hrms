<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(
        Request $request,
        Closure $next,
        string ...$roles
    ): Response {

        $user = $request->user();

        if (!$user || !$user->role) {
            abort(403, 'You do not have permission to access this resource.');
        }

        if (!$user->role->is_active) {
            abort(403, 'Your assigned role is currently inactive.');
        }

        if (!in_array($user->role->name, $roles, true)) {
            abort(403, 'You do not have permission to access this resource.');
        }

        return $next($request);
    }
}

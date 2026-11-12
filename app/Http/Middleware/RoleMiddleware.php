<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(
        Request $request,
        Closure $next,
        string ...$roles
    ): Response {

        $user = $request->user();

        // User must be logged in.
        abort_unless($user, 401);

        // At least one requested role must match.
        $hasRole = collect($roles)->contains(
            fn ($role) => $user->hasRole($role)
        );

        abort_unless($hasRole, 403, 'You are not authorized to access this page.');

        return $next($request);
    }
}
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    public function handle(
        Request $request,
        Closure $next,
        string ...$permissions
    ): Response {

        $user = $request->user();

        // User must be logged in.
        abort_unless($user, 401);

        // At least one requested permission must match.
        $hasPermission = collect($permissions)->contains(
            fn ($permission) => $user->hasPermission($permission)
        );

        abort_unless(
            $hasPermission,
            403,
            'You do not have permission to perform this action.'
        );

        return $next($request);
    }
}
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApprovedVendorMiddleware
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {

        $user = $request->user();

        abort_unless($user && $user->hasRole('vendor'), 403);

        $vendor = $user->vendor;

        abort_unless($vendor, 403, 'Vendor profile not found.');

        if ($vendor->status !== 'approved') {

            $message = match ($vendor->status) {
                'suspended' => 'Your vendor account has been suspended. Please contact the administrator.',
                'rejected' => 'Your vendor application has been rejected. Please contact the administrator.',
                default => 'Your vendor application is not approved yet.',
            };

            auth()->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('vendor.login')
                ->with('error', $message);
        }

        return $next($request);
    }
}

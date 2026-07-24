<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Web-friendly role guard for the Blade admin panel.
 *
 * Unlike RequireRole (which returns JSON for API requests), this middleware
 * redirects unauthenticated users to the admin login page and returns a
 * 403 HTML view for forbidden role mismatches.
 */
class RequireRoleWeb
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->guest(route('admin.login'));
        }

        if (! in_array($user->role, $roles, true)) {
            abort(403, 'You do not have permission to access this admin area.');
        }

        if (! $user->is_active) {
            auth()->guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->guest(route('admin.login'))
                ->withErrors(['email' => 'यो खाता निष्क्रिय गरिएको छ।']);
        }

        return $next($request);
    }
}
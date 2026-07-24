<?php

namespace App\Http\Middleware;

use App\Support\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveSession
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->is_active) {
            return ApiResponse::error('Authentication required', 401);
        }

        $sessionVersion = $request->hasSession() ? $request->session()->get('session_version') : null;
        if ($sessionVersion !== null && (int) $sessionVersion !== (int) $user->session_version) {
            $request->session()->invalidate();

            return ApiResponse::error('Authentication required', 401);
        }

        return $next($request);
    }
}
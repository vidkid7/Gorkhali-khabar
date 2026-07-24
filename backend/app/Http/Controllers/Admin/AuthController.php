<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Admin Panel authentication (Blade / session-based).
 *
 * Mirrors the API AuthController login semantics but issues redirects
 * instead of JSON, sets the web-guard session, and enforces a staff-only
 * role gate at the controller level as a defence-in-depth measure.
 */
class AuthController extends Controller
{
    private const LOCK_MINUTES = 15;

    private const MAX_FAILURES = 5;

    public function showLogin(): View|RedirectResponse
    {
        if (auth()->guard('web')->check() && auth()->user()?->isStaff()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email', 'max:254'],
            'password' => ['required', 'string', 'max:128'],
        ]);

        $email = strtolower(trim($credentials['email']));
        $user = User::query()->where('email', $email)->first();

        if (! $user || ! $user->password_hash || ! $user->is_active) {
            // Constant-time dummy verify to avoid timing leaks
            password_verify($credentials['password'], '$2y$12$rN6gn4q1MqAelqfkLKKqnOK6sqY8Sr74jkLLzbVcdOU8xjs9d7Bvm');

            throw ValidationException::withMessages([
                'email' => 'अमान्य प्रमाणपत्रहरू।',
            ]);
        }

        if ($user->locked_until && $user->locked_until->isFuture()) {
            throw ValidationException::withMessages([
                'email' => 'धेरै असफल प्रयासहरू। कृपया पछि प्रयास गर्नुहोस्।',
            ]);
        }

        // Support both $2b$ (new) and $2y$ (legacy PHP) bcrypt prefixes
        $hash = str_starts_with($user->password_hash, '$2b$')
            ? '$2y$'.substr($user->password_hash, 4)
            : $user->password_hash;

        if (! password_verify($credentials['password'], $hash)) {
            $this->recordFailedLogin($user);

            throw ValidationException::withMessages([
                'email' => 'अमान्य प्रमाणपत्रहरू।',
            ]);
        }

        if (! $user->isStaff()) {
            throw ValidationException::withMessages([
                'email' => 'तपाईंसँग प्रशासक पहुँच छैन।',
            ]);
        }

        DB::transaction(function () use ($user): void {
            $user->forceFill([
                'failed_login_count' => 0,
                'locked_until' => null,
                'last_failed_login_at' => null,
            ])->save();
        });

        Auth::guard('web')->login($user, true);
        $request->session()->regenerate();
        $request->session()->put('session_version', $user->session_version);

        return redirect()->intended(route('admin.dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return redirect()->route('admin.login');
    }

    private function recordFailedLogin(User $user): void
    {
        $failed = ((int) $user->failed_login_count) + 1;
        $lockedUntil = $failed >= self::MAX_FAILURES
            ? now()->addMinutes(self::LOCK_MINUTES)
            : null;

        $user->forceFill([
            'failed_login_count' => $failed,
            'last_failed_login_at' => now(),
            'locked_until' => $lockedUntil,
        ])->save();
    }
}
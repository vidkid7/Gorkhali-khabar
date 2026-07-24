<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetMail;
use App\Mail\VerifyEmailMail;
use App\Models\EmailVerificationToken;
use App\Models\PasswordResetToken;
use App\Models\User;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    private const LOCK_MINUTES = 15;

    private const MAX_FAILURES = 5;

    private const DUMMY_PASSWORD_HASH = '$2y$12$rN6gn4q1MqAelqfkLKKqnOK6sqY8Sr74jkLLzbVcdOU8xjs9d7Bvm';

    public function register(Request $request): JsonResponse
    {
        $request->merge([
            'name' => trim((string) $request->input('name')),
            'email' => strtolower(trim((string) $request->input('email'))),
        ]);
        $data = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'email' => ['required', 'email', 'max:254'],
            'password' => [
                'required',
                'string',
                'min:8',
                'max:128',
                'regex:/[A-Z]/',
                'regex:/[a-z]/',
                'regex:/[0-9]/',
                'regex:/[^A-Za-z0-9]/',
            ],
        ]);

        if (User::query()->where('email', $data['email'])->exists()) {
            return ApiResponse::error('यो इमेल ठेगाना पहिले नै दर्ता भइसकेको छ', 409);
        }

        $user = User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password_hash' => password_hash($data['password'], PASSWORD_BCRYPT),
            'role' => 'READER',
        ]);

        return ApiResponse::success([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ], 201, 'दर्ता सफल भयो। कृपया आफ्नो इमेल प्रमाणित गर्नुहोस्।');
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $data = $request->validate([
            'token' => ['required', 'string', 'regex:/^[a-f0-9]{64}$/i'],
            'password' => [
                'required',
                'string',
                'min:8',
                'max:128',
                'regex:/[A-Z]/',
                'regex:/[a-z]/',
                'regex:/[0-9]/',
                'regex:/[^A-Za-z0-9]/',
            ],
        ]);

        $reset = DB::transaction(function () use ($data): bool {
            $token = PasswordResetToken::query()
                ->where('token_hash', hash('sha256', $data['token']))
                ->where('used', false)
                ->where('expires_at', '>', now())
                ->lockForUpdate()
                ->first();

            if (! $token) {
                return false;
            }

            User::query()->whereKey($token->user_id)->update([
                'password_hash' => password_hash($data['password'], PASSWORD_BCRYPT),
                'failed_login_count' => 0,
                'locked_until' => null,
                'last_failed_login_at' => null,
                'session_version' => DB::raw('session_version + 1'),
            ]);
            $token->update(['used' => true]);

            return true;
        });

        return $reset
            ? ApiResponse::success(message: 'पासवर्ड सफलतापूर्वक रिसेट भयो। कृपया लगइन गर्नुहोस्।')
            : ApiResponse::error('अमान्य वा म्याद सकिएको टोकन', 400);
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        $request->merge(['email' => strtolower(trim((string) $request->input('email')))]);
        $data = $request->validate(['email' => ['required', 'email', 'max:254']]);
        $user = User::query()->where('email', $data['email'])->first();

        if ($user) {
            $plainToken = bin2hex(random_bytes(32));
            DB::transaction(function () use ($user, $plainToken): void {
                PasswordResetToken::query()
                    ->where('user_id', $user->id)
                    ->where('used', false)
                    ->update(['used' => true]);
                PasswordResetToken::query()->create([
                    'user_id' => $user->id,
                    'token_hash' => hash('sha256', $plainToken),
                    'expires_at' => now()->addHour(),
                    'used' => false,
                ]);
            });

            $url = rtrim(config('app.url'), '/').'/auth/reset-password?token='.$plainToken;
            Mail::to($user->email)->queue(new PasswordResetMail($user->name ?: 'User', $url, $user->language ?: 'ne'));
        }

        return ApiResponse::success(message: 'यदि इमेल दर्ता छ भने, रिसेट लिंक पठाइनेछ');
    }

    public function sendVerification(Request $request): JsonResponse
    {
        $user = $request->user();
        if ($user->email_verified) {
            return ApiResponse::error('इमेल पहिले नै प्रमाणित भइसकेको छ', 400);
        }

        $plainToken = bin2hex(random_bytes(32));
        DB::transaction(function () use ($user, $plainToken): void {
            EmailVerificationToken::query()
                ->where('user_id', $user->id)
                ->where('used', false)
                ->update(['used' => true]);
            EmailVerificationToken::query()->create([
                'user_id' => $user->id,
                'token_hash' => hash('sha256', $plainToken),
                'expires_at' => now()->addDay(),
                'used' => false,
            ]);
        });

        $url = rtrim(config('app.url'), '/').'/api/v1/auth/verify-email?token='.$plainToken;
        Mail::to($user->email)->queue(new VerifyEmailMail($user->name ?: 'User', $url, $user->language ?: 'ne'));

        return ApiResponse::success(message: $user->language === 'en' ? 'Verification email sent' : 'प्रमाणीकरण इमेल पठाइयो');
    }

    public function verifyEmail(Request $request): JsonResponse
    {
        $data = $request->validate([
            'token' => ['required', 'string', 'regex:/^[a-f0-9]{64}$/i'],
        ]);

        $verified = DB::transaction(function () use ($data): bool {
            $token = EmailVerificationToken::query()
                ->where('token_hash', hash('sha256', $data['token']))
                ->where('used', false)
                ->where('expires_at', '>', now())
                ->lockForUpdate()
                ->first();

            if (! $token) {
                return false;
            }

            User::query()->whereKey($token->user_id)->update(['email_verified' => now()]);
            $token->update(['used' => true]);

            return true;
        });

        return $verified
            ? ApiResponse::success(message: 'इमेल प्रमाणित भयो।')
            : ApiResponse::error('अमान्य वा म्याद सकिएको टोकन', 400);
    }

    public function login(Request $request): JsonResponse
    {
        $request->merge(['email' => strtolower(trim((string) $request->input('email')))]);
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'max:128'],
        ]);

        $user = User::query()->where('email', $credentials['email'])->first();

        if (! $user || ! $user->password_hash || ! $user->is_active) {
            password_verify($credentials['password'], self::DUMMY_PASSWORD_HASH);

            return ApiResponse::error('Invalid credentials', 401);
        }

        if ($user->locked_until?->isFuture()) {
            return ApiResponse::error('Invalid credentials', 401);
        }

        $passwordHash = str_starts_with($user->password_hash, '$2b$')
            ? '$2y$'.substr($user->password_hash, 4)
            : $user->password_hash;
        if (! password_verify($credentials['password'], $passwordHash)) {
            $recentFailure = $user->last_failed_login_at?->greaterThan(now()->subMinutes(self::LOCK_MINUTES)) ?? false;
            $user->failed_login_count = ($recentFailure ? $user->failed_login_count : 0) + 1;
            $user->last_failed_login_at = now();
            $user->locked_until = $user->failed_login_count >= self::MAX_FAILURES
                ? now()->addMinutes(self::LOCK_MINUTES)
                : null;
            $user->save();

            return ApiResponse::error('Invalid credentials', 401);
        }

        if ($user->isStaff() && app()->isProduction() && $this->isSeedPassword($credentials['password'])) {
            return ApiResponse::error('Invalid credentials', 401);
        }

        $user->forceFill([
            'failed_login_count' => 0,
            'last_failed_login_at' => null,
            'locked_until' => null,
        ])->save();

        Auth::login($user);
        if ($request->hasSession()) {
            $request->session()->regenerate();
            $request->session()->put('session_version', $user->session_version);
        }

        return ApiResponse::success(['user' => $this->userPayload($user)]);
    }

    public function session(Request $request): JsonResponse
    {
        $user = $request->user();

        return $user
            ? ApiResponse::success(['user' => $this->userPayload($user)])
            : ApiResponse::error('Authentication required', 401);
    }

    public function logout(Request $request): JsonResponse
    {
        Auth::guard('web')->logout();
        Auth::forgetGuards();

        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return ApiResponse::success();
    }

    /** @return array<string, mixed> */
    private function userPayload(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'image' => $user->image,
            'role' => $user->role,
            'email_verified' => $user->email_verified,
            'session_version' => $user->session_version,
        ];
    }

    private function isSeedPassword(string $password): bool
    {
        foreach (['SEED_ADMIN_PASSWORD', 'SEED_EDITOR_PASSWORD', 'SEED_AUTHOR_PASSWORD'] as $name) {
            $seedPassword = getenv($name);
            if (is_string($seedPassword) && $seedPassword !== '' && hash_equals($seedPassword, $password)) {
                return true;
            }
        }

        return false;
    }
}
<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Account;
use App\Models\User;
use App\Support\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController
{
    public function redirect()
    {
        if (! $this->enabled()) {
            return ApiResponse::error('Google authentication is not configured', 404);
        }

        return Socialite::driver('google')->redirect();
    }

    public function callback(Request $request)
    {
        if (! $this->enabled()) {
            return ApiResponse::error('Google authentication is not configured', 404);
        }

        $providerUser = Socialite::driver('google')->user();
        $email = strtolower(trim((string) $providerUser->getEmail()));
        $verified = filter_var($providerUser->user['email_verified'] ?? false, FILTER_VALIDATE_BOOL);

        if (! $email || ! $verified) {
            return ApiResponse::error('Google email is not verified', 403);
        }

        $user = DB::transaction(function () use ($providerUser, $email): User {
            $account = Account::query()
                ->where('provider', 'google')
                ->where('provider_account_id', $providerUser->getId())
                ->first();
            $user = $account?->user ?: User::query()->where('email', $email)->first();

            if (! $user) {
                $user = User::query()->create([
                    'name' => $providerUser->getName(),
                    'email' => $email,
                    'image' => $providerUser->getAvatar(),
                    'role' => 'READER',
                    'email_verified' => now(),
                    'is_active' => true,
                ]);
            } elseif (! $user->is_active) {
                abort(401);
            } else {
                $user->forceFill(['email_verified' => $user->email_verified ?: now()])->save();
            }

            Account::query()->updateOrCreate(
                ['provider' => 'google', 'provider_account_id' => $providerUser->getId()],
                [
                    'user_id' => $user->id,
                    'type' => 'oauth',
                    'refresh_token' => $providerUser->refreshToken,
                    'access_token' => $providerUser->token,
                    'expires_at' => $providerUser->expiresIn,
                ],
            );

            return $user;
        });

        Auth::guard('web')->login($user);
        $request->session()->regenerate();
        $request->session()->put('session_version', $user->session_version);

        return redirect(rtrim(config('app.url'), '/').'/auth/login?google=true');
    }

    private function enabled(): bool
    {
        return filled(config('services.google.client_id')) && filled(config('services.google.client_secret'));
    }
}
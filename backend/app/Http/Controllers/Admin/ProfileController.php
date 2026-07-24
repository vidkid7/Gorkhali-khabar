<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Lets a logged-in admin user manage their own profile: name, email,
 * preferred language, UI theme and password. The user cannot promote
 * themselves or change their role from here (use the Users screen).
 */
class ProfileController extends Controller
{
    public function show(Request $request): View
    {
        return view('admin.profile.show', ['user' => $request->user()]);
    }

    public function update(Request $request, AuditService $audit): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:254', Rule::unique('users', 'email')->ignore($user->id)],
            'language' => ['nullable', Rule::in(['ne', 'en'])],
            'theme' => ['nullable', Rule::in(['light', 'dark'])],
            'admin_theme' => ['nullable', Rule::in(['light', 'dark'])],
            'current_password' => ['nullable', 'required_with:password', 'string'],
            'password' => ['nullable', 'string', 'min:8', 'max:128', 'confirmed'],
        ]);

        $old = $user->only(['name', 'email', 'language', 'theme', 'admin_theme']);

        // Email change forces a session version bump (invalidates other sessions)
        if ($data['email'] !== $user->email) {
            $user->email_verified = null;
            $user->session_version = ((int) $user->session_version) + 1;
        }

        $user->name = $data['name'];
        $user->email = strtolower(trim($data['email']));
        $user->language = $data['language'] ?? $user->language;
        $user->theme = $data['theme'] ?? $user->theme;
        $user->admin_theme = $data['admin_theme'] ?? $user->admin_theme;

        if (! empty($data['password'])) {
            // Verify current password before allowing change
            if (! $data['current_password'] || ! password_verify($data['current_password'], $user->password_hash)) {
                return back()->withErrors(['current_password' => 'हालको पासवर्ड मिलेन।'])->withInput();
            }
            $user->password_hash = Hash::make($data['password']);
            $user->password = $user->password_hash;
        }

        $user->save();

        $audit->record($user, 'UPDATE', 'User', $user->id, oldValue: $old, newValue: $user->only(['name', 'email', 'language', 'theme']));

        return back()->with('status', 'प्रोफाइल अपडेट गरियो।');
    }
}
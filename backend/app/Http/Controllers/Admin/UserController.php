<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::query();
        if ($search = trim((string) $request->query('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }
        if ($role = $request->query('role')) {
            $query->where('role', $role);
        }

        $users = $query->orderByDesc('created_at')->paginate(30)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function create(): View
    {
        return view('admin.users.form', ['user' => new User()]);
    }

    public function store(Request $request, AuditService $audit): RedirectResponse
    {
        $data = $this->validateData($request);

        $user = DB::transaction(function () use ($data) {
            return User::query()->create([
                'name' => $data['name'],
                'email' => strtolower(trim($data['email'])),
                'password_hash' => password_hash($data['password'], PASSWORD_BCRYPT),
                'role' => $data['role'],
                'is_active' => $data['is_active'] ?? true,
                'language' => $data['language'] ?? 'ne',
                'theme' => 'light',
                'admin_theme' => 'light',
            ]);
        });

        $audit->record($request->user(), 'CREATE', 'User', $user->id, newValue: ['email' => $user->email, 'role' => $user->role]);

        return redirect()->route('admin.users.index')->with('status', 'प्रयोगकर्ता सिर्जना गरियो।');
    }

    public function edit(User $user): View
    {
        return view('admin.users.form', compact('user'));
    }

    public function update(Request $request, AuditService $audit, User $user): RedirectResponse
    {
        $data = $this->validateData($request, $user->id, true);
        $old = $user->only(['name', 'email', 'role', 'is_active', 'language']);

        DB::transaction(function () use ($user, $data) {
            $update = [
                'name' => $data['name'],
                'email' => strtolower(trim($data['email'])),
                'role' => $data['role'],
                'is_active' => $data['is_active'] ?? $user->is_active,
                'language' => $data['language'] ?? $user->language,
            ];

            // Block last admin from demotion
            if ($old['role'] === 'ADMIN' && $data['role'] !== 'ADMIN') {
                $activeAdmins = User::query()->where('role', 'ADMIN')->where('is_active', true)->count();
                if ($activeAdmins <= 1) {
                    abort(400, 'अन्तिम सक्रिय एडमिनको भूमिका परिवर्तन गर्न सकिँदैन।');
                }
                $update['session_version'] = ((int) $user->session_version) + 1;
            }

            if (! empty($data['password'])) {
                $update['password_hash'] = password_hash($data['password'], PASSWORD_BCRYPT);
            }

            $user->update($update);
        });

        $audit->record($request->user(), 'UPDATE', 'User', $user->id, oldValue: $old, newValue: $user->only(['name', 'email', 'role', 'is_active']));

        return redirect()->route('admin.users.index')->with('status', 'प्रयोगकर्ता अपडेट गरियो।');
    }

    public function destroy(Request $request, AuditService $audit, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'आफ्नो खाता मेटाउन सकिँदैन।');
        }
        if ($user->role === 'ADMIN') {
            $activeAdmins = User::query()->where('role', 'ADMIN')->where('is_active', true)->count();
            if ($activeAdmins <= 1) {
                return back()->with('error', 'अन्तिम एडमिन मेटाउन सकिँदैन।');
            }
        }
        $old = $user->only(['email', 'role']);
        $user->delete();
        $audit->record($request->user(), 'DELETE', 'User', $user->id, oldValue: $old);

        return redirect()->route('admin.users.index')->with('status', 'प्रयोगकर्ता मेटाइयो।');
    }

    private function validateData(Request $request, ?string $id = null, bool $isUpdate = false): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:254', Rule::unique('users', 'email')->ignore($id)],
            'role' => ['required', Rule::in(['READER', 'AUTHOR', 'EDITOR', 'ADMIN'])],
            'is_active' => ['nullable'],
            'language' => ['nullable', Rule::in(['ne', 'en'])],
            'password' => [$isUpdate ? 'nullable' : 'required', 'string', 'min:8', 'max:128'],
        ]) + ['is_active' => $request->boolean('is_active')];
    }
}
<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\BreakingNews;
use App\Models\QuickLink;
use App\Models\SiteSetting;
use App\Models\Tag;
use App\Models\User;
use App\Services\AuditService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminPrimitiveController extends Controller
{
    public function userRoleUpdate(Request $request, AuditService $audit, string $id): JsonResponse
    {
        if (! $request->isJson()) {
            return ApiResponse::error('Invalid content type', 415);
        }
        $role = $request->input('role');
        if (! is_string($role) || ! in_array($role, ['READER', 'AUTHOR', 'EDITOR', 'ADMIN'], true)) {
            return ApiResponse::error('Invalid role', 400);
        }
        if ($id === $request->user()->id) {
            return ApiResponse::error('Cannot change your own role', 400);
        }

        $result = DB::transaction(static function () use ($id, $role): array|JsonResponse {
            $user = User::query()->lockForUpdate()->find($id);
            if (! $user) {
                return ApiResponse::error('User not found', 404);
            }
            $oldRole = $user->role;
            if ($oldRole === 'ADMIN' && $role !== 'ADMIN') {
                $activeAdmins = User::query()
                    ->where('role', 'ADMIN')
                    ->where('is_active', true)
                    ->lockForUpdate()
                    ->count();
                if ($activeAdmins <= 1) {
                    return ApiResponse::error('Cannot remove the last active admin', 400);
                }
            }
            $user->update([
                'role' => $role,
                'session_version' => ((int) $user->session_version) + 1,
            ]);

            return [$user->fresh(), $oldRole];
        });
        if ($result instanceof JsonResponse) {
            return $result;
        }
        [$user, $oldRole] = $result;
        $audit->record($request->user(), 'UPDATE', 'User', $id, ['role' => $oldRole], ['role' => $role]);

        return ApiResponse::success($user->only(['id', 'name', 'email', 'role']));
    }

    public function settingsIndex(): JsonResponse
    {
        return ApiResponse::success(SiteSetting::query()->pluck('value', 'key')->all());
    }

    public function settingsUpdate(Request $request, AuditService $audit): JsonResponse
    {
        if (! $request->isJson()) {
            return ApiResponse::error('Invalid content type', 415);
        }
        $settings = $request->json()->all();
        if ($settings === [] || array_is_list($settings)) {
            return ApiResponse::error('अपडेट गर्न कुनै सेटिङ प्रदान गरिएको छैन', 400);
        }
        $oldValues = SiteSetting::query()->whereIn('key', array_keys($settings))->pluck('value', 'key')->all();
        DB::transaction(static function () use ($settings): void {
            foreach ($settings as $key => $value) {
                SiteSetting::query()->updateOrCreate(['key' => $key], ['value' => $value]);
            }
        });
        $audit->record($request->user(), 'SETTINGS_CHANGE', 'SiteSettings', oldValue: $oldValues, newValue: $settings);

        return $this->settingsIndex();
    }

    public function breakingNewsStore(Request $request, AuditService $audit): JsonResponse
    {
        if (! $request->isJson()) {
            return ApiResponse::error('Invalid content type', 415);
        }
        $data = $this->breakingNewsData($request);
        if (! $this->isPublishedArticle($data['article_id'] ?? null)) {
            return ApiResponse::error('प्रकाशित लेख मात्र लिंक गर्न सकिन्छ', 400);
        }
        $data['is_active'] ??= true;
        $item = BreakingNews::query()->create($data);
        $audit->record($request->user(), 'CREATE', 'BreakingNews', $item->id, newValue: $item->toArray());

        return ApiResponse::success($item, 201);
    }

    public function breakingNewsUpdate(Request $request, AuditService $audit, string $id): JsonResponse
    {
        if (! $request->isJson()) {
            return ApiResponse::error('Invalid content type', 415);
        }
        $item = BreakingNews::query()->find($id);
        if (! $item) {
            return ApiResponse::error('ब्रेकिङ समाचार फेला परेन', 404);
        }
        $data = $this->breakingNewsData($request, true);
        if (array_key_exists('article_id', $data) && ! $this->isPublishedArticle($data['article_id'])) {
            return ApiResponse::error('प्रकाशित लेख मात्र लिंक गर्न सकिन्छ', 400);
        }
        $old = $item->toArray();
        $item->update($data);
        $audit->record($request->user(), 'UPDATE', 'BreakingNews', $id, $old, $item->fresh()->toArray());

        return ApiResponse::success($item->fresh());
    }

    public function breakingNewsDestroy(Request $request, AuditService $audit, string $id): JsonResponse
    {
        $item = BreakingNews::query()->find($id);
        if (! $item) {
            return ApiResponse::error('ब्रेकिङ समाचार फेला परेन', 404);
        }
        $old = $item->toArray();
        $item->delete();
        $audit->record($request->user(), 'DELETE', 'BreakingNews', $id, oldValue: $old);

        return ApiResponse::success($old);
    }

    public function tagsStore(Request $request): JsonResponse
    {
        if (! $request->isJson()) {
            return ApiResponse::error('Invalid content type', 415);
        }
        $name = trim((string) $request->input('name'));
        $slug = Str::slug($name);
        if ($name === '') {
            return ApiResponse::error('ट्याग नाम आवश्यक छ', 400);
        }
        if ($slug === '') {
            return ApiResponse::error('अमान्य ट्याग नाम', 400);
        }
        if (Tag::query()->where('slug', $slug)->exists()) {
            return ApiResponse::error('यो ट्याग पहिले नै अवस्थित छ', 409);
        }

        return ApiResponse::success(Tag::query()->create([
            'name' => $name,
            'name_en' => trim((string) $request->input('name_en')) ?: null,
            'slug' => $slug,
        ]), 201);
    }

    public function tagsUpdate(Request $request): JsonResponse
    {
        if (! $request->isJson()) {
            return ApiResponse::error('Invalid content type', 415);
        }
        $request->validate(['id' => ['required', 'string']]);
        $tag = Tag::query()->find($request->string('id')->toString());
        if (! $tag) {
            return ApiResponse::error('ट्याग फेला परेन', 404);
        }
        $name = trim((string) $request->input('name'));
        $slug = Str::slug($name);
        if ($name === '' || $slug === '') {
            return ApiResponse::error('ट्याग नाम आवश्यक छ', 400);
        }
        if (Tag::query()->where('slug', $slug)->whereKeyNot($tag->id)->exists()) {
            return ApiResponse::error('यो ट्याग पहिले नै अवस्थित छ', 409);
        }
        $tag->update(['name' => $name, 'name_en' => trim((string) $request->input('name_en')) ?: null, 'slug' => $slug]);

        return ApiResponse::success($tag->fresh());
    }

    public function tagsDestroy(Request $request): JsonResponse
    {
        $request->validate(['id' => ['required', 'string']]);
        $tag = Tag::query()->find($request->string('id')->toString());
        if (! $tag) {
            return ApiResponse::error('ट्याग फेला परेन', 404);
        }
        $tag->delete();

        return ApiResponse::success(null);
    }

    public function linksIndex(): JsonResponse
    {
        return ApiResponse::success(QuickLink::query()->orderBy('sort_order')->orderBy('created_at')->get());
    }

    public function linksStore(Request $request, AuditService $audit): JsonResponse
    {
        if (! $request->isJson()) {
            return ApiResponse::error('Invalid content type', 415);
        }
        $data = $this->linkData($request);
        $link = QuickLink::query()->updateOrCreate(['slug' => $data['slug']], $data);
        $audit->record($request->user(), 'CREATE', 'QuickLink', $link->id, newValue: $link->toArray());

        return ApiResponse::success($link);
    }

    public function linksUpdate(Request $request, AuditService $audit, string $id): JsonResponse
    {
        if (! $request->isJson()) {
            return ApiResponse::error('Invalid content type', 415);
        }
        $link = QuickLink::query()->find($id);
        if (! $link) {
            return ApiResponse::error('लिंक फेला परेन', 404);
        }
        $old = $link->toArray();
        $link->update($request->validate([
            'href' => ['sometimes', 'string', 'min:1', 'max:255'],
            'title_ne' => ['sometimes', 'string', 'min:1', 'max:120'],
            'title_en' => ['sometimes', 'string', 'min:1', 'max:120'],
            'description_ne' => ['sometimes', 'string', 'min:1', 'max:255'],
            'description_en' => ['sometimes', 'string', 'min:1', 'max:255'],
            'icon_key' => ['sometimes', 'string', 'min:1', 'max:64'],
            'accent_color' => ['sometimes', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ]));
        $audit->record($request->user(), 'UPDATE', 'QuickLink', $link->id, $old, $link->fresh()->toArray());

        return ApiResponse::success($link->fresh());
    }

    public function linksDestroy(Request $request, AuditService $audit, string $id): JsonResponse
    {
        $link = QuickLink::query()->find($id);
        if (! $link) {
            return ApiResponse::error('लिंक फेला परेन', 404);
        }
        $audit->record($request->user(), 'DELETE', 'QuickLink', $id, oldValue: $link->toArray());
        $link->delete();

        return ApiResponse::success();
    }

    /** @return array<string, mixed> */
    private function linkData(Request $request): array
    {
        return $request->validate([
            'slug' => ['required', 'regex:/^[a-z0-9-]+$/', 'max:64'],
            'href' => ['required', 'string', 'max:255'],
            'title_ne' => ['required', 'string', 'max:120'],
            'title_en' => ['required', 'string', 'max:120'],
            'description_ne' => ['required', 'string', 'max:255'],
            'description_en' => ['required', 'string', 'max:255'],
            'icon_key' => ['required', 'string', 'max:64'],
            'accent_color' => ['sometimes', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ]);
    }

    /** @return array<string, mixed> */
    private function breakingNewsData(Request $request, bool $partial = false): array
    {
        $presence = $partial ? 'sometimes' : 'required';

        return $request->validate([
            'title' => [$presence, 'string', 'min:1', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'article_id' => ['nullable', 'string'],
            'expires_at' => ['nullable', 'date'],
            'is_active' => [$partial ? 'sometimes' : 'sometimes', 'boolean'],
        ]);
    }

    private function isPublishedArticle(?string $articleId): bool
    {
        return $articleId === null || Article::query()->whereKey($articleId)->where('status', 'PUBLISHED')->exists();
    }
}

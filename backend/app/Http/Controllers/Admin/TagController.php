<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TagController extends Controller
{
    public function index(): View
    {
        $tags = Tag::query()->withCount('articleTags')->orderBy('name')->paginate(50);
        return view('admin.tags.index', compact('tags'));
    }

    public function create(): View
    {
        return view('admin.tags.form', ['tag' => new Tag()]);
    }

    public function store(Request $request, AuditService $audit): RedirectResponse
    {
        $data = $this->validateData($request);
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        $tag = Tag::query()->create($data);
        $audit->record($request->user(), 'CREATE', 'Tag', $tag->id, newValue: $tag->toArray());

        return redirect()->route('admin.tags.index')->with('status', 'ट्याग सिर्जना गरियो।');
    }

    public function edit(Tag $tag): View
    {
        return view('admin.tags.form', compact('tag'));
    }

    public function update(Request $request, AuditService $audit, Tag $tag): RedirectResponse
    {
        $data = $this->validateData($request, $tag->id);
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        $old = $tag->toArray();
        $tag->update($data);
        $audit->record($request->user(), 'UPDATE', 'Tag', $tag->id, oldValue: $old, newValue: $tag->fresh()->toArray());

        return redirect()->route('admin.tags.index')->with('status', 'ट्याग अपडेट गरियो।');
    }

    public function destroy(Request $request, AuditService $audit, Tag $tag): RedirectResponse
    {
        $old = $tag->toArray();
        $tag->articleTags()->delete();
        $tag->delete();
        $audit->record($request->user(), 'DELETE', 'Tag', $tag->id, oldValue: $old);

        return redirect()->route('admin.tags.index')->with('status', 'ट्याग मेटाइयो।');
    }

    private function validateData(Request $request, ?string $id = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:60'],
            'name_en' => ['nullable', 'string', 'max:60'],
            'slug' => ['nullable', 'string', 'max:60', 'unique:tags,slug'.($id ? ",{$id}" : '')],
        ]);
    }
}
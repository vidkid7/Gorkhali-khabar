<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class GalleryController extends Controller
{
    public function index(): View
    {
        $galleries = Gallery::query()->orderByDesc('created_at')->paginate(30);
        return view('admin.galleries.index', compact('galleries'));
    }

    public function create(): View
    {
        return view('admin.galleries.form', ['gallery' => new Gallery()]);
    }

    public function store(Request $request, AuditService $audit): RedirectResponse
    {
        $data = $this->validateData($request);
        $data['slug'] = $data['slug'] ?? Str::slug($data['title']);
        $gallery = Gallery::query()->create($data);
        $audit->record($request->user(), 'CREATE', 'Gallery', $gallery->id, newValue: $gallery->toArray());

        return redirect()->route('admin.galleries.index')->with('status', 'ग्यालेरी सिर्जना गरियो।');
    }

    public function edit(Gallery $gallery): View
    {
        return view('admin.galleries.form', compact('gallery'));
    }

    public function update(Request $request, AuditService $audit, Gallery $gallery): RedirectResponse
    {
        $data = $this->validateData($request, $gallery->id);
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }
        $old = $gallery->toArray();
        $gallery->update($data);
        $audit->record($request->user(), 'UPDATE', 'Gallery', $gallery->id, oldValue: $old, newValue: $gallery->fresh()->toArray());

        return redirect()->route('admin.galleries.index')->with('status', 'ग्यालेरी अपडेट गरियो।');
    }

    public function destroy(Request $request, AuditService $audit, Gallery $gallery): RedirectResponse
    {
        $old = $gallery->toArray();
        $gallery->delete();
        $audit->record($request->user(), 'DELETE', 'Gallery', $gallery->id, oldValue: $old);

        return redirect()->route('admin.galleries.index')->with('status', 'ग्यालेरी मेटाइयो।');
    }

    private function validateData(Request $request, ?string $id = null): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:galleries,slug'.($id ? ",{$id}" : '')],
            'description' => ['nullable', 'string', 'max:1000'],
            'cover_image' => ['nullable', 'string', 'max:500'],
            'is_published' => ['nullable'],
        ]) + ['is_published' => $request->boolean('is_published')];
    }
}
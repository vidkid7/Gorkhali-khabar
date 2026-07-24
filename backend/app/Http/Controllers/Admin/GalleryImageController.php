<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Models\GalleryImage;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GalleryImageController extends Controller
{
    public function index(Request $request): View
    {
        $query = GalleryImage::query()->with('gallery:id,title');
        if ($galleryId = $request->query('gallery')) {
            $query->where('gallery_id', $galleryId);
        }
        $images = $query->orderByDesc('created_at')->paginate(30)->withQueryString();
        $galleries = Gallery::query()->orderBy('title')->get(['id', 'title']);

        return view('admin.gallery-images.index', compact('images', 'galleries'));
    }

    public function create(): View
    {
        return view('admin.gallery-images.form', [
            'image' => new GalleryImage(),
            'galleries' => Gallery::query()->orderBy('title')->get(['id', 'title']),
        ]);
    }

    public function store(Request $request, AuditService $audit): RedirectResponse
    {
        $data = $this->validateData($request);
        $image = GalleryImage::query()->create($data);
        $audit->record($request->user(), 'CREATE', 'GalleryImage', $image->id, newValue: $image->toArray());

        return redirect()->route('admin.gallery-images.index')->with('status', 'तस्वीर थपियो।');
    }

    public function edit(GalleryImage $galleryImage): View
    {
        return view('admin.gallery-images.form', [
            'image' => $galleryImage,
            'galleries' => Gallery::query()->orderBy('title')->get(['id', 'title']),
        ]);
    }

    public function update(Request $request, AuditService $audit, GalleryImage $galleryImage): RedirectResponse
    {
        $data = $this->validateData($request);
        $old = $galleryImage->toArray();
        $galleryImage->update($data);
        $audit->record($request->user(), 'UPDATE', 'GalleryImage', $galleryImage->id, oldValue: $old, newValue: $galleryImage->fresh()->toArray());

        return redirect()->route('admin.gallery-images.index')->with('status', 'तस्वीर अपडेट गरियो।');
    }

    public function destroy(Request $request, AuditService $audit, GalleryImage $galleryImage): RedirectResponse
    {
        $old = $galleryImage->toArray();
        $galleryImage->delete();
        $audit->record($request->user(), 'DELETE', 'GalleryImage', $galleryImage->id, oldValue: $old);

        return redirect()->route('admin.gallery-images.index')->with('status', 'तस्वीर मेटाइयो।');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'gallery_id' => ['required', 'string', 'exists:galleries,id'],
            'image_url' => ['required', 'string', 'max:500'],
            'caption' => ['nullable', 'string', 'max:500'],
            'alt_text' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer'],
        ]);
    }
}
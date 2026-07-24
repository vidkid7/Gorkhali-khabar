<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MediaFile;
use App\Services\AuditService;
use App\Services\MediaStorageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MediaController extends Controller
{
    public function index(Request $request): View
    {
        $query = MediaFile::query()->with('uploader:id,name');
        if ($search = trim((string) $request->query('search'))) {
            $query->where('filename', 'like', "%{$search}%");
        }
        $media = $query->orderByDesc('created_at')->paginate(30)->withQueryString();

        return view('admin.media.index', compact('media'));
    }

    public function store(Request $request, AuditService $audit, MediaStorageService $storage): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'max:10240'], // 10 MB
        ]);

        $stored = $storage->store($request->file('file'), $request->user());
        $audit->record($request->user(), 'UPLOAD', 'MediaFile', $stored->id, newValue: ['filename' => $stored->filename]);

        return back()->with('status', 'फाइल अपलोड भयो।');
    }

    public function destroy(Request $request, AuditService $audit, MediaStorageService $storage, MediaFile $media): RedirectResponse
    {
        $old = $media->only('filename');
        $storage->delete($media);
        $audit->record($request->user(), 'DELETE', 'MediaFile', $media->id, oldValue: $old);

        return back()->with('status', 'मिडिया मेटाइयो।');
    }
}
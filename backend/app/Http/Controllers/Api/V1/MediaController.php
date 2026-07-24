<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\MediaFile;
use App\Services\AuditService;
use App\Services\MediaStorageService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MediaController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $page = max(1, $request->integer('page', 1));
        $pageSize = min(50, max(1, $request->integer('pageSize', 20)));
        $query = MediaFile::query()->with('uploader:id,name');
        if ($request->filled('search')) {
            $search = '%'.strtolower($request->string('search')).'%';
            $query->where(function ($query) use ($search): void {
                $query->whereRaw('LOWER(original_name) LIKE ?', [$search])->orWhereRaw('LOWER(alt_text) LIKE ?', [$search]);
            });
        }
        if ($request->filled('mime_type')) {
            $query->where('mime_type', 'like', $request->string('mime_type').'%');
        }
        $total = (clone $query)->count();

        return ApiResponse::success([
            'data' => $query->orderByDesc('created_at')->forPage($page, $pageSize)->get(),
            'total' => $total,
            'page' => $page,
            'pageSize' => $pageSize,
            'totalPages' => (int) ceil($total / $pageSize),
        ]);
    }

    public function store(Request $request, MediaStorageService $storage, AuditService $audit): JsonResponse
    {
        if ($request->isJson()) {
            return $this->storeRemote($request, $audit);
        }
        if (! $request->hasFile('file')) {
            return ApiResponse::error('Invalid content type', 415);
        }

        $request->validate(['file' => ['required', 'file', 'mimes:jpg,jpeg,png,gif,webp,avif,mp4,webm,pdf', 'max:102400'], 'alt_text' => ['nullable', 'string']]);
        $file = $request->file('file');
        $mime = (string) $file->getMimeType();
        $isVideo = str_starts_with($mime, 'video/');
        if (! $isVideo && $file->getSize() > 10 * 1024 * 1024) {
            return ApiResponse::error('फाइल १० MB भन्दा ठूलो हुन सक्दैन', 400);
        }
        if ($request->filled('alt_text')) {
            $media = $storage->store($file, $request->user());
            $media->update(['alt_text' => $request->string('alt_text')->toString()]);
        } else {
            $media = $storage->store($file, $request->user());
        }

        return ApiResponse::success($media->load('uploader:id,name'), 201);
    }

    private function storeRemote(Request $request, AuditService $audit): JsonResponse
    {
        $data = $request->validate([
            'url' => ['required', 'string'],
            'alt_text' => ['nullable', 'string'],
        ]);
        $url = trim($data['url']);
        $parts = parse_url($url);
        $host = strtolower($parts['host'] ?? '');
        $extension = strtolower(pathinfo($parts['path'] ?? '', PATHINFO_EXTENSION));
        if (! filter_var($url, FILTER_VALIDATE_URL)
            || ! in_array($parts['scheme'] ?? '', ['http', 'https'], true)
            || $this->isPrivateHostname($host)) {
            return ApiResponse::error('केवल http/https URL अनुमति छ', 400);
        }
        if (! in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif'], true)) {
            return ApiResponse::error('केवल सार्वजनिक छवि URL अनुमति छ', 400);
        }

        $originalName = rawurldecode(basename($parts['path']));
        $media = MediaFile::query()->create([
            'filename' => 'url-'.Str::ulid().'.'.$extension,
            'original_name' => $originalName,
            'mime_type' => 'image/jpeg',
            'size' => 0,
            'url' => $url,
            'alt_text' => isset($data['alt_text']) ? trim($data['alt_text']) ?: null : null,
            'uploaded_by' => $request->user()->getKey(),
        ]);
        $audit->record($request->user(), 'CREATE', 'MediaFile', $media->id, newValue: [
            'url' => $url,
            'original_name' => $originalName,
        ]);

        return ApiResponse::success($media, 201);
    }

    private function isPrivateHostname(string $host): bool
    {
        if ($host === 'localhost'
            || str_ends_with($host, '.localhost')
            || str_ends_with($host, '.local')
            || str_ends_with($host, '.internal')) {
            return true;
        }

        if (filter_var($host, FILTER_VALIDATE_IP)) {
            return ! filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
        }

        return false;
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $media = MediaFile::query()->find($id);
        if (! $media) {
            return ApiResponse::error('मिडिया फेला परेन', 404);
        }
        $media->update($request->validate(['alt_text' => ['nullable', 'string']]));

        return ApiResponse::success($media->fresh());
    }

    public function destroy(MediaStorageService $storage, string $id): JsonResponse
    {
        $media = MediaFile::query()->find($id);
        if (! $media) {
            return ApiResponse::error('मिडिया फेला परेन', 404);
        }
        $storage->delete($media);

        return ApiResponse::success(['id' => $id]);
    }
}

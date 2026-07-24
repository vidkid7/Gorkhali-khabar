<?php

namespace App\Services;

use App\Models\MediaFile;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaStorageService
{
    public function store(UploadedFile $file, User $uploader, string $directory = 'uploads'): MediaFile
    {
        $diskName = config('filesystems.default', 'public');
        $disk = Storage::disk($diskName);
        $filename = (string) Str::ulid().'.'.strtolower($file->getClientOriginalExtension());
        $path = $disk->putFileAs($directory, $file, $filename);
        $dimensions = @getimagesize($file->getRealPath()) ?: [null, null];

        return MediaFile::query()->create([
            'filename' => $filename,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType() ?: 'application/octet-stream',
            'size' => $file->getSize(),
            'url' => $disk->url($path),
            'width' => $dimensions[0],
            'height' => $dimensions[1],
            'uploaded_by' => $uploader->getKey(),
        ]);
    }

    public function delete(MediaFile $media): bool
    {
        $diskName = config('filesystems.default', 'public');
        $deleted = Storage::disk($diskName)->delete('uploads/'.$media->filename);
        $media->delete();

        return $deleted;
    }
}
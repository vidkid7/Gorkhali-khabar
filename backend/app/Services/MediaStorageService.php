<?php

namespace App\Services;

use App\Models\MediaFile;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class MediaStorageService
{
    public function __construct(private readonly CloudinaryMediaService $cloudinary) {}

    public function store(UploadedFile $file, User $uploader, string $directory = 'uploads'): MediaFile
    {
        $driver = (string) config('media.driver', 'local');
        $isPdf = $file->getMimeType() === 'application/pdf';

        if ($driver === 'cloudinary' && ! $isPdf) {
            if (! $this->cloudinaryConfigured()) {
                if (app()->environment('production')) {
                    throw new RuntimeException('Cloudinary storage is required in production.');
                }

                return $this->storeLocal($file, $uploader, $directory);
            }

            return $this->storeCloudinary($file, $uploader, $directory);
        }

        return $this->storeLocal($file, $uploader, $directory);
    }

    public function delete(MediaFile $media): bool
    {
        $variants = $media->variants ?? [];
        $provider = $variants['storage_provider'] ?? null;

        if ($provider === 'cloudinary') {
            $publicId = $variants['cloudinary_public_id'] ?? null;
            $resourceType = $variants['cloudinary_resource_type'] ?? 'image';
            if (! is_string($publicId) || $publicId === '') {
                throw new RuntimeException('Cloudinary media metadata is incomplete.');
            }
            if (! $this->cloudinary->delete($publicId, (string) $resourceType)) {
                return false;
            }

            return (bool) $media->delete();
        }

        if ($provider === 'local' || str_starts_with((string) $media->url, '/storage/')) {
            $diskName = (string) config('filesystems.default', 'public');
            $path = $variants['local_path'] ?? 'uploads/'.$media->filename;
            Storage::disk($diskName)->delete((string) $path);
        }

        return (bool) $media->delete();
    }

    private function storeLocal(UploadedFile $file, User $uploader, string $directory): MediaFile
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
            'variants' => [
                'storage_provider' => 'local',
                'local_disk' => $diskName,
                'local_path' => $path,
            ],
            'uploaded_by' => $uploader->getKey(),
        ]);
    }

    private function storeCloudinary(UploadedFile $file, User $uploader, string $directory): MediaFile
    {
        $publicId = trim($directory, '/').'/'.Str::ulid();
        $result = $this->cloudinary->upload($file, $publicId);

        try {
            return MediaFile::query()->create([
                'filename' => basename($result['public_id']),
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType() ?: 'application/octet-stream',
                'size' => $file->getSize(),
                'url' => $result['url'],
                'width' => $result['width'],
                'height' => $result['height'],
                'variants' => [
                    'storage_provider' => 'cloudinary',
                    'cloudinary_public_id' => $result['public_id'],
                    'cloudinary_resource_type' => $result['resource_type'],
                ],
                'uploaded_by' => $uploader->getKey(),
            ]);
        } catch (Throwable $exception) {
            try {
                $this->cloudinary->delete($result['public_id'], $result['resource_type']);
            } catch (Throwable $cleanupException) {
                report($cleanupException);
            }

            throw $exception;
        }
    }

    private function cloudinaryConfigured(): bool
    {
        foreach (['cloud_name', 'api_key', 'api_secret'] as $key) {
            if (! filled(config("media.cloudinary.{$key}"))) {
                return false;
            }
        }

        return true;
    }
}

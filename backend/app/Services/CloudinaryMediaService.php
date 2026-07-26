<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class CloudinaryMediaService
{
    /**
     * @return array{
     *     url: string,
     *     public_id: string,
     *     resource_type: string,
     *     width: ?int,
     *     height: ?int
     * }
     */
    public function upload(UploadedFile $file, string $publicId): array
    {
        $resourceType = str_starts_with((string) $file->getMimeType(), 'video/')
            ? 'video'
            : 'image';
        $parameters = [
            'folder' => (string) config('media.cloudinary.folder'),
            'public_id' => $publicId,
            'timestamp' => now()->timestamp,
        ];

        $contents = file_get_contents($file->getRealPath());
        if ($contents === false) {
            throw new RuntimeException('The uploaded media file could not be read.');
        }

        $response = $this->client()
            ->attach('file', $contents, $file->getClientOriginalName())
            ->post($this->endpoint($resourceType, 'upload'), [
                ...$parameters,
                'api_key' => config('media.cloudinary.api_key'),
                'signature' => $this->signature($parameters),
            ])
            ->throw()
            ->json();

        if (empty($response['secure_url']) || empty($response['public_id'])) {
            throw new RuntimeException('Cloudinary did not return an asset URL.');
        }

        return [
            'url' => (string) $response['secure_url'],
            'public_id' => (string) $response['public_id'],
            'resource_type' => (string) ($response['resource_type'] ?? $resourceType),
            'width' => isset($response['width']) ? (int) $response['width'] : null,
            'height' => isset($response['height']) ? (int) $response['height'] : null,
        ];
    }

    public function delete(string $publicId, string $resourceType): bool
    {
        $parameters = [
            'public_id' => $publicId,
            'timestamp' => now()->timestamp,
        ];
        $response = $this->client()
            ->post($this->endpoint($resourceType, 'destroy'), [
                ...$parameters,
                'api_key' => config('media.cloudinary.api_key'),
                'signature' => $this->signature($parameters),
            ])
            ->throw()
            ->json();

        return in_array($response['result'] ?? null, ['ok', 'not found'], true);
    }

    private function client(): PendingRequest
    {
        foreach (['cloud_name', 'api_key', 'api_secret'] as $key) {
            if (! filled(config("media.cloudinary.$key"))) {
                throw new RuntimeException("Cloudinary configuration is incomplete: {$key}");
            }
        }

        return Http::acceptJson()->timeout(45)->retry(2, 250);
    }

    private function endpoint(string $resourceType, string $action): string
    {
        return sprintf(
            'https://api.cloudinary.com/v1_1/%s/%s/%s',
            rawurlencode((string) config('media.cloudinary.cloud_name')),
            $resourceType,
            $action,
        );
    }

    private function signature(array $parameters): string
    {
        ksort($parameters);
        $payload = collect($parameters)
            ->map(fn (mixed $value, string $key): string => "{$key}={$value}")
            ->implode('&');

        return sha1($payload.config('media.cloudinary.api_secret'));
    }
}

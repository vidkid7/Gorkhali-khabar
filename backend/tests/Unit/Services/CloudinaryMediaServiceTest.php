<?php

namespace Tests\Unit\Services;

use App\Services\CloudinaryMediaService;
use Illuminate\Http\Client\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Tests\TestCase;

class CloudinaryMediaServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('media.cloudinary', [
            'cloud_name' => 'demo-cloud',
            'api_key' => 'demo-key',
            'api_secret' => 'demo-secret',
            'folder' => 'gorkhali-khabar',
        ]);
    }

    public function test_it_uploads_images_with_a_signed_cloudinary_request(): void
    {
        Http::fake([
            'api.cloudinary.com/*' => Http::response([
                'secure_url' => 'https://res.cloudinary.com/demo/image/upload/cover.webp',
                'public_id' => 'gorkhali-khabar/uploads/cover',
                'resource_type' => 'image',
                'width' => 1200,
                'height' => 675,
            ]),
        ]);

        $result = app(CloudinaryMediaService::class)->upload(
            UploadedFile::fake()->image('cover.jpg', 1200, 675),
            'uploads/cover',
        );

        $this->assertSame('https://res.cloudinary.com/demo/image/upload/cover.webp', $result['url']);
        $this->assertSame('image', $result['resource_type']);
        $this->assertSame(1200, $result['width']);
        $this->assertSame(675, $result['height']);

        Http::assertSent(fn (Request $request): bool =>
            str_ends_with($request->url(), '/demo-cloud/image/upload')
            && str_contains($request->body(), 'name="api_key"')
            && str_contains($request->body(), 'demo-key')
            && str_contains($request->body(), 'name="signature"')
        );
    }

    public function test_it_uploads_and_deletes_video_with_video_resource_type(): void
    {
        Http::fake([
            '*/video/upload' => Http::response([
                'secure_url' => 'https://res.cloudinary.com/demo/video/upload/clip.mp4',
                'public_id' => 'gorkhali-khabar/uploads/clip',
                'resource_type' => 'video',
            ]),
            '*/video/destroy' => Http::response(['result' => 'ok']),
        ]);

        $service = app(CloudinaryMediaService::class);
        $result = $service->upload(
            UploadedFile::fake()->create('clip.mp4', 500, 'video/mp4'),
            'uploads/clip',
        );

        $this->assertSame('video', $result['resource_type']);
        $this->assertTrue($service->delete($result['public_id'], 'video'));

        Http::assertSent(fn (Request $request): bool =>
            str_ends_with($request->url(), '/demo-cloud/video/destroy')
            && $request['public_id'] === 'gorkhali-khabar/uploads/clip'
        );
    }

    public function test_it_rejects_incomplete_configuration_before_uploading(): void
    {
        config()->set('media.cloudinary.api_secret', null);
        Http::fake();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Cloudinary configuration is incomplete');

        app(CloudinaryMediaService::class)->upload(
            UploadedFile::fake()->image('cover.jpg'),
            'uploads/cover',
        );

        Http::assertNothingSent();
    }
}

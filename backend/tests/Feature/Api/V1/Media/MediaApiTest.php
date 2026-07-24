<?php

namespace Tests\Feature\Api\V1\Media;

use App\Models\MediaFile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_can_list_and_upload_media_but_readers_cannot(): void
    {
        Storage::fake('public');
        $reader = $this->user('media-reader', 'READER');
        $editor = $this->user('media-editor', 'EDITOR');

        $this->actingAs($reader)->getJson('/api/v1/media')->assertStatus(403);
        $this->actingAs($editor)->post('/api/v1/media', [
            'file' => UploadedFile::fake()->image('cover.png', 20, 20),
            'alt_text' => 'Cover',
        ])->assertCreated()->assertJsonPath('data.original_name', 'cover.png');

        $this->actingAs($editor)->getJson('/api/v1/media')->assertOk()->assertJsonPath('data.total', 1)->assertJsonPath('data.data.0.uploader.id', $editor->id);
    }

    public function test_only_admin_can_delete_media_and_invalid_files_are_rejected(): void
    {
        Storage::fake('public');
        $editor = $this->user('media-delete-editor', 'EDITOR');
        $admin = $this->user('media-delete-admin', 'ADMIN');
        $media = MediaFile::query()->create([
            'id' => 'media-delete',
            'filename' => 'delete.png',
            'original_name' => 'delete.png',
            'mime_type' => 'image/png',
            'size' => 10,
            'url' => '/storage/uploads/delete.png',
            'uploaded_by' => $editor->id,
        ]);

        $this->actingAs($editor)->post('/api/v1/media', ['file' => UploadedFile::fake()->create('bad.exe', 10, 'application/octet-stream')])->assertStatus(400);
        $this->actingAs($editor)->deleteJson('/api/v1/media/'.$media->id)->assertStatus(403);
        $this->actingAs($admin)->deleteJson('/api/v1/media/'.$media->id)->assertOk()->assertJsonPath('data.id', $media->id);
        $this->assertDatabaseMissing('media_files', ['id' => $media->id]);
    }

    public function test_staff_can_register_only_public_remote_image_urls(): void
    {
        $editor = $this->user('media-url-editor', 'EDITOR');

        $this->actingAs($editor)->postJson('/api/v1/media', [
            'url' => 'https://cdn.example.com/photos/cover.webp',
            'alt_text' => 'Remote cover',
        ])->assertCreated()
            ->assertJsonPath('data.url', 'https://cdn.example.com/photos/cover.webp')
            ->assertJsonPath('data.original_name', 'cover.webp')
            ->assertJsonPath('data.alt_text', 'Remote cover');

        $this->actingAs($editor)->postJson('/api/v1/media', ['url' => 'http://127.0.0.1/private.jpg'])->assertStatus(400);
        $this->actingAs($editor)->postJson('/api/v1/media', ['url' => 'https://cdn.example.com/file.pdf'])->assertStatus(400);
    }

    public function test_media_upload_requires_supported_content_type(): void
    {
        $editor = $this->user('media-content-type-editor', 'EDITOR');

        $this->actingAs($editor)->call('POST', '/api/v1/media', [], [], [], ['CONTENT_TYPE' => 'text/plain'])
            ->assertStatus(415);
    }

    private function user(string $id, string $role): User
    {
        return User::query()->create(['id' => $id, 'name' => $id, 'email' => $id.'@example.com', 'role' => $role, 'is_active' => true]);
    }
}

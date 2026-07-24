<?php

namespace Tests\Feature\Api\V1\Media;

use App\Models\Gallery;
use App\Models\GalleryImage;
use App\Models\Reel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EditorialContentApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_gallery_and_reel_lists_hide_inactive_content(): void
    {
        $gallery = Gallery::query()->create(['id' => 'public-gallery', 'title' => 'Public', 'slug' => 'public-gallery', 'is_active' => true]);
        Gallery::query()->create(['id' => 'hidden-gallery', 'title' => 'Hidden', 'slug' => 'hidden-gallery', 'is_active' => false]);
        GalleryImage::query()->create(['id' => 'second-image', 'gallery_id' => $gallery->id, 'url' => '/second.jpg', 'sort_order' => 2]);
        GalleryImage::query()->create(['id' => 'first-image', 'gallery_id' => $gallery->id, 'url' => '/first.jpg', 'sort_order' => 1]);
        Reel::query()->create(['id' => 'public-reel', 'title' => 'Public Reel', 'slug' => 'public-reel', 'video_url' => '/public.mp4', 'is_active' => true]);
        Reel::query()->create(['id' => 'hidden-reel', 'title' => 'Hidden Reel', 'slug' => 'hidden-reel', 'video_url' => '/hidden.mp4', 'is_active' => false]);

        $this->getJson('/api/v1/galleries')->assertOk()->assertJsonCount(1, 'data.data')->assertJsonPath('data.data.0._count.images', 2);
        $this->getJson('/api/v1/galleries/'.$gallery->id)->assertOk()->assertJsonPath('data.images.0.id', 'first-image');
        $this->getJson('/api/v1/reels')->assertOk()->assertJsonCount(1, 'data.data')->assertJsonPath('data.data.0.slug', 'public-reel');
        $this->getJson('/api/v1/reels/'.$gallery->id)->assertStatus(404);
    }

    public function test_admin_can_create_and_update_gallery_and_reel(): void
    {
        $admin = User::query()->create(['id' => 'editorial-admin', 'name' => 'Admin', 'email' => 'editorial-admin@example.com', 'role' => 'ADMIN', 'is_active' => true]);
        $this->actingAs($admin)->postJson('/api/v1/galleries', [
            'title' => 'Gallery',
            'slug' => 'gallery',
            'images' => [['url' => '/one.jpg', 'sort_order' => 1], ['url' => '/two.jpg', 'sort_order' => 2]],
        ])->assertCreated()->assertJsonCount(2, 'data.images');
        $this->actingAs($admin)->postJson('/api/v1/galleries', ['title' => 'Duplicate', 'slug' => 'gallery'])->assertStatus(409);
        $this->actingAs($admin)->postJson('/api/v1/reels', ['title' => 'Reel', 'slug' => 'reel', 'video_url' => '/reel.mp4'])->assertCreated();
        $this->actingAs($admin)->postJson('/api/v1/reels', ['title' => 'Duplicate', 'slug' => 'reel', 'video_url' => '/reel.mp4'])->assertStatus(409);
    }

    public function test_only_admin_can_include_inactive_galleries_and_reels(): void
    {
        Gallery::query()->create(['id' => 'inactive-gallery', 'title' => 'Inactive', 'slug' => 'inactive-gallery', 'is_active' => false]);
        Reel::query()->create(['id' => 'inactive-reel', 'title' => 'Inactive', 'slug' => 'inactive-reel', 'video_url' => '/inactive.mp4', 'is_active' => false]);
        $admin = User::query()->create(['id' => 'inactive-admin', 'name' => 'Admin', 'email' => 'inactive-admin@example.com', 'role' => 'ADMIN', 'is_active' => true]);
        $editor = User::query()->create(['id' => 'inactive-editor', 'name' => 'Editor', 'email' => 'inactive-editor@example.com', 'role' => 'EDITOR', 'is_active' => true]);

        $this->getJson('/api/v1/galleries?includeInactive=true')->assertOk()->assertJsonCount(0, 'data.data');
        $this->actingAs($editor)->getJson('/api/v1/reels?includeInactive=true')->assertOk()->assertJsonCount(0, 'data.data');
        $this->actingAs($admin)->getJson('/api/v1/galleries?includeInactive=true')->assertOk()->assertJsonPath('data.data.0.id', 'inactive-gallery');
        $this->actingAs($admin)->getJson('/api/v1/reels?includeInactive=true')->assertOk()->assertJsonPath('data.data.0.id', 'inactive-reel');
    }

    public function test_editorial_writes_require_json_content_type(): void
    {
        $admin = User::query()->create(['id' => 'content-type-admin', 'name' => 'Admin', 'email' => 'content-type-admin@example.com', 'role' => 'ADMIN', 'is_active' => true]);

        $this->actingAs($admin)->call('POST', '/api/v1/galleries', [], [], [], ['CONTENT_TYPE' => 'text/plain'])->assertStatus(415);
        $this->actingAs($admin)->call('POST', '/api/v1/reels', [], [], [], ['CONTENT_TYPE' => 'text/plain'])->assertStatus(415);
    }
}

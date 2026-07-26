# Cloudinary Media, About Page, and Advertisement Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Make Laravel the Cloudinary-first source of truth for image and video media, redesign the About page, and correct responsive advertisement sizing.

**Architecture:** Laravel's existing `MediaStorageService` will select a Cloudinary or local provider from configuration while preserving the current `/api/v1/media` response contract. Cloudinary calls use Laravel's HTTP client and signed Cloudinary Upload API requests, avoiding another runtime package. The frontend changes remain focused: one responsive `AdSlot` renderer and one editorial About page built from existing site configuration and language classes.

**Tech Stack:** PHP 8.3, Laravel 13, Laravel HTTP client, MySQL JSON metadata, Next.js 16, React 19, TypeScript, Tailwind CSS, Vitest, PHPUnit, Docker Compose

## Global Constraints

- Laravel remains the source of truth for media management.
- Production configuration using `cloudinary` must fail clearly when credentials are missing.
- Local development falls back to the public disk when Cloudinary is not configured.
- Images remain limited to 10 MB and videos to 100 MB.
- PDFs use local storage.
- Existing media records and the `/api/v1/media` response shape remain compatible.
- Cloudinary secrets remain outside source control.
- Existing Gorkhali Khabar branding, routes, header, footer, and admin authorization remain intact.
- The About page must show only the active language and work from 320 px through large desktop widths.
- Empty or failed advertisement data must not render an oversized placeholder.

---

### Task 1: Cloudinary Storage Provider

**Files:**
- Create: `backend/config/media.php`
- Create: `backend/app/Services/CloudinaryMediaService.php`
- Create: `backend/tests/Unit/Services/CloudinaryMediaServiceTest.php`
- Modify: `backend/.env.example`

**Interfaces:**
- Consumes: `Illuminate\Http\UploadedFile`, Laravel `Http` facade, environment values
- Produces: `CloudinaryMediaService::upload(UploadedFile $file, string $publicId): array{url:string,public_id:string,resource_type:string,width:?int,height:?int}` and `CloudinaryMediaService::delete(string $publicId, string $resourceType): bool`

- [ ] **Step 1: Write failing Cloudinary request tests**

```php
<?php

namespace Tests\Unit\Services;

use App\Services\CloudinaryMediaService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
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

        $this->assertSame('image', $result['resource_type']);
        $this->assertSame(1200, $result['width']);
        Http::assertSent(fn ($request) =>
            str_contains($request->url(), '/image/upload')
            && $request->hasHeader('Content-Type')
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
    }
}
```

- [ ] **Step 2: Run the tests and confirm the class is missing**

Run:

```powershell
docker exec gorkhali_redesign-nextjs-backend-1 php artisan test tests/Unit/Services/CloudinaryMediaServiceTest.php
```

Expected: FAIL because `CloudinaryMediaService` does not exist.

- [ ] **Step 3: Add the media configuration contract**

```php
<?php

return [
    'driver' => env('MEDIA_STORAGE_DRIVER', app()->environment('production') ? 'cloudinary' : 'local'),
    'cloudinary' => [
        'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
        'api_key' => env('CLOUDINARY_API_KEY'),
        'api_secret' => env('CLOUDINARY_API_SECRET'),
        'folder' => trim(env('CLOUDINARY_FOLDER', 'gorkhali-khabar'), '/'),
    ],
];
```

Add blank examples:

```dotenv
MEDIA_STORAGE_DRIVER=local
CLOUDINARY_CLOUD_NAME=
CLOUDINARY_API_KEY=
CLOUDINARY_API_SECRET=
CLOUDINARY_FOLDER=gorkhali-khabar
```

- [ ] **Step 4: Implement signed upload and deletion**

```php
<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class CloudinaryMediaService
{
    public function upload(UploadedFile $file, string $publicId): array
    {
        $resourceType = str_starts_with((string) $file->getMimeType(), 'video/') ? 'video' : 'image';
        $timestamp = now()->timestamp;
        $folder = config('media.cloudinary.folder');
        $parameters = ['folder' => $folder, 'public_id' => $publicId, 'timestamp' => $timestamp];
        $response = $this->client()
            ->attach('file', fopen($file->getRealPath(), 'r'), $file->getClientOriginalName())
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
            'url' => $response['secure_url'],
            'public_id' => $response['public_id'],
            'resource_type' => $response['resource_type'] ?? $resourceType,
            'width' => $response['width'] ?? null,
            'height' => $response['height'] ?? null,
        ];
    }

    public function delete(string $publicId, string $resourceType): bool
    {
        $parameters = ['public_id' => $publicId, 'timestamp' => now()->timestamp];
        $response = $this->client()->post($this->endpoint($resourceType, 'destroy'), [
            ...$parameters,
            'api_key' => config('media.cloudinary.api_key'),
            'signature' => $this->signature($parameters),
        ])->throw()->json();

        return in_array($response['result'] ?? null, ['ok', 'not found'], true);
    }

    private function client(): PendingRequest
    {
        foreach (['cloud_name', 'api_key', 'api_secret'] as $key) {
            if (! config("media.cloudinary.$key")) {
                throw new RuntimeException("Cloudinary configuration is incomplete: $key");
            }
        }

        return Http::acceptJson()->timeout(45)->retry(2, 250);
    }

    private function endpoint(string $resourceType, string $action): string
    {
        return sprintf(
            'https://api.cloudinary.com/v1_1/%s/%s/%s',
            rawurlencode(config('media.cloudinary.cloud_name')),
            $resourceType,
            $action,
        );
    }

    private function signature(array $parameters): string
    {
        ksort($parameters);
        $payload = collect($parameters)->map(fn ($value, $key) => "$key=$value")->implode('&');

        return sha1($payload.config('media.cloudinary.api_secret'));
    }
}
```

- [ ] **Step 5: Run the provider tests**

Run:

```powershell
docker exec gorkhali_redesign-nextjs-backend-1 php artisan test tests/Unit/Services/CloudinaryMediaServiceTest.php
```

Expected: PASS.

- [ ] **Step 6: Commit the provider**

```powershell
git add backend/config/media.php backend/app/Services/CloudinaryMediaService.php backend/tests/Unit/Services/CloudinaryMediaServiceTest.php backend/.env.example
git commit -m "feat: add Cloudinary media provider"
```

### Task 2: Laravel Media API Integration

**Files:**
- Modify: `backend/app/Services/MediaStorageService.php`
- Modify: `backend/app/Http/Controllers/Api/V1/MediaController.php`
- Modify: `backend/tests/Feature/Api/V1/Media/MediaApiTest.php`

**Interfaces:**
- Consumes: `CloudinaryMediaService::upload()` and `CloudinaryMediaService::delete()`
- Produces: unchanged `/api/v1/media` JSON responses with provider metadata in `MediaFile::variants`

- [ ] **Step 1: Add failing Cloudinary persistence and deletion tests**

Add tests that configure `media.driver=cloudinary`, mock
`CloudinaryMediaService`, upload an image and video, and assert:

```php
$response->assertCreated()
    ->assertJsonPath('data.url', 'https://res.cloudinary.com/demo/image/upload/cover.webp')
    ->assertJsonPath('data.variants.storage_provider', 'cloudinary')
    ->assertJsonPath('data.variants.cloudinary_public_id', 'gorkhali-khabar/uploads/cover')
    ->assertJsonPath('data.variants.cloudinary_resource_type', 'image');
```

Add a deletion assertion:

```php
$cloudinary->shouldReceive('delete')
    ->once()
    ->with('gorkhali-khabar/uploads/cover', 'image')
    ->andReturnTrue();
```

Also add a local fallback test with `media.driver=local`, and a remote URL
deletion test that asserts no storage provider is called.

- [ ] **Step 2: Run the focused API tests**

Run:

```powershell
docker exec gorkhali_redesign-nextjs-backend-1 php artisan test tests/Feature/Api/V1/Media/MediaApiTest.php
```

Expected: FAIL because the service still uses only Laravel's default disk.

- [ ] **Step 3: Make `MediaStorageService` provider-aware**

Implement these behaviors:

```php
public function store(UploadedFile $file, User $uploader, string $directory = 'uploads'): MediaFile
{
    $driver = config('media.driver', 'local');
    $isPdf = $file->getMimeType() === 'application/pdf';
    $cloudinaryConfigured = collect(['cloud_name', 'api_key', 'api_secret'])
        ->every(fn ($key) => filled(config("media.cloudinary.$key")));

    if ($driver === 'cloudinary' && ! $isPdf && ! $cloudinaryConfigured) {
        if (app()->environment('production')) {
            throw new RuntimeException('Cloudinary storage is required in production.');
        }

        return $this->storeLocal($file, $uploader, $directory);
    }

    return $driver === 'cloudinary' && ! $isPdf
        ? $this->storeCloudinary($file, $uploader, $directory)
        : $this->storeLocal($file, $uploader, $directory);
}
```

`storeCloudinary()` must generate the public ID, upload first, persist the
returned URL and `variants`, and delete the uploaded asset if persistence
throws:

```php
try {
    return MediaFile::query()->create([
        'filename' => basename($result['public_id']),
        'original_name' => $file->getClientOriginalName(),
        'mime_type' => $file->getMimeType(),
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
} catch (\Throwable $exception) {
    $this->cloudinary->delete($result['public_id'], $result['resource_type']);
    throw $exception;
}
```

`delete()` must inspect `variants.storage_provider`; delete Cloudinary assets
before deleting the record, delete local assets from their recorded path, and
delete URL-only database records without touching a disk.

- [ ] **Step 4: Return safe upload errors from the API**

Wrap the storage call and return a safe 502 response:

```php
try {
    $media = $storage->store($file, $request->user());
} catch (\Throwable $exception) {
    report($exception);

    return ApiResponse::error('मिडिया अपलोड गर्न सकिएन। कृपया पुनः प्रयास गर्नुहोस्।', 502);
}
```

Do not include exception messages or Cloudinary credentials in the response.

- [ ] **Step 5: Run media and authorization regression tests**

Run:

```powershell
docker exec gorkhali_redesign-nextjs-backend-1 php artisan test tests/Feature/Api/V1/Media/MediaApiTest.php tests/Feature/Admin/AdminPanelRegressionTest.php
```

Expected: PASS.

- [ ] **Step 6: Commit the integration**

```powershell
git add backend/app/Services/MediaStorageService.php backend/app/Http/Controllers/Api/V1/MediaController.php backend/tests/Feature/Api/V1/Media/MediaApiTest.php
git commit -m "feat: route Laravel media through Cloudinary"
```

### Task 3: Responsive Advertisement Rendering

**Files:**
- Modify: `src/components/ads/AdSlot.tsx`
- Modify: `tests/ad-slot.test.tsx`

**Interfaces:**
- Consumes: existing ad API fields `position.width`, `position.height`, and `image_url`
- Produces: `ad-${position}` test IDs and position-specific responsive presentation classes

- [ ] **Step 1: Add failing placement-style tests**

Add assertions:

```tsx
expect(await screen.findByTestId("ad-HEADER")).toHaveAttribute("data-layout", "leaderboard");
expect(screen.getByRole("link")).toHaveClass("max-w-[728px]");
expect(screen.getByRole("img")).toHaveClass("object-contain");
```

Add a SIDEBAR campaign and assert `data-layout="sidebar"` with
`max-w-[300px]`. Preserve the existing empty-response collapse test.

- [ ] **Step 2: Run the focused frontend test**

Run:

```powershell
npm test -- --run tests/ad-slot.test.tsx
```

Expected: FAIL because the current link expands to the full slot width and the
image uses `object-cover`.

- [ ] **Step 3: Add a position presentation map**

```tsx
const PRESENTATION = {
  HEADER: { layout: "leaderboard", maxWidth: "max-w-[728px]" },
  FOOTER: { layout: "leaderboard", maxWidth: "max-w-[728px]" },
  BETWEEN_SECTIONS: { layout: "section-banner", maxWidth: "max-w-[970px]" },
  IN_ARTICLE: { layout: "article-banner", maxWidth: "max-w-[728px]" },
  SIDEBAR: { layout: "sidebar", maxWidth: "max-w-[300px]" },
} as const;
```

Render the slot with compact vertical padding, center the link, use
`w-full`, and change the image to `h-auto w-full object-contain`. Set the
aside's `data-layout` to the map value. Return `null` for missing image URLs
as well as API and image failures.

- [ ] **Step 4: Run the ad tests**

Run:

```powershell
npm test -- --run tests/ad-slot.test.tsx
```

Expected: PASS.

- [ ] **Step 5: Commit the ad fix**

```powershell
git add src/components/ads/AdSlot.tsx tests/ad-slot.test.tsx
git commit -m "fix: size advertisement placements responsively"
```

### Task 4: Editorial About Page

**Files:**
- Modify: `src/app/about/page.tsx`
- Modify: `src/app/globals.css`
- Create: `tests/about-page.test.ts`

**Interfaces:**
- Consumes: `getSiteConfig()`, `Header`, `Footer`, `PublicPageHeader`, existing `.language-ne` and `.language-en` visibility rules
- Produces: responsive editorial sections with stable selectors `about-values`, `about-standards`, and `about-contact`

- [ ] **Step 1: Add failing structural tests**

Read the page source and assert the required language and design contracts:

```ts
import fs from "node:fs";
import path from "node:path";
import { describe, expect, it } from "vitest";

const source = fs.readFileSync(path.join(process.cwd(), "src/app/about/page.tsx"), "utf8");

describe("About page", () => {
  it("provides isolated Nepali and English editorial experiences", () => {
    expect(source).toContain('className="language-ne');
    expect(source).toContain('className="language-en');
    expect(source).toContain('data-testid="about-values"');
    expect(source).toContain('data-testid="about-standards"');
    expect(source).toContain('data-testid="about-contact"');
  });

  it("uses configured contact details and keeps the shared shell", () => {
    expect(source).toContain("<Header />");
    expect(source).toContain("<Footer />");
    expect(source).toContain("config.contact_email");
    expect(source).toContain("config.contact_phone");
  });
});
```

- [ ] **Step 2: Run the About page test**

Run:

```powershell
npm test -- --run tests/about-page.test.ts
```

Expected: FAIL because the required editorial sections do not exist.

- [ ] **Step 3: Implement the approved editorial layout**

Retain the page metadata and server-side `getSiteConfig()` call. Replace the
plain document body with:

- A navy editorial hero with red rule, mission statement, and compact
  "Accuracy / Balance / Timeliness" trust markers.
- Three value cards using `ShieldCheck`, `Scale`, and `Clock3`.
- A newsroom standards section with four numbered commitments.
- A restrained team/newsroom statement.
- A contact card populated from configured address, phone, and email.
- Parallel `.language-ne` and `.language-en` wrappers so global language CSS
  displays only the active language.

Use semantic sections, visible focus styles for links, `minmax(0, 1fr)` grid
behavior, and no external images.

- [ ] **Step 4: Add focused About page styles**

Add `.about-editorial-*` classes to `globals.css` for the hero, cards,
standards grid, and contact panel. Use existing tokens:

```css
.about-editorial-hero {
  background:
    linear-gradient(120deg, rgb(8 32 63 / 0.98), rgb(10 55 95 / 0.92)),
    var(--surface);
  border-top: 4px solid var(--primary);
  color: white;
}

.about-editorial-card {
  border: 1px solid var(--border);
  background: var(--surface);
  box-shadow: 0 16px 40px rgb(15 23 42 / 0.06);
}

@media (max-width: 640px) {
  .about-editorial-hero,
  .about-editorial-card {
    border-radius: 1rem;
  }
}
```

- [ ] **Step 5: Run About and frontend regression tests**

Run:

```powershell
npm test -- --run tests/about-page.test.ts tests/public-page-primitives.test.tsx tests/brand-defaults.test.ts
npx tsc --noEmit
```

Expected: PASS.

- [ ] **Step 6: Commit the About page**

```powershell
git add src/app/about/page.tsx src/app/globals.css tests/about-page.test.ts
git commit -m "feat: redesign the About page"
```

### Task 5: Container Configuration and Live Verification

**Files:**
- Modify: `.env.example`
- Modify: `compose.yaml`
- Modify: `README.md`

**Interfaces:**
- Consumes: the configuration contract from Task 1
- Produces: consistent media environment variables in backend, worker, and scheduler containers

- [ ] **Step 1: Pass media configuration into backend services**

Add to `x-backend-environment`:

```yaml
MEDIA_STORAGE_DRIVER: ${MEDIA_STORAGE_DRIVER:-local}
CLOUDINARY_CLOUD_NAME: ${CLOUDINARY_CLOUD_NAME:-}
CLOUDINARY_API_KEY: ${CLOUDINARY_API_KEY:-}
CLOUDINARY_API_SECRET: ${CLOUDINARY_API_SECRET:-}
CLOUDINARY_FOLDER: ${CLOUDINARY_FOLDER:-gorkhali-khabar}
```

Do not add credentials to the frontend service or commit real values.

- [ ] **Step 2: Document local and production setup**

Document:

```dotenv
# Local development
MEDIA_STORAGE_DRIVER=local

# Production
MEDIA_STORAGE_DRIVER=cloudinary
CLOUDINARY_CLOUD_NAME=your-cloud-name
CLOUDINARY_API_KEY=your-api-key
CLOUDINARY_API_SECRET=your-api-secret
CLOUDINARY_FOLDER=gorkhali-khabar
```

State that existing local media is not automatically migrated.

- [ ] **Step 3: Run the full focused verification suite**

Run:

```powershell
npm test -- --run
npx tsc --noEmit
docker exec gorkhali_redesign-nextjs-backend-1 php artisan test tests/Unit/Services/CloudinaryMediaServiceTest.php tests/Feature/Api/V1/Media/MediaApiTest.php tests/Feature/Admin/AdminPanelRegressionTest.php
git diff --check
```

Expected: all tests PASS and `git diff --check` produces no output.

- [ ] **Step 4: Build and restart the local application**

Copy the updated source into the existing containers if Docker Hub remains
unavailable, run `npm run build` inside the frontend container, clear Laravel
caches, and restart the Compose services. Do not restart the frontend until its
production build succeeds.

Expected: frontend build completes and every container reports running; health
checks report healthy for web, frontend, backend, MySQL, Redis, and Mailpit.

- [ ] **Step 5: Perform live desktop and mobile QA**

Verify:

- `http://localhost:8080/` at 1440 × 900 and 390 × 844.
- `http://localhost:8080/about` at both sizes and in Nepali and English.
- `http://localhost:8080/articles/nepal-cricket-historic-victory`.
- Header, between-section, sidebar, in-article, and footer advertisements.
- No oversized empty advertisement area after a missing or failed creative.
- An authenticated image and video upload through the admin media library when
  test Cloudinary credentials are available; otherwise verify the signed
  Cloudinary requests through automated HTTP fakes and confirm local fallback
  live.

- [ ] **Step 6: Commit configuration and documentation**

```powershell
git add .env.example compose.yaml README.md
git commit -m "docs: configure Cloudinary media storage"
```

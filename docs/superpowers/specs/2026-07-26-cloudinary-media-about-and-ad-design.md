# Cloudinary Media, About Page, and Advertisement Design

## Objective

Unify image and video storage behind the Laravel API, improve the public About
page, and correct advertisement presentation without replacing the existing
Gorkhali Khabar brand, routes, content, or admin workflows.

## Scope

This change covers:

- Cloudinary-backed uploads and deletion in the Laravel media API.
- Local filesystem fallback when Cloudinary credentials are unavailable during
  local development.
- Database metadata required to manage Cloudinary assets safely.
- Existing admin image and video upload flows that use `/api/v1/media`.
- A responsive redesign of `/about`.
- Responsive advertisement sizing and spacing for existing ad placements.

It does not replace the Laravel API, redesign the full website, migrate old
local files automatically, or introduce a separate media microservice.

## Media Architecture

Laravel remains the source of truth for media management. The existing
`MediaStorageService` will select a storage provider:

- `cloudinary` when `MEDIA_STORAGE_DRIVER=cloudinary` and all required
  Cloudinary credentials are present.
- `local` when explicitly configured or when credentials are absent in local
  development.
- Production configuration using `cloudinary` fails clearly when credentials
  are missing instead of silently storing assets on an ephemeral filesystem.

The Cloudinary provider uploads images with `resource_type=image` and videos
with `resource_type=video`. Assets are stored under a Gorkhali Khabar folder
using generated public IDs. The service returns the secure URL, public ID,
resource type, dimensions when available, and provider name.

The media record stores provider metadata in its existing `variants` JSON
column:

```json
{
  "storage_provider": "cloudinary",
  "cloudinary_public_id": "gorkhali-khabar/uploads/example",
  "cloudinary_resource_type": "image"
}
```

Local media records use `storage_provider=local`. Existing records without this
metadata remain valid and are treated as local or remote URL records according
to their URL.

Deletion reads the record metadata. Cloudinary assets are deleted using the
stored public ID and resource type; local assets are deleted from the configured
public disk. Remote URL registrations are removed only from the database.

## Upload Flow

1. An authenticated ADMIN, EDITOR, or AUTHOR uploads an allowed image, video, or
   PDF through the existing admin interface.
2. Laravel validates MIME type and size before contacting storage.
3. `MediaStorageService` selects the configured provider and uploads the file.
4. Laravel persists the secure URL and provider metadata in `media_files`.
5. The API returns the same media response shape currently consumed by article,
   advertisement, and media-library forms.
6. Failed uploads return a clear Nepali error message and do not create a
   database record.

Images remain limited to 10 MB and videos to 100 MB. PDFs continue to use local
storage unless Cloudinary raw-file support is explicitly configured later.

## Configuration

The environment contract will include:

- `MEDIA_STORAGE_DRIVER=cloudinary`
- `CLOUDINARY_CLOUD_NAME`
- `CLOUDINARY_API_KEY`
- `CLOUDINARY_API_SECRET`
- `CLOUDINARY_FOLDER=gorkhali-khabar`

These values are passed to the Laravel backend, worker, and scheduler containers.
Secrets remain external to source control. The example environment file contains
blank placeholders only.

## About Page Design

The page uses an editorial, institutional direction consistent with the current
red, navy, white, and muted-gray brand.

Content hierarchy:

1. Branded editorial hero with a concise mission statement and newsroom motif.
2. Mission, vision, and public-service values in a responsive card grid.
3. Editorial standards section covering accuracy, balance, corrections, and
   accountability.
4. Newsroom/team statement with compact trust indicators.
5. Contact panel populated from existing site configuration.
6. Existing header and footer preserved.

The active language controls the page content. Nepali and English are not shown
simultaneously. The layout uses existing typography variables and components,
adds no external image dependency, and remains usable from 320 px mobile widths
through large desktop screens.

## Advertisement Presentation

`AdSlot` keeps the disclosure label but removes excessive empty vertical space.
Each placement uses a bounded container that matches its intended creative:

- Header and footer: centered leaderboard with a maximum width of 728 px.
- Between sections: centered banner with a maximum width of 970 px.
- Sidebar: 300 × 250 style presentation constrained to the available column.
- In article: centered responsive banner within the article width.

Images use their intrinsic aspect ratio, `object-fit: contain`, and a responsive
width. The slot background and padding remain subtle. On small screens, banners
scale down without cropping or horizontal overflow. Empty or failed ad data
renders no oversized placeholder.

## Error Handling

- Missing Cloudinary credentials in production return a configuration error.
- Cloudinary upload failures produce a safe API error without exposing secrets.
- Database creation occurs only after a successful upload.
- If database persistence fails after upload, the service attempts to remove the
  just-uploaded Cloudinary asset.
- Delete failures keep the database record so an administrator can retry.
- Existing local and remote URL media continue to render.

## Testing

Automated coverage will verify:

- Storage-provider selection and credential validation.
- Image and video Cloudinary upload options.
- Media metadata persistence.
- Cloudinary, local, and remote-record deletion behavior.
- API authorization, MIME validation, and size limits.
- About page language isolation and content sections.
- Advertisement placement classes and responsive image behavior.

Live verification will cover:

- Admin image upload.
- Admin video upload.
- Media preview and deletion.
- Homepage and article advertisement rendering at desktop and mobile sizes.
- `/about` at desktop and mobile sizes in both languages.
- Existing homepage, article, and admin routes after container restart.

## Acceptance Criteria

- New admin image and video uploads use Cloudinary when configured.
- Cloudinary URLs and deletion identifiers are persisted without exposing API
  secrets.
- Local development works without Cloudinary credentials.
- Existing media records remain compatible.
- The About page is visually stronger, responsive, and shows only the selected
  language.
- Advertisements display at their intended aspect ratios without oversized
  empty containers.
- Relevant frontend and Laravel tests pass, the production frontend build
  succeeds, and the local container stack is healthy.

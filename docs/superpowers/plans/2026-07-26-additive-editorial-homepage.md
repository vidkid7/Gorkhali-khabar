# Additive Editorial Homepage Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add useful editorial and multimedia homepage blocks, plus realistic seeded records, without removing or replacing any existing Gorkhali Khabar content.

**Architecture:** Extend the existing Laravel `HomeController` payload and idempotent legacy-compatible seed data. Add focused Next.js presentation components that render only when their payload contains data, while preserving current homepage sections and fallbacks.

**Tech Stack:** Laravel 13, PHP 8.3, MySQL, PHPUnit, Next.js 16, React 19, TypeScript, Tailwind CSS, Vitest, Docker Compose.

## Global Constraints

- Keep the current header, breaking-news ticker, hero, daily brief, category sections, trending blocks, provincial news, footer, and Laravel-driven ordering.
- New blocks are additive and data-dependent; an empty feed hides only that block.
- Seed data must be idempotent and must preserve existing rows and values.
- Existing role boundaries, audit logging, routes, and public URLs remain unchanged.
- No existing article, category, media, or permission data may be deleted.

---

### Task 1: Add failing backend coverage for additive seed records and payload fields

**Files:**
- Modify: `backend/tests/Feature/Schema/LegacyCompatibleSeederTest.php`
- Modify: `backend/tests/Feature/Api/V1/Content/HomeApiTest.php`

**Interfaces:**
- Consumes: existing `LegacyCompatibleSeeder`, `HomeController`, and legacy models.
- Produces: regression assertions for seeded editorial sections and additive homepage payload keys.

- [ ] **Step 1: Write the failing seed assertions**

Add assertions after the existing idempotency checks:

```php
$this->assertGreaterThan(0, DB::table('homepage_sections')->whereIn('section_key', [
    'latest-updates',
    'editor-picks',
    'opinion-desk',
    'media-highlights',
])->count());
$this->assertGreaterThan(0, DB::table('articles')->where('slug', 'seeded-editorial-lead')->count());
$this->assertGreaterThan(0, DB::table('breaking_news')->where('title', 'गोर्खाली खबर विशेष अपडेट')->count());
```

Record the `articles`, `breaking_news`, `reels`, and `galleries` counts in the first-run count map so the second seed run must preserve them.

- [ ] **Step 2: Write the failing homepage payload assertions**

Extend the home API test to assert the response includes:

```php
->assertJsonStructure([
    'data' => [
        'latestUpdates',
        'editorPicks',
        'opinion',
        'mediaHighlights',
    ],
])
->assertJsonPath('data.latestUpdates.0.slug', 'seeded-editorial-lead');
```

- [ ] **Step 3: Run the focused tests and verify they fail**

Run:

```bash
docker compose run --rm --no-deps backend php artisan test --filter='LegacyCompatibleSeederTest|HomeApiTest'
```

Expected: FAIL because the new seeded rows and payload keys do not yet exist.

---

### Task 2: Seed realistic additive editorial data

**Files:**
- Modify: `backend/database/seeders/LegacyCompatibleSeeder.php`
- Test: `backend/tests/Feature/Schema/LegacyCompatibleSeederTest.php`

**Interfaces:**
- Consumes: existing category IDs, seeded admin user, `insertDefaults`, and legacy-compatible table schemas.
- Produces: idempotent records for homepage sections, articles, breaking news, reels, and galleries.

- [ ] **Step 1: Add stable seeded categories and article rows**

Use `insertDefaults` keyed by `slug` to add at least these records:

```php
['slug' => 'seeded-editorial-lead', 'title' => 'समुदाय, प्रविधि र नयाँ नेपालको कथा', 'title_en' => 'Stories of community, technology, and a changing Nepal', 'status' => 'PUBLISHED', 'is_featured' => true],
['slug' => 'seeded-opinion-nepal', 'title' => 'नेपालको सार्वजनिक संवादलाई बलियो बनाउने समय', 'title_en' => 'Time to strengthen Nepal’s public conversation', 'status' => 'PUBLISHED'],
['slug' => 'seeded-province-update', 'title' => 'प्रदेशबाट आएका परिवर्तनका सात संकेत', 'title_en' => 'Seven signals of change from the provinces', 'status' => 'PUBLISHED'],
```

Populate required legacy fields using the existing article schema, assign the `samachar`, `bichar`, and `bagmati-pradesh` categories, and assign the seeded admin as author. Do not overwrite rows that already exist.

- [ ] **Step 2: Add homepage section seeds after existing sections**

Add stable rows with sort orders after the existing 30:

```php
['section_key' => 'latest-updates', 'title' => 'पछिल्ला अपडेट', 'title_en' => 'Latest Updates', 'category_slug' => 'samachar', 'layout' => 'list', 'sort_order' => 40, 'is_active' => true],
['section_key' => 'editor-picks', 'title' => 'सम्पादकको छनोट', 'title_en' => 'Editor’s Picks', 'category_slug' => 'feature', 'layout' => 'featured', 'sort_order' => 50, 'is_active' => true],
['section_key' => 'opinion-desk', 'title' => 'विचार डेस्क', 'title_en' => 'Opinion Desk', 'category_slug' => 'bichar', 'layout' => 'grid', 'sort_order' => 60, 'is_active' => true],
['section_key' => 'media-highlights', 'title' => 'फोटो र भिडियो', 'title_en' => 'Photo & Video', 'category_slug' => null, 'layout' => 'grid', 'sort_order' => 70, 'is_active' => true],
```

- [ ] **Step 3: Seed one breaking update, two reels, and one gallery**

Use stable slugs/IDs and existing tables/models. Make the media records point to local-safe public asset paths or existing public assets, and keep all records active. Use `insertDefaults` or a location-aware helper so the operation is repeatable.

- [ ] **Step 4: Run the seed test and verify idempotency**

Run the focused seeder test. Expected: PASS, with unchanged counts on the second run and existing values preserved.

---

### Task 3: Extend the Laravel homepage payload

**Files:**
- Modify: `backend/app/Http/Controllers/Api/V1/HomeController.php`
- Modify: `src/lib/home-api.ts`
- Test: `backend/tests/Feature/Api/V1/Content/HomeApiTest.php`
- Test: `tests/home-api.test.ts`

**Interfaces:**
- Consumes: published articles, active breaking news, active reels/galleries, and seeded sections.
- Produces: `latestUpdates`, `editorPicks`, `opinion`, and `mediaHighlights` arrays in the home response and matching TypeScript interfaces.

- [ ] **Step 1: Add backend queries**

Build bounded queries using existing public article scopes:

```php
$latestUpdates = $this->articles()->latest('published_at')->limit(8)->get();
$editorPicks = $this->articles()->where('is_featured', true)->latest('published_at')->limit(6)->get();
$opinion = $this->articles()->whereHas('category', fn ($query) => $query->where('slug', 'bichar'))->latest('published_at')->limit(6)->get();
$mediaHighlights = [
    'reels' => Reel::query()->where('is_active', true)->latest()->limit(6)->get(),
    'galleries' => Gallery::query()->where('is_active', true)->latest()->limit(4)->get(),
];
```

Keep existing payload keys unchanged and add the four new keys.

- [ ] **Step 2: Add TypeScript interfaces and mapping**

Extend `HomePayload` with typed arrays and a `MediaHighlights` interface. Keep existing optional article fields and normalize them in `src/app/page.tsx`.

- [ ] **Step 3: Run backend and frontend API tests**

Run:

```bash
docker compose run --rm --no-deps backend php artisan test --filter=HomeApiTest
npm test -- --run tests/home-api.test.ts
```

Expected: PASS.

---

### Task 4: Add additive frontend blocks and preserve existing sections

**Files:**
- Create: `src/components/home/LatestUpdatesRail.tsx`
- Create: `src/components/home/EditorialFeatureStrip.tsx`
- Create: `src/components/home/MediaHighlights.tsx`
- Modify: `src/app/page.tsx`
- Test: `tests/home-api.test.ts`

**Interfaces:**
- Consumes: new `HomePayload` arrays and the existing `normalizeArticle` behavior.
- Produces: optional rendered blocks that do not alter existing section order or remove existing sections.

- [ ] **Step 1: Write component-level empty-state tests**

Test that each component returns `null` for an empty array and renders its localized heading plus links when data exists.

- [ ] **Step 2: Implement the latest-updates rail**

Render a compact timestamped list with title, category, and reading time. Return `null` for no items. Link each item to the existing article route.

- [ ] **Step 3: Implement editor/opinion strips**

Use the existing `CategorySection`/`SectionHeader` visual language, but render only the new arrays. Keep the current category sections untouched.

- [ ] **Step 4: Implement media highlights**

Reuse the existing reel and gallery card components where compatible. Render a media strip only when at least one reel or gallery exists.

- [ ] **Step 5: Insert the blocks additively**

In `src/app/page.tsx`, keep the current hero, daily brief, mapped managed sections, trending, most-commented, missed-content, and provincial blocks. Add the new blocks around them with conditional rendering:

```tsx
<LatestUpdatesRail articles={data.latestUpdates.map(normalizeArticle)} />
<EditorialFeatureStrip editorPicks={...} opinion={...} />
<MediaHighlights {...data.mediaHighlights} />
```

Do not delete or hide any existing block.

---

### Task 5: Verify admin control, seed visibility, and production readiness

**Files:**
- Modify only if verification finds a defect.
- Test: existing backend admin regression suite and frontend tests.

**Interfaces:**
- Consumes: all implementation tasks.
- Produces: a running local app with seeded additive content and verified admin management.

- [ ] **Step 1: Run the full backend suite**

```bash
docker compose build backend
docker compose run --rm --no-deps backend php artisan test
```

Expected: all tests pass.

- [ ] **Step 2: Run frontend tests, strict TypeScript, and production build**

```bash
npm test -- --run
npx tsc --noEmit
npm run build
```

Expected: all tests, type checks, and the 65-route production build pass.

- [ ] **Step 3: Deploy the exact local build**

```bash
docker compose up -d --force-recreate backend worker scheduler frontend web
docker compose exec -T backend php artisan migrate --force
docker compose exec -T backend php artisan db:seed --class=LegacyCompatibleSeeder --force
```

- [ ] **Step 4: Verify live API and browser behavior**

Check:

```text
GET /api/health -> 200
GET /api/v1/home -> new additive keys contain seeded records
GET /api/v1/menus?location=header -> existing and seeded links
```

Using the open browser, verify the dashboard, Pages, Menus, Homepage Sections, and Live Blogs remain accessible, then verify the homepage shows the new seeded blocks alongside the old ones.

- [ ] **Step 5: Check integrity**

Run `git diff --check`, inspect `git status --short`, confirm no existing records were deleted, and report any unrelated baseline lint findings separately.

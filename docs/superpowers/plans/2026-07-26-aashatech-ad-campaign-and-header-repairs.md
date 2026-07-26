# AashaTech Ad Campaign and Header Repairs Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add a generated, Laravel-managed AashaTech ad campaign at balanced public placements while repairing the breaking ticker, reader-auth labeling, and missing service navigation.

**Architecture:** Laravel remains the advertisement source of truth and owns seeded campaign records, scheduling, tracking, and admin control. Focused React components render responsive disclosed advertisements, while homepage and article layouts decide placement cadence. Navigation merges editorial menu data with fixed services, and the breaking ticker owns its continuous track behavior.

**Tech Stack:** Next.js 16, React 19, TypeScript, Tailwind CSS, PHP 8.3, Laravel 13, MySQL, Vitest, Laravel feature tests, Playwright, built-in image generation.

## Global Constraints

- Target every campaign link at `https://www.aashatech.com/`.
- Display `विज्ञापन / Advertisement` on every rendered advertisement.
- Use three final bitmap sizes: `728×90`, `970×90`, and `300×250`.
- Store campaign assets under `public/images/ads/aashatech/`.
- Keep the hidden admin route hidden and protected by `ADMIN`, `EDITOR`, or `AUTHOR`.
- Label public authentication as `पाठक लगइन / Reader Login` and `पाठक दर्ता / Reader Registration`.
- Preserve Laravel-managed editorial navigation order.
- Always include Patro, Rashifal, Share Market, Forex, and Photo Gallery.
- Failed advertisements must collapse without leaving empty layout space.
- Do not interrupt headlines, metadata, media players, galleries, or navigation with ads.

---

### Task 1: Complete the Public Advertisement Renderer

**Files:**
- Modify: `src/components/ads/AdSlot.tsx`
- Delete: `src/components/ads/AdBanner.tsx`
- Create: `tests/ad-slot.test.tsx`

**Interfaces:**
- Consumes: `GET /api/v1/ads?position=HEADER` (and the other declared position types), `POST /api/v1/ads/{id}/impression`, and `POST /api/v1/ads/{id}/click`.
- Produces: `AdSlot({ position, className, compactLabel }: { position: AdPositionType; className?: string; compactLabel?: boolean })`.

- [ ] **Step 1: Write failing component tests**

```tsx
it("renders a disclosed sponsored link and tracks its impression", async () => {
  server.use(adResponse({ image_url: "/images/ads/aashatech/leaderboard.webp" }));
  render(<AdSlot position="HEADER" />);
  expect(await screen.findByText("विज्ञापन / Advertisement")).toBeVisible();
  expect(screen.getByRole("link")).toHaveAttribute("href", "https://www.aashatech.com/");
  await waitFor(() => expect(fetch).toHaveBeenCalledWith(
    "/api/v1/ads/aashatech-header/impression",
    expect.objectContaining({ method: "POST" }),
  ));
});

it("collapses when no advertisement is returned", async () => {
  server.use(emptyAdResponse());
  const { container } = render(<AdSlot position="HEADER" />);
  await waitFor(() => expect(container).toBeEmptyDOMElement());
});
```

- [ ] **Step 2: Run the focused test and verify failure**

Run: `npm test -- --run tests/ad-slot.test.tsx`

Expected: FAIL because the current renderer has no disclosure, typed positions, or deterministic empty-state test surface.

- [ ] **Step 3: Implement the focused renderer**

```tsx
export type AdPositionType =
  | "HEADER"
  | "SIDEBAR"
  | "IN_ARTICLE"
  | "FOOTER"
  | "BETWEEN_SECTIONS";

export function AdSlot({
  position,
  className = "",
  compactLabel = false,
}: {
  position: AdPositionType;
  className?: string;
  compactLabel?: boolean;
}) {
  // Fetch active ads, select one, record one impression, and collapse on errors.
  return ad && !imgError ? (
    <aside className={`ad-slot ${className}`} aria-label="Advertisement">
      <span className={compactLabel ? "ad-slot__label ad-slot__label--compact" : "ad-slot__label"}>
        विज्ञापन / Advertisement
      </span>
      <a
        href={ad.target_url}
        target="_blank"
        rel="noopener noreferrer sponsored"
        onClick={trackClick}
      >
        <Image
          src={ad.image_url}
          alt={ad.title}
          width={ad.position.width || 728}
          height={ad.position.height || 90}
          className="h-auto w-full object-cover"
          unoptimized
          onError={() => setImgError(true)}
        />
      </a>
    </aside>
  ) : null;
}
```

- [ ] **Step 4: Run the focused test**

Run: `npm test -- --run tests/ad-slot.test.tsx`

Expected: PASS.

- [ ] **Step 5: Commit the renderer**

```bash
git add src/components/ads/AdSlot.tsx src/components/ads/AdBanner.tsx tests/ad-slot.test.tsx
git commit -m "feat: complete public ad renderer"
```

---

### Task 2: Generate and Seed the AashaTech Campaign

**Files:**
- Create: `public/images/ads/aashatech/aashatech-leaderboard.webp`
- Create: `public/images/ads/aashatech/aashatech-section-banner.webp`
- Create: `public/images/ads/aashatech/aashatech-sidebar.webp`
- Modify: `backend/database/seeders/LegacyCompatibleSeeder.php`
- Modify: `backend/tests/Feature/Schema/LegacyCompatibleSeederTest.php`

**Interfaces:**
- Consumes: Existing `ad_positions` names and the three final asset URLs.
- Produces: One idempotent active AashaTech advertisement record per configured position.

- [ ] **Step 1: Add the failing seeder assertion**

```php
public function test_seeder_creates_one_aashatech_ad_per_position(): void
{
    $this->seed(LegacyCompatibleSeeder::class);
    $this->seed(LegacyCompatibleSeeder::class);

    $this->assertDatabaseCount('advertisements', 5);
    $this->assertSame(
        5,
        DB::table('advertisements')
            ->where('target_url', 'https://www.aashatech.com/')
            ->where('is_active', true)
            ->count(),
    );
}
```

- [ ] **Step 2: Run the seeder test and verify failure**

Run: `docker compose run --rm --no-deps backend php artisan test --filter=LegacyCompatibleSeederTest`

Expected: FAIL because no campaign advertisements exist.

- [ ] **Step 3: Generate three source artworks with the built-in image generator**

Use one built-in generation call per asset with these prompt invariants:

```text
Use case: ads-marketing
Asset type: AashaTech technology-services website advertisement background
Primary request: Create polished campaign artwork representing connected web systems, mobile applications, custom software, AI automation, secure cloud delivery, and Nepal-focused digital transformation.
Style/medium: premium editorial technology illustration, realistic depth, restrained and trustworthy
Composition/framing: keep the left third calm for exact brand copy; place connected digital-system imagery toward the right
Lighting/mood: confident, clear, modern
Constraints: no generated words, no letters, no logos, no watermark, no fake interface text, no people
```

Generate:

- A very wide leaderboard composition.
- A very wide between-sections composition with a different digital-system scene.
- A portrait-friendly 300×250 composition with a central connected-device scene.

- [ ] **Step 4: Compose exact copy and export final WebP assets**

Use Sharp with deterministic SVG text overlays:

```ts
const copy = [
  "AashaTech",
  "Digital Systems That Transform How Organizations Work",
  "Start a Project  →",
  "aashatech.com",
];
```

Export exact final dimensions `728×90`, `970×90`, and `300×250`. Inspect all outputs for legibility, crop quality, and correct spelling before adding them to the project.

- [ ] **Step 5: Upsert campaign records**

```php
$campaignByPosition = [
    'header' => ['id' => 'aashatech-header', 'image_url' => '/images/ads/aashatech/aashatech-leaderboard.webp'],
    'sidebar' => ['id' => 'aashatech-sidebar', 'image_url' => '/images/ads/aashatech/aashatech-sidebar.webp'],
    'in-article' => ['id' => 'aashatech-in-article', 'image_url' => '/images/ads/aashatech/aashatech-leaderboard.webp'],
    'footer' => ['id' => 'aashatech-footer', 'image_url' => '/images/ads/aashatech/aashatech-leaderboard.webp'],
    'between-sections' => ['id' => 'aashatech-between-sections', 'image_url' => '/images/ads/aashatech/aashatech-section-banner.webp'],
];

foreach ($campaignByPosition as $positionName => $campaign) {
    $positionId = DB::table('ad_positions')->where('name', $positionName)->value('id');
    DB::table('advertisements')->updateOrInsert(
        ['id' => $campaign['id']],
        [
            'title' => 'AashaTech Digital Systems',
            'image_url' => $campaign['image_url'],
            'target_url' => 'https://www.aashatech.com/',
            'position_id' => $positionId,
            'is_active' => true,
            'updated_at' => now(),
        ],
    );
}
```

- [ ] **Step 6: Run the seeder test twice**

Run: `docker compose run --rm --no-deps backend php artisan test --filter=LegacyCompatibleSeederTest`

Expected: PASS with exactly five campaign records after repeated seeding.

- [ ] **Step 7: Commit assets and seed data**

```bash
git add public/images/ads/aashatech backend/database/seeders/LegacyCompatibleSeeder.php backend/tests/Feature/Schema/LegacyCompatibleSeederTest.php
git commit -m "feat: seed AashaTech advertising campaign"
```

---

### Task 3: Add Balanced Homepage and Footer Placements

**Files:**
- Modify: `src/app/page.tsx`
- Modify: `src/components/layout/Footer.tsx`
- Create: `tests/ad-placement.test.tsx`

**Interfaces:**
- Consumes: `AdSlot` and `AdPositionType` from Task 1.
- Produces: Header, between-section, and footer placements with a two-or-three-section cadence.

- [ ] **Step 1: Write placement tests**

```tsx
it("places a header ad before the lead and section ads after every third managed section", () => {
  const tree = renderHomepage({ managedSections: sixSections });
  expect(screen.getAllByTestId("ad-HEADER")).toHaveLength(1);
  expect(screen.getAllByTestId("ad-BETWEEN_SECTIONS")).toHaveLength(2);
});

it("places one footer advertisement before the newsletter", () => {
  render(<Footer />);
  expect(screen.getByTestId("ad-FOOTER")).toBeBefore(screen.getByRole("form", {
    name: /newsletter|न्यूजलेटर/i,
  }));
});
```

- [ ] **Step 2: Run the placement test and verify failure**

Run: `npm test -- --run tests/ad-placement.test.tsx`

Expected: FAIL because the layouts do not mount ad placements.

- [ ] **Step 3: Implement the homepage cadence**

```tsx
<AdSlot position="HEADER" className="mb-6 sm:mb-8" />
<section className="editorial-band editorial-band--lead">
  <HeroDeck articles={data.featured.map(normalizeArticle)} />
</section>

{renderedSections.map((managed, index) => (
  <Fragment key={managed.id}>
    {renderManagedSection(managed)}
    {(index + 1) % 3 === 0 && (
      <AdSlot position="BETWEEN_SECTIONS" className="my-8 sm:my-12" />
    )}
  </Fragment>
))}
```

- [ ] **Step 4: Add the footer placement**

Render `<AdSlot position="FOOTER" />` immediately before the newsletter section, inside the public footer.

- [ ] **Step 5: Run placement and full frontend tests**

Run: `npm test -- --run tests/ad-placement.test.tsx`

Expected: PASS.

Run: `npm test -- --run`

Expected: All frontend tests pass.

- [ ] **Step 6: Commit public placements**

```bash
git add src/app/page.tsx src/components/layout/Footer.tsx tests/ad-placement.test.tsx
git commit -m "feat: add balanced public ad placements"
```

---

### Task 4: Add Qualifying Article and Sidebar Placements

**Files:**
- Modify: `src/components/articles/ArticleContent.tsx`
- Modify: `src/app/articles/[slug]/page.tsx`
- Create: `tests/article-ad-placement.test.tsx`

**Interfaces:**
- Consumes: `AdSlot` from Task 1 and article `word_count`.
- Produces: `ArticleContent` with a middle ad for articles of at least 500 words and a responsive sidebar placement on article detail.

- [ ] **Step 1: Write failing article-placement tests**

```tsx
it("shows an in-article ad for content with at least 500 words", () => {
  render(<ArticleContent article={{ ...article, word_count: 700 }} />);
  expect(screen.getByTestId("ad-IN_ARTICLE")).toBeVisible();
});

it("does not interrupt a short article", () => {
  render(<ArticleContent article={{ ...article, word_count: 180 }} />);
  expect(screen.queryByTestId("ad-IN_ARTICLE")).not.toBeInTheDocument();
});
```

- [ ] **Step 2: Run the focused test and verify failure**

Run: `npm test -- --run tests/article-ad-placement.test.tsx`

Expected: FAIL because article ads are not rendered.

- [ ] **Step 3: Split sanitized article content at a paragraph boundary**

```tsx
const showArticleAd = word_count >= 500;
const { beforeAd, afterAd } = splitHtmlAtParagraph(contentHtml, 0.5);

<div dangerouslySetInnerHTML={{ __html: beforeAd }} />
{showArticleAd && <AdSlot position="IN_ARTICLE" className="my-8" />}
<div dangerouslySetInnerHTML={{ __html: afterAd }} />
```

`splitHtmlAtParagraph` must return the original content as `beforeAd` and an empty `afterAd` when no safe paragraph boundary exists.

- [ ] **Step 4: Add the responsive sidebar placement**

Use a desktop `lg:grid-cols-[minmax(0,1fr)_300px]` article shell. Render `<AdSlot position="SIDEBAR" />` in the 300px column and allow it to move below the article on narrower layouts.

- [ ] **Step 5: Run focused and full tests**

Run: `npm test -- --run tests/article-ad-placement.test.tsx`

Expected: PASS.

Run: `npm test -- --run`

Expected: All frontend tests pass.

- [ ] **Step 6: Commit article placements**

```bash
git add src/components/articles/ArticleContent.tsx src/app/articles/[slug]/page.tsx tests/article-ad-placement.test.tsx
git commit -m "feat: add article advertising placements"
```

---

### Task 5: Repair the Breaking-News Ticker

**Files:**
- Modify: `src/components/ui/BreakingNewsTicker.tsx`
- Modify: `src/app/globals.css`
- Create: `tests/breaking-news-ticker.test.tsx`

**Interfaces:**
- Consumes: Active breaking-news items with optional article slugs.
- Produces: A repeated `breaking-news-track` with focus/hover pause and reduced-motion fallback.

- [ ] **Step 1: Write failing ticker tests**

```tsx
it("duplicates a single item to keep the ticker track continuous", () => {
  render(<BreakingNewsTicker items={[breakingItem]} />);
  expect(screen.getAllByText(breakingItem.title)).toHaveLength(2);
});

it("keeps the fixed label separate from the moving track", () => {
  render(<BreakingNewsTicker items={[breakingItem]} />);
  expect(screen.getByTestId("breaking-label")).not.toHaveClass("breaking-news-track");
  expect(screen.getByTestId("breaking-track")).toHaveClass("breaking-news-track");
});
```

- [ ] **Step 2: Run the focused test and verify failure**

Run: `npm test -- --run tests/breaking-news-ticker.test.tsx`

Expected: FAIL because one item is rendered only once.

- [ ] **Step 3: Implement a repeated half-track loop**

```tsx
const repeatedItems = items.length < 4
  ? [...items, ...items]
  : items;

<div data-testid="breaking-track" className="breaking-news-track">
  {repeatedItems.map(renderItem)}
</div>
```

```css
.breaking-news-track {
  display: flex;
  width: max-content;
  animation: breaking-news-loop 28s linear infinite;
}

.breaking-news-track:hover,
.breaking-news-track:focus-within {
  animation-play-state: paused;
}

@keyframes breaking-news-loop {
  from { transform: translateX(0); }
  to { transform: translateX(-50%); }
}

@media (prefers-reduced-motion: reduce) {
  .breaking-news-track {
    animation: none;
    transform: none;
  }
  .breaking-news-track > :nth-child(n + 2) {
    display: none;
  }
}
```

- [ ] **Step 4: Run focused and full frontend tests**

Run: `npm test -- --run tests/breaking-news-ticker.test.tsx`

Expected: PASS.

Run: `npm test -- --run`

Expected: All frontend tests pass.

- [ ] **Step 5: Commit the ticker repair**

```bash
git add src/components/ui/BreakingNewsTicker.tsx src/app/globals.css tests/breaking-news-ticker.test.tsx
git commit -m "fix: keep breaking news continuously visible"
```

---

### Task 6: Clarify Reader Auth and Restore Service Navigation

**Files:**
- Modify: `src/components/layout/Header.tsx`
- Modify: `src/components/layout/navigation-data.ts`
- Modify: `src/i18n/locales/ne.json`
- Modify: `src/i18n/locales/en.json`
- Modify: `tests/navigation-data.test.ts`
- Create: `tests/header-reader-auth.test.tsx`
- Modify: `backend/tests/Feature/Api/V1/Auth/AuthApiTest.php`

**Interfaces:**
- Consumes: Laravel-managed header menu entries.
- Produces: `mergePublicNavItems(managed: PublicNavItem[]): PublicNavItem[]` and reader-specific auth labels.

- [ ] **Step 1: Add failing navigation merge tests**

```ts
it("retains fixed services when managed editorial navigation loads", async () => {
  const items = await getPublicNavItems(apiReturningManagedNews);
  expect(items.map((item) => item.href)).toEqual(expect.arrayContaining([
    "/patro",
    "/rashifal",
    "/share-market",
    "/patro/forex",
    "/galleries",
  ]));
});

it("prefers a managed entry when its URL duplicates a fixed service", async () => {
  const items = await getPublicNavItems(apiReturningManagedPatro);
  expect(items.filter((item) => item.href === "/patro")).toHaveLength(1);
  expect(items.find((item) => item.href === "/patro")?.label).toBe("सेवा पात्रो");
});
```

- [ ] **Step 2: Add failing reader-label and authorization tests**

```tsx
render(<Header />);
expect(screen.getByRole("link", { name: "पाठक लगइन" })).toHaveAttribute("href", "/auth/login");
expect(screen.getByRole("link", { name: "पाठक दर्ता" })).toHaveAttribute("href", "/auth/register");
expect(screen.queryByText(/admin|एडमिन/i)).not.toBeInTheDocument();
```

```php
public function test_new_reader_cannot_access_staff_session(): void
{
    $reader = User::factory()->create(['role' => 'READER']);
    $this->actingAs($reader)->get('/gorkhali-admin')->assertRedirect();
}
```

- [ ] **Step 3: Run focused tests and verify failure**

Run: `npm test -- --run tests/navigation-data.test.ts tests/header-reader-auth.test.tsx`

Expected: FAIL because service items are replaced and public labels are generic.

Run: `docker compose run --rm --no-deps backend php artisan test --filter=AuthApiTest`

Expected: Existing auth tests pass and the new staff-denial assertion exposes any missing boundary.

- [ ] **Step 4: Merge editorial and fixed services**

```ts
const FIXED_SERVICE_ITEMS: PublicNavItem[] = [
  { key: "patro", href: "/patro", group: "service" },
  { key: "horoscope", href: "/rashifal", group: "service" },
  { key: "shareMarket", href: "/share-market", group: "service" },
  { key: "forex", href: "/patro/forex", group: "service" },
  { key: "photoGallery", href: "/galleries", group: "service" },
];

export function mergePublicNavItems(managed: PublicNavItem[]): PublicNavItem[] {
  const managedUrls = new Set(managed.map((item) => item.href));
  return [
    ...managed,
    ...FIXED_SERVICE_ITEMS.filter((item) => !managedUrls.has(item.href)),
  ];
}
```

Use the merged list after every successful menu API request. Expose all services in the drawer and move overflow service links into `अन्य / More` at desktop widths below `2xl`.

- [ ] **Step 5: Add reader-specific translations**

```json
{
  "readerLogin": "पाठक लगइन",
  "readerRegister": "पाठक दर्ता"
}
```

```json
{
  "readerLogin": "Reader Login",
  "readerRegister": "Reader Registration"
}
```

Replace only public header and reader-auth-page labels; retain staff-login copy for staff callbacks.

- [ ] **Step 6: Run focused and full test suites**

Run: `npm test -- --run tests/navigation-data.test.ts tests/header-reader-auth.test.tsx`

Expected: PASS.

Run: `docker compose run --rm --no-deps backend php artisan test --filter=AuthApiTest`

Expected: PASS.

Run: `npm test -- --run`

Expected: All frontend tests pass.

- [ ] **Step 7: Commit header repairs**

```bash
git add src/components/layout/Header.tsx src/components/layout/navigation-data.ts src/i18n/locales/ne.json src/i18n/locales/en.json tests/navigation-data.test.ts tests/header-reader-auth.test.tsx backend/tests/Feature/Api/V1/Auth/AuthApiTest.php
git commit -m "fix: clarify reader auth and restore service navigation"
```

---

### Task 7: Deploy Locally and Verify the Complete Experience

**Files:**
- Modify only if verification reveals a scoped defect.

**Interfaces:**
- Consumes: All deliverables from Tasks 1–6.
- Produces: Healthy local containers and evidence that the accepted experience works.

- [ ] **Step 1: Run static and automated verification**

Run:

```bash
npx tsc --noEmit
npm test -- --run
docker compose run --rm --no-deps backend php artisan test --filter='AdvertisementApiTest|LegacyCompatibleSeederTest|AuthApiTest'
git diff --check
```

Expected: TypeScript passes, all frontend tests pass, selected Laravel suites pass, and no whitespace errors appear.

- [ ] **Step 2: Seed the local campaign and rebuild**

Run:

```bash
docker compose exec backend php artisan db:seed --class=LegacyCompatibleSeeder --force
docker compose build frontend
docker compose up -d --force-recreate frontend web
docker compose ps
```

Expected: Frontend and web containers become healthy; the seeded campaign has five active position records.

- [ ] **Step 3: Verify advertisement APIs**

Check each type:

```text
GET /api/v1/ads?position=HEADER
GET /api/v1/ads?position=SIDEBAR
GET /api/v1/ads?position=IN_ARTICLE
GET /api/v1/ads?position=FOOTER
GET /api/v1/ads?position=BETWEEN_SECTIONS
```

Expected: Each returns one active AashaTech record with the correct local image URL and `https://www.aashatech.com/` target.

- [ ] **Step 4: Run desktop and mobile browser checks**

Use Playwright at `1280×800`, `1024×768`, `390×844`, and `375×812`.

Verify:

- Homepage has no horizontal overflow.
- Header, balanced between-section, and footer ads load.
- Every ad exposes `विज्ञापन / Advertisement`.
- Every ad link targets AashaTech in a new tab.
- The breaking headline stays visible at initial load, halfway through the loop, and after one full loop.
- Patro, Rashifal, Share Market, Forex, and Photo Gallery are discoverable.
- Public buttons read Reader Login and Reader Registration in English and their approved Nepali equivalents.
- No public admin link is visible.
- A qualifying article has an in-article and sidebar ad without reducing readable article width below the accepted mobile layout.

- [ ] **Step 5: Verify tracking**

Record the chosen ad's initial impression and click counts, load its placement once, click it in a controlled browser page, and query the record again.

Expected: Impressions increase by at least one and clicks increase by exactly one for the controlled click.

- [ ] **Step 6: Inspect generated assets**

Open all three final WebP files at original resolution and confirm:

- Exact spelling.
- No generated text artifacts.
- Correct aspect ratio.
- Clear CTA.
- No clipping at responsive widths.
- Consistent campaign identity.

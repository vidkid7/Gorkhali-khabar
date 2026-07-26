# Editorial Verticals and Category Seeding Implementation Plan
> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (- [ ]) syntax for tracking.

**Goal:** Add missing and new editorial categories with safe sample content, expose them through the More menu, and strengthen category-page editorial discovery.

**Architecture:** Laravel's idempotent LegacyCompatibleSeeder remains the source of truth for categories, menus, and sample articles. The Next.js public navigation derives secondary items from the admin-managed menu API, while category pages add a data-derived most-read rail and related-topic strip without introducing a new service.

**Tech Stack:** Laravel 13, MySQL, PHP 8.3, Next.js 16, React 19, TypeScript, Tailwind CSS, PHPUnit, Vitest, Docker Compose

## Global Constraints

- Keep existing primary navigation, categories, routes, logo, header, footer, and advertisements unchanged.
- Add new editorial verticals under अन्य / More and keep them editable through the existing Laravel admin interfaces.
- Seeded articles are clearly fictional demonstration content; do not present claims, events, or bylines as current reporting.
- Re-running the seeder must be idempotent and must not overwrite administrator-authored content.
- Use local placeholder images only; do not copy Washington Post content, assets, branding, or code.
- Design additions must be responsive from 320 px mobile through desktop.

---

### Task 1: Seed Editorial Categories and Demonstration Articles

**Files:**
- Modify: backend/database/seeders/LegacyCompatibleSeeder.php
- Modify: backend/tests/Feature/Schema/LegacyCompatibleSeederTest.php

**Interfaces:**
- Consumes: insertDefaults(string $table, string $key, array $rows, bool $preserveExisting = false)
- Produces: category rows and three published sample articles per editorial-vertical-* slug

- [ ] **Step 1: Write the failing seed assertions**

Add this test after seeding twice:

~~~php
$slugs = [
    'antarrashtriya', 'feature', 'video', 'anveshan', 'jalbayu-paryawaran',
    'krishi', 'paryatan', 'kala-sanskriti', 'jivanshaili', 'surakshya-aparadh',
    'rojgari', 'prabas',
];

$this->assertSame(count($slugs), DB::table('categories')->whereIn('slug', $slugs)->count());
foreach ($slugs as $slug) {
    $this->assertSame(3, DB::table('articles')->where('slug', 'like', "editorial-vertical-{$slug}-%")->count());
}
~~~

Also assert a pre-existing samachar record remains unchanged after two seed runs.

- [ ] **Step 2: Run test to verify it fails**

Run:

~~~powershell
docker exec gorkhali_redesign-nextjs-backend-1 php artisan test tests/Feature/Schema/LegacyCompatibleSeederTest.php
~~~

Expected: FAIL because the vertical slugs and their articles do not exist.

- [ ] **Step 3: Add the twelve category definitions**

Add antarrashtriya, feature, video, and these exact new editorial rows to the top-level category seed list:

~~~php
['name' => 'अनुसन्धान', 'name_en' => 'Investigations', 'slug' => 'anveshan', 'color' => '#7f1d1d', 'sort_order' => 33],
['name' => 'जलवायु र वातावरण', 'name_en' => 'Climate & Environment', 'slug' => 'jalbayu-paryawaran', 'color' => '#0f766e', 'sort_order' => 34],
['name' => 'कृषि', 'name_en' => 'Agriculture', 'slug' => 'krishi', 'color' => '#4d7c0f', 'sort_order' => 35],
['name' => 'पर्यटन', 'name_en' => 'Travel', 'slug' => 'paryatan', 'color' => '#0369a1', 'sort_order' => 36],
['name' => 'कला र संस्कृति', 'name_en' => 'Arts & Culture', 'slug' => 'kala-sanskriti', 'color' => '#9d174d', 'sort_order' => 37],
['name' => 'जीवनशैली', 'name_en' => 'Lifestyle', 'slug' => 'jivanshaili', 'color' => '#a16207', 'sort_order' => 38],
['name' => 'सुरक्षा र अपराध', 'name_en' => 'Security & Crime', 'slug' => 'surakshya-aparadh', 'color' => '#475569', 'sort_order' => 39],
['name' => 'रोजगारी', 'name_en' => 'Jobs & Careers', 'slug' => 'rojgari', 'color' => '#4338ca', 'sort_order' => 40],
['name' => 'प्रवास', 'name_en' => 'Diaspora', 'slug' => 'prabas', 'color' => '#be123c', 'sort_order' => 41],
~~~

- [ ] **Step 4: Add idempotent sample article generation**

After retrieving the administrator ID, define a vertical map and use a nested loop to generate three published articles per vertical:

~~~php
foreach ($verticals as $slug => $vertical) {
    $categoryId = DB::table('categories')->where('slug', $slug)->value('id');
    foreach ([1, 2, 3] as $index) {
        $articles[] = [
            'slug' => "editorial-vertical-{$slug}-{$index}",
            'title' => "नमूना: {$vertical['name']} विशेष रिपोर्ट {$index}",
            'title_en' => "Sample: {$vertical['name_en']} report {$index}",
            'excerpt' => 'यो प्रदर्शनका लागि तयार गरिएको नमूना सामग्री हो।',
            'excerpt_en' => 'This is fictional demonstration content prepared for the portal.',
            'content' => '<p>यो सामग्री परीक्षण र प्रदर्शनका लागि मात्र हो।</p>',
            'content_en' => '<p>This story is fictional sample content for testing and demonstration.</p>',
            'status' => 'PUBLISHED',
            'reading_time' => 3 + $index,
            'word_count' => 500 + ($index * 90),
            'published_at' => $now->copy()->subHours(($index * 3) + 1),
            'category_id' => $categoryId,
            'author_id' => $adminId,
        ];
    }
}
$this->insertDefaults('articles', 'slug', $articles, true);
~~~

- [ ] **Step 5: Run the seed tests and reseed local data**

Run:

~~~powershell
docker exec gorkhali_redesign-nextjs-backend-1 php artisan test tests/Feature/Schema/LegacyCompatibleSeederTest.php
docker exec gorkhali_redesign-nextjs-backend-1 php artisan db:seed --class=LegacyCompatibleSeeder --force
~~~

Expected: tests PASS and repeated seeding creates no duplicate rows.

- [ ] **Step 6: Commit**

~~~powershell
git add backend/database/seeders/LegacyCompatibleSeeder.php backend/tests/Feature/Schema/LegacyCompatibleSeederTest.php
git commit -m "feat: seed editorial verticals"
~~~

### Task 2: Group Editorial Verticals in More Navigation

**Files:**
- Modify: backend/database/seeders/LegacyCompatibleSeeder.php
- Modify: src/components/layout/navigation-data.ts
- Modify: tests/navigation-data.test.ts

**Interfaces:**
- Consumes: EditorialMenu[] from /api/v1/menus?location=header
- Produces: PublicNavItem[] where the first six remain primary and all added verticals are secondary

- [ ] **Step 1: Write a failing navigation test**

~~~ts
it("keeps editorial verticals in the More group", async () => {
  const items = await getPublicNavItems({ get: vi.fn().mockResolvedValue([
    { id: "investigations", location: "header", label: "अनुसन्धान", label_en: "Investigations", href: "/categories/anveshan", sort_order: 160 },
  ]) });

  expect(items.find((item) => item.href === "/categories/anveshan")).toEqual(
    expect.objectContaining({ group: "secondary", label_en: "Investigations" }),
  );
});
~~~

- [ ] **Step 2: Run test to verify it fails**

Run:

~~~powershell
npm test -- --run tests/navigation-data.test.ts
~~~

Expected: FAIL because single-item API responses are currently promoted by index.

- [ ] **Step 3: Implement explicit grouping and menu seeding**

Extend the navigation mapping so only slash, News, Politics, Economy, Sports, and Opinion are primary. Seed all twelve vertical routes after existing secondary header entries and in the footer, such as:

~~~php
['location' => 'header', 'label' => 'अनुसन्धान', 'label_en' => 'Investigations', 'href' => '/categories/anveshan', 'sort_order' => 160],
['location' => 'header', 'label' => 'जलवायु', 'label_en' => 'Climate', 'href' => '/categories/jalbayu-paryawaran', 'sort_order' => 170],
~~~

- [ ] **Step 4: Run navigation tests**

Run:

~~~powershell
npm test -- --run tests/navigation-data.test.ts
~~~

Expected: PASS and all added category routes resolve to secondary.

- [ ] **Step 5: Commit**

~~~powershell
git add backend/database/seeders/LegacyCompatibleSeeder.php src/components/layout/navigation-data.ts tests/navigation-data.test.ts
git commit -m "feat: group editorial verticals under More"
~~~

### Task 3: Add Category Discovery and Most-Read Rail

**Files:**
- Create: src/components/categories/CategoryDiscovery.tsx
- Modify: src/app/categories/[slug]/page.tsx
- Modify: src/app/globals.css
- Create: tests/category-discovery.test.tsx

**Interfaces:**
- Consumes: HomeArticle[], category.slug, and category.color
- Produces: CategoryDiscovery({ articles, activeSlug, accentColor }): JSX.Element

- [ ] **Step 1: Write a failing component test**

~~~tsx
render(
  <CategoryDiscovery
    activeSlug="anveshan"
    accentColor="#7f1d1d"
    articles={articles}
  />,
);

expect(screen.getByRole("heading", { name: "धेरै पढिएको" })).toBeVisible();
expect(screen.getAllByTestId("most-read-item")).toHaveLength(3);
expect(screen.getByTestId("category-topic-strip")).toBeVisible();
~~~

- [ ] **Step 2: Run test to verify it fails**

Run:

~~~powershell
npm test -- --run tests/category-discovery.test.tsx
~~~

Expected: FAIL because CategoryDiscovery does not exist.

- [ ] **Step 3: Implement the compact component**

Create a semantic aside with a धेरै पढिएको heading, rank numbers 01–03, and article links for the first three supplied items. Above it, render a topic strip with the active category and links to News, Politics, Economy, Sports, and Opinion. Use only supplied article data; do not add a popularity API.

- [ ] **Step 4: Integrate it into the archive layout**

For archives with four or more articles, change the lead grid to three desktop columns: hero, supporting articles, and CategoryDiscovery. Keep the horizontal-list fallback for fewer than four articles. Place the topic strip above every archive layout.

- [ ] **Step 5: Add responsive styles and run tests**

Add category-discovery styles using existing CSS tokens. Stack the rail below the editorial lead below 1024 px.

Run:

~~~powershell
npm test -- --run tests/category-discovery.test.tsx
npx tsc --noEmit
~~~

Expected: PASS.

- [ ] **Step 6: Commit**

~~~powershell
git add src/components/categories/CategoryDiscovery.tsx src/app/categories/[slug]/page.tsx src/app/globals.css tests/category-discovery.test.tsx
git commit -m "feat: add category discovery rail"
~~~

### Task 4: Full Verification and Live Check

**Files:**
- Modify: none

- [ ] **Step 1: Run complete automated checks**

~~~powershell
npm test -- --run
npx tsc --noEmit
docker exec gorkhali_redesign-nextjs-backend-1 php artisan test tests/Feature/Schema/LegacyCompatibleSeederTest.php
git diff --check
~~~

Expected: all checks pass with no whitespace errors.

- [ ] **Step 2: Rebuild, seed, and restart local containers**

Copy updated source into the existing containers if the registry remains unavailable. Run the frontend production build before restart, seed LegacyCompatibleSeeder, then restart frontend, backend, worker, scheduler, and web.

- [ ] **Step 3: Verify routes and responsiveness**

At 1440 px and 390 px, verify homepage primary navigation remains unchanged, More includes verticals, and all new category routes return 200 with three sample stories and no horizontal overflow:
- /categories/anveshan
- /categories/jalbayu-paryawaran
- /categories/krishi
- /categories/paryatan
- /categories/kala-sanskriti
- /categories/jivanshaili
- /categories/surakshya-aparadh
- /categories/rojgari
- /categories/prabas

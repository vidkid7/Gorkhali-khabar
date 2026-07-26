# Static Frontend Parity Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Make the Vite static frontend deployed to cPanel match the current Next.js public UI while retaining Laravel as the only live backend.

**Architecture:** Keep `static-site` as the browser-rendered application and expand it from the current simplified components into a public-site parity layer. It will consume only same-origin Laravel `/api/v1` endpoints. Apache continues routing `/api/*` and `/gorkhali-admin` to Laravel and routes all other public paths to `index.html`.

**Tech Stack:** React 19, TypeScript, Vite, Vitest, Laravel/PHP, Apache/cPanel.

## Global Constraints

- Do not add a Node.js runtime requirement to cPanel.
- Do not change Laravel routes, database schema, or `/gorkhali-admin` behavior.
- Do not hard-code editorial content that is available through `/api/v1/home`.
- Preserve every existing public URL handled by `static-site/src/router.ts`.
- Keep all production API calls same-origin under `/api/v1`.

---

### Task 1: Define static parity data contracts

**Files:**
- Modify: `static-site/src/api/types.ts`
- Modify: `static-site/src/api/client.ts`
- Test: `static-site/src/api/client.test.ts`

**Interfaces:**
- Consumes: Laravel JSON envelopes from `/api/v1/home`, `/api/v1/articles/*`, `/api/v1/categories/*`, and public utility endpoints.
- Produces: typed client functions returning normalized data for static page components.

- [ ] **Step 1: Write failing tests for homepage section and visual metadata normalization**

```ts
it("returns a usable empty collection when an optional home section is absent", async () => {
  global.fetch = vi.fn().mockResolvedValue(jsonResponse({ success: true, data: {} }));
  await expect(apiGetOptionalArray("/api/v1/home-sections")).resolves.toEqual([]);
});
```

- [ ] **Step 2: Run the focused test and verify it fails because the optional helper is absent**

Run: `npm.cmd run static:test -- static-site/src/api/client.test.ts`

Expected: a failing import or undefined-function error for `apiGetOptionalArray`.

- [ ] **Step 3: Add minimal normalized contracts and optional-array client helper**

```ts
export async function apiGetOptionalArray<T>(path: string): Promise<T[]> {
  try {
    const data = await apiGet<T[] | undefined>(path);
    return Array.isArray(data) ? data : [];
  } catch (error) {
    if (error instanceof ApiRequestError && error.status === 404) return [];
    throw error;
  }
}
```

- [ ] **Step 4: Run the focused tests and verify they pass**

Run: `npm.cmd run static:test -- static-site/src/api/client.test.ts`

Expected: `1 passed` or more with no failure.

- [ ] **Step 5: Commit the contract change**

```bash
git add static-site/src/api/types.ts static-site/src/api/client.ts static-site/src/api/client.test.ts
git commit -m "feat: extend static frontend API contracts"
```

### Task 2: Rebuild the shared public chrome

**Files:**
- Modify: `static-site/src/components/SiteHeader.tsx`
- Modify: `static-site/src/components/SiteFooter.tsx`
- Create: `static-site/src/components/EditorialNav.tsx`
- Create: `static-site/src/components/AdPlacement.tsx`
- Modify: `static-site/src/components/SiteHeader.test.tsx`
- Create: `static-site/src/components/SiteFooter.test.tsx`

**Interfaces:**
- Consumes: public navigation labels, `window.location.pathname`, and `/api/v1/home` breaking/trending data where available.
- Produces: responsive header/footer with public links and an ad placeholder that accepts `position` and optional creative data.

- [ ] **Step 1: Write failing header and footer parity tests**

```tsx
it("renders the primary nav, patron link, and admin-free public header", () => {
  render(<SiteHeader />);
  expect(screen.getByRole("link", { name: "गृहपृष्ठ" })).toHaveAttribute("href", "/");
  expect(screen.getByRole("link", { name: "पात्रो" })).toHaveAttribute("href", "/patro");
  expect(screen.queryByRole("link", { name: "एडमिन" })).not.toBeInTheDocument();
});
```

- [ ] **Step 2: Run the focused component tests and verify the new assertions fail**

Run: `npm.cmd run static:test -- static-site/src/components/SiteHeader.test.tsx static-site/src/components/SiteFooter.test.tsx`

Expected: failure because the current header lacks the complete navigation and footer test file.

- [ ] **Step 3: Implement the shared chrome using the current Next layout hierarchy as the visual source**

```tsx
export function AdPlacement({ position }: { position: "HEADER" | "BETWEEN_SECTIONS" | "ARTICLE" }) {
  return <aside className={`ad-placement ad-placement--${position.toLowerCase()}`} aria-label="विज्ञापन" />;
}
```

Implement the utility row, wordmark, primary and service navigation, mobile drawer, ticker/search affordance, newsletter/footer columns, and only public-facing links. Keep `/gorkhali-admin` out of the visible navigation.

- [ ] **Step 4: Run shared-chrome tests**

Run: `npm.cmd run static:test -- static-site/src/components/SiteHeader.test.tsx static-site/src/components/SiteFooter.test.tsx`

Expected: all selected tests pass.

- [ ] **Step 5: Commit the shared chrome**

```bash
git add static-site/src/components
git commit -m "feat: match static site header and footer"
```

### Task 3: Port the editorial homepage composition

**Files:**
- Modify: `static-site/src/pages/HomePage.tsx`
- Create: `static-site/src/components/HeroDeck.tsx`
- Create: `static-site/src/components/LatestUpdatesRail.tsx`
- Create: `static-site/src/components/EditorialSection.tsx`
- Create: `static-site/src/components/MediaHighlights.tsx`
- Modify: `static-site/src/pages/HomePage.test.tsx`

**Interfaces:**
- Consumes: `HomePayload` from `/api/v1/home`.
- Produces: hero, latest updates, managed category sections, provinces, media/reels, and reusable article rails.

- [ ] **Step 1: Write a failing homepage layout test**

```tsx
it("renders the editorial hero and latest updates from the Laravel home payload", async () => {
  mockHomePayload({ featured: [article("lead")], latestUpdates: [article("latest")] });
  render(<HomePage />);
  expect(await screen.findByRole("heading", { name: "lead" })).toBeInTheDocument();
  expect(screen.getByText("ताजा अपडेट")).toBeInTheDocument();
});
```

- [ ] **Step 2: Run the focused homepage test and verify it fails**

Run: `npm.cmd run static:test -- static-site/src/pages/HomePage.test.tsx`

Expected: failure because the current page does not render the editorial hero/latest-update components.

- [ ] **Step 3: Implement componentized homepage parity**

```tsx
<main className="public-home public-page-shell">
  <AdPlacement position="HEADER" />
  <HeroDeck articles={data.featured} />
  <LatestUpdatesRail articles={data.latestUpdates} />
  {sections.map((section) => <EditorialSection key={section.slug} {...section} />)}
  <MediaHighlights reels={data.mediaHighlights.reels} galleries={data.mediaHighlights.galleries} />
</main>
```

Use runtime data only, preserve fallback/error states, and render a section only when it has content.

- [ ] **Step 4: Run homepage tests**

Run: `npm.cmd run static:test -- static-site/src/pages/HomePage.test.tsx`

Expected: all homepage tests pass.

- [ ] **Step 5: Commit homepage parity**

```bash
git add static-site/src/pages/HomePage.tsx static-site/src/components/HeroDeck.tsx static-site/src/components/LatestUpdatesRail.tsx static-site/src/components/EditorialSection.tsx static-site/src/components/MediaHighlights.tsx static-site/src/pages/HomePage.test.tsx
git commit -m "feat: port editorial homepage to static frontend"
```

### Task 4: Match article, category, and public utility presentation

**Files:**
- Modify: `static-site/src/pages/ArticlePage.tsx`
- Modify: `static-site/src/pages/CategoryPage.tsx`
- Modify: `static-site/src/pages/SearchPage.tsx`
- Modify: `static-site/src/pages/CollectionPage.tsx`
- Modify: `static-site/src/pages/StaticPage.tsx`
- Modify: `static-site/src/pages/UtilityPage.tsx`
- Modify: `static-site/src/pages/ArticlePage.test.tsx`
- Modify: `static-site/src/pages/CategoryPage.test.tsx`
- Modify: `static-site/src/pages/SearchPage.test.tsx`

**Interfaces:**
- Consumes: route params from `resolvePublicRoute` and Laravel article/category/search payloads.
- Produces: responsive public pages with a consistent reading layout and graceful unsupported-route behavior.

- [ ] **Step 1: Add failing article and category visual-structure tests**

```tsx
it("renders an article headline, metadata, reading body, and related articles", async () => {
  mockArticlePayload(article("story"));
  render(<ArticlePage slug="story" />);
  expect(await screen.findByRole("article")).toBeInTheDocument();
  expect(screen.getByText("सम्बन्धित समाचार")).toBeInTheDocument();
});
```

- [ ] **Step 2: Run page tests and verify expected failures**

Run: `npm.cmd run static:test -- static-site/src/pages/ArticlePage.test.tsx static-site/src/pages/CategoryPage.test.tsx static-site/src/pages/SearchPage.test.tsx`

Expected: failures for the missing reading and category-discovery structure.

- [ ] **Step 3: Implement the desktop and mobile reading/archive layouts**

Use semantic `<article>`, `<aside>`, and `<nav>` landmarks. Preserve pagination, search query handling, linkable category/article paths, and a branded empty state for utility pages that are not yet exposed by Laravel.

- [ ] **Step 4: Run the focused page tests**

Run: `npm.cmd run static:test -- static-site/src/pages/ArticlePage.test.tsx static-site/src/pages/CategoryPage.test.tsx static-site/src/pages/SearchPage.test.tsx`

Expected: all selected tests pass.

- [ ] **Step 5: Commit page parity work**

```bash
git add static-site/src/pages
git commit -m "feat: match static article and archive pages"
```

### Task 5: Complete route coverage and responsive visual system

**Files:**
- Modify: `static-site/src/router.ts`
- Modify: `static-site/src/router.test.ts`
- Modify: `static-site/src/styles.css`
- Modify: `static-site/src/components/ArticleCard.tsx`
- Modify: `static-site/src/components/ArticleSection.tsx`

**Interfaces:**
- Consumes: browser paths and existing article component props.
- Produces: parity routes, design tokens, responsive breakpoints, and accessible mobile navigation behavior.

- [ ] **Step 1: Add failing router tests for public Next routes that must not become 404s**

```ts
it.each(["/live", "/patro/forex", "/share-market", "/rashifal"])("recognizes %s", (pathname) => {
  expect(resolvePublicRoute(pathname).name).not.toBe("not-found");
});
```

- [ ] **Step 2: Run the router test and verify it fails**

Run: `npm.cmd run static:test -- static-site/src/router.test.ts`

Expected: failures for the routes not represented by the static router.

- [ ] **Step 3: Implement route fallbacks and the visual token system**

Define shared spacing, color, typography, card, breakpoint, and reduced-motion rules in `styles.css`. Use responsive containers and grid collapse rules rather than page-specific inline widths.

- [ ] **Step 4: Run router and component tests**

Run: `npm.cmd run static:test -- static-site/src/router.test.ts static-site/src/pages`

Expected: all selected tests pass.

- [ ] **Step 5: Commit responsive route coverage**

```bash
git add static-site/src/router.ts static-site/src/router.test.ts static-site/src/styles.css static-site/src/components/ArticleCard.tsx static-site/src/components/ArticleSection.tsx
git commit -m "feat: complete static public route coverage"
```

### Task 6: Verify parity and deploy the rebuilt static release

**Files:**
- Modify: `tests/static-deployment.test.ts` only if a new public route needs an Apache rewrite guard.
- Modify: `static-site/deploy/.htaccess` only if tests prove the rewrite order needs adjustment.

**Interfaces:**
- Consumes: Vite production output and the existing Laravel front controller.
- Produces: a verified public release that leaves Laravel and its `.env` untouched.

- [ ] **Step 1: Add a failing deployment assertion only if API/admin rewrite ordering changes**

```ts
expect(rules.indexOf("RewriteRule ^api/(.*)$ laravel.php [L,QSA]")).toBeLessThan(
  rules.indexOf("RewriteRule ^ index.html [L]"),
);
```

- [ ] **Step 2: Run the deployment test before any rewrite change**

Run: `npm.cmd run static:test -- tests/static-deployment.test.ts`

Expected: current behavior stays green unless a new route exposes a real rewrite regression.

- [ ] **Step 3: Build and perform visual parity checks**

Run:

```bash
npm.cmd run static:test
npm.cmd run static:build
npm.cmd run build
```

Compare `/`, one `/articles/<slug>`, one `/categories/<slug>`, and mobile-width screenshots for the Next and static local servers.

- [ ] **Step 4: Deploy only the rebuilt public artifact**

Upload the new `static-site/dist` contents plus `static-site/deploy/.htaccess` and `static-site/deploy/laravel.php` to the cPanel document root. Do not overwrite `/home/gorkhalikhabar/gorkhali-laravel/.env`.

- [ ] **Step 5: Verify the production origin and commit/push deployment source changes**

Run direct-origin checks for `/`, `/api/v1/home`, an article, a category, and `/gorkhali-admin`; then run `git status`, commit remaining source changes, and push `main`.

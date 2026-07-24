# PHP-Compatible Static Frontend Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace the Laravel service-status homepage with a PHP-hostable static React frontend that preserves the Gorkhali Khabar public design and loads current content from Laravel.

**Architecture:** A focused Vite application in `static-site/` reuses the repository's design tokens and public assets without requiring a Node runtime in production. It performs same-origin requests through a typed Laravel API client, resolves public routes in the browser, and leaves `/api/*` and `/gorkhali-admin/*` under Laravel. Deployment builds locally, uploads the static artifact, and installs Apache rules that serve static routes first and Laravel routes second.

**Tech Stack:** React 19, TypeScript 5, Vite 7, Vitest, Laravel 12, Apache `.htaccess`, PHP 8.2

## Global Constraints

- The cPanel production runtime is PHP 8.2 and must not start a Node application.
- The public site reads live data from same-origin Laravel `/api/v1/*` endpoints.
- Laravel remains installed at `/home1/gorkhal1/gorkhali-laravel`.
- Laravel admin remains available at `/gorkhali-admin`.
- Authenticated reader features are excluded from the static public release.
- Deployment must back up `public_html` before replacing frontend files.
- Unknown public routes must render a public not-found screen.

---

### Task 1: Static-site build boundary and API client

**Files:**
- Create: `static-site/index.html`
- Create: `static-site/vite.config.ts`
- Create: `static-site/tsconfig.json`
- Create: `static-site/src/api/types.ts`
- Create: `static-site/src/api/client.ts`
- Create: `static-site/src/api/client.test.ts`
- Modify: `package.json`

**Interfaces:**
- Consumes: Laravel's `{ success: true, data: T }` response envelope.
- Produces: `apiGet<T>(path: string, init?: RequestInit): Promise<T>` and public API entity types.

- [ ] **Step 1: Write the failing API-client tests**

```ts
// static-site/src/api/client.test.ts
import { afterEach, describe, expect, it, vi } from "vitest";
import { apiGet, ApiRequestError } from "./client";

afterEach(() => vi.unstubAllGlobals());

describe("apiGet", () => {
  it("unwraps successful Laravel responses", async () => {
    vi.stubGlobal("fetch", vi.fn().mockResolvedValue(new Response(
      JSON.stringify({ success: true, data: { service: "gorkhali-api" } }),
      { status: 200, headers: { "Content-Type": "application/json" } },
    )));
    await expect(apiGet<{ service: string }>("/api/v1/status"))
      .resolves.toEqual({ service: "gorkhali-api" });
  });

  it("throws a stable public error for failed requests", async () => {
    vi.stubGlobal("fetch", vi.fn().mockResolvedValue(new Response(
      JSON.stringify({ success: false, error: "Not found" }),
      { status: 404, headers: { "Content-Type": "application/json" } },
    )));
    await expect(apiGet("/api/v1/missing"))
      .rejects.toEqual(new ApiRequestError(404, "Not found"));
  });
});
```

- [ ] **Step 2: Run the test and verify the missing-module failure**

Run:

```bash
npx vitest run static-site/src/api/client.test.ts
```

Expected: FAIL because `static-site/src/api/client.ts` does not exist.

- [ ] **Step 3: Add the API entity contracts**

```ts
// static-site/src/api/types.ts
export type LocalizedText = string | null;

export interface Category {
  id: string;
  slug: string;
  name: string;
  name_en: LocalizedText;
  color: string | null;
}

export interface Article {
  id: string;
  slug: string;
  title: string;
  title_en: LocalizedText;
  excerpt: LocalizedText;
  excerpt_en: LocalizedText;
  content?: LocalizedText;
  content_en?: LocalizedText;
  featured_image: LocalizedText;
  reading_time: number | null;
  published_at: string | null;
  view_count: number;
  comment_count: number;
  category: Category | null;
  author?: { id?: string; name: string } | null;
}

export interface HomePayload {
  breakingNews: Array<{
    id: string;
    title: string;
    title_en: LocalizedText;
    article?: { slug: string } | null;
  }>;
  featured: Article[];
  categoryGroups: Record<string, Article[]>;
  trending: Article[];
  mostCommented: Article[];
  reels: Array<Record<string, unknown>>;
  matches: Array<Record<string, unknown>>;
  olderArticles: Article[];
  editorPicks: Article[];
  provinceGroups: Record<string, Article[]>;
}

export interface Paginated<T> {
  data: T[];
  current_page?: number;
  last_page?: number;
  total?: number;
}
```

- [ ] **Step 4: Implement the API client**

```ts
// static-site/src/api/client.ts
interface ApiEnvelope<T> {
  success: boolean;
  data?: T;
  error?: string;
}

export class ApiRequestError extends Error {
  constructor(
    public readonly status: number,
    message: string,
  ) {
    super(message);
    this.name = "ApiRequestError";
  }
}

export async function apiGet<T>(
  path: string,
  init: RequestInit = {},
): Promise<T> {
  const response = await fetch(path, {
    ...init,
    headers: {
      Accept: "application/json",
      ...init.headers,
    },
  });
  const body = await response.json() as ApiEnvelope<T>;
  if (!response.ok || !body.success || body.data === undefined) {
    throw new ApiRequestError(
      response.status,
      body.error || "Unable to load content.",
    );
  }
  return body.data;
}
```

- [ ] **Step 5: Add the isolated Vite configuration**

```ts
// static-site/vite.config.ts
import { defineConfig } from "vite";
import react from "@vitejs/plugin-react";
import { resolve } from "node:path";

export default defineConfig({
  root: resolve(__dirname),
  plugins: [react()],
  publicDir: resolve(__dirname, "../public"),
  build: {
    outDir: resolve(__dirname, "dist"),
    emptyOutDir: true,
  },
  resolve: {
    alias: {
      "@static": resolve(__dirname, "src"),
    },
  },
});
```

```json
// static-site/tsconfig.json
{
  "compilerOptions": {
    "target": "ES2020",
    "useDefineForClassFields": true,
    "lib": ["ES2020", "DOM", "DOM.Iterable"],
    "allowJs": false,
    "skipLibCheck": true,
    "esModuleInterop": true,
    "allowSyntheticDefaultImports": true,
    "strict": true,
    "forceConsistentCasingInFileNames": true,
    "module": "ESNext",
    "moduleResolution": "Bundler",
    "resolveJsonModule": true,
    "isolatedModules": true,
    "noEmit": true,
    "jsx": "react-jsx",
    "types": ["vitest/globals"]
  },
  "include": ["src", "vite.config.ts"]
}
```

```html
<!-- static-site/index.html -->
<!doctype html>
<html lang="ne">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="गोर्खाली खबर — नेपालको विश्वसनीय अनलाइन समाचार पोर्टल" />
    <link rel="icon" href="/icons/logo.svg" />
    <title>गोर्खाली खबर</title>
  </head>
  <body>
    <div id="root"></div>
    <script type="module" src="/src/main.tsx"></script>
  </body>
</html>
```

Add these scripts to the root `package.json`:

```json
{
  "scripts": {
    "static:dev": "vite --config static-site/vite.config.ts",
    "static:build": "vite build --config static-site/vite.config.ts",
    "static:test": "vitest run static-site"
  }
}
```

- [ ] **Step 6: Run the API-client test**

Run:

```bash
npm run static:test -- static-site/src/api/client.test.ts
```

Expected: PASS with 2 tests.

- [ ] **Step 7: Commit the build boundary**

```bash
git add package.json static-site/index.html static-site/tsconfig.json static-site/vite.config.ts static-site/src/api
git commit -m "feat: add static frontend API client"
```

---

### Task 2: Public application shell and browser routing

**Files:**
- Create: `static-site/src/main.tsx`
- Create: `static-site/src/App.tsx`
- Create: `static-site/src/router.ts`
- Create: `static-site/src/router.test.ts`
- Create: `static-site/src/styles.css`
- Create: `static-site/src/components/SiteHeader.tsx`
- Create: `static-site/src/components/SiteFooter.tsx`
- Create: `static-site/src/components/PageState.tsx`

**Interfaces:**
- Consumes: `window.location.pathname`.
- Produces: `resolvePublicRoute(pathname: string): PublicRoute` and a stable public layout.

- [ ] **Step 1: Write route-resolution tests**

```ts
// static-site/src/router.test.ts
import { describe, expect, it } from "vitest";
import { resolvePublicRoute } from "./router";

describe("resolvePublicRoute", () => {
  it.each([
    ["/", { name: "home" }],
    ["/articles/example-story", { name: "article", slug: "example-story" }],
    ["/categories/politics", { name: "category", slug: "politics" }],
    ["/search", { name: "search" }],
    ["/sports", { name: "utility", slug: "sports" }],
  ])("resolves %s", (path, expected) => {
    expect(resolvePublicRoute(path)).toEqual(expected);
  });

  it("returns not-found for unknown paths", () => {
    expect(resolvePublicRoute("/not-a-public-route")).toEqual({ name: "not-found" });
  });
});
```

- [ ] **Step 2: Run the route test**

Run:

```bash
npm run static:test -- static-site/src/router.test.ts
```

Expected: FAIL because `static-site/src/router.ts` does not exist.

- [ ] **Step 3: Implement public route resolution**

```ts
// static-site/src/router.ts
export type PublicRoute =
  | { name: "home" }
  | { name: "article"; slug: string }
  | { name: "category"; slug: string }
  | { name: "search" }
  | { name: "reels" }
  | { name: "galleries" }
  | { name: "utility"; slug: string }
  | { name: "static"; slug: string }
  | { name: "not-found" };

const utilityRoutes = new Set([
  "finance", "sports", "rashifal", "patro", "share-market",
]);
const staticRoutes = new Set([
  "about", "privacy-policy", "terms-of-service", "cookie-policy",
]);

export function resolvePublicRoute(pathname: string): PublicRoute {
  const parts = pathname.replace(/^\/+|\/+$/g, "").split("/").filter(Boolean);
  if (parts.length === 0) return { name: "home" };
  if (parts[0] === "articles" && parts[1]) return { name: "article", slug: parts[1] };
  if (parts[0] === "categories" && parts[1]) return { name: "category", slug: parts[1] };
  if (parts[0] === "search" && parts.length === 1) return { name: "search" };
  if (parts[0] === "reels" && parts.length === 1) return { name: "reels" };
  if (parts[0] === "galleries" && parts.length === 1) return { name: "galleries" };
  if (utilityRoutes.has(parts[0]) && parts.length === 1) {
    return { name: "utility", slug: parts[0] };
  }
  if (staticRoutes.has(parts[0]) && parts.length === 1) {
    return { name: "static", slug: parts[0] };
  }
  return { name: "not-found" };
}
```

- [ ] **Step 4: Add accessible loading, empty, error, and not-found states**

```tsx
// static-site/src/components/PageState.tsx
export function LoadingState() {
  return <div className="page-state" role="status">समाचार लोड हुँदैछ…</div>;
}

export function EmptyState({ message = "सामग्री उपलब्ध छैन।" }: { message?: string }) {
  return <div className="page-state">{message}</div>;
}

export function ErrorState({ retry }: { retry: () => void }) {
  return (
    <div className="page-state" role="alert">
      <p>सामग्री लोड गर्न सकिएन।</p>
      <button type="button" onClick={retry}>पुनः प्रयास गर्नुहोस्</button>
    </div>
  );
}

export function NotFoundPage() {
  return (
    <main className="page-state">
      <h1>पृष्ठ फेला परेन</h1>
      <a href="/">गृहपृष्ठमा फर्कनुहोस्</a>
    </main>
  );
}
```

- [ ] **Step 5: Implement the public shell**

Create `SiteHeader.tsx` with the Gorkhali logo, the category links currently used by the production header, a search link, and a mobile navigation toggle. Create `SiteFooter.tsx` with the site identity, public policy links, and current-year copyright. Copy the public color, spacing, typography, card, grid, and responsive rules needed by these components into `static-site/src/styles.css`.

```tsx
// static-site/src/main.tsx
import { StrictMode } from "react";
import { createRoot } from "react-dom/client";
import { App } from "./App";
import "./styles.css";

createRoot(document.getElementById("root")!).render(
  <StrictMode><App /></StrictMode>,
);
```

```tsx
// static-site/src/App.tsx
import { resolvePublicRoute } from "./router";
import { SiteHeader } from "./components/SiteHeader";
import { SiteFooter } from "./components/SiteFooter";
import { NotFoundPage } from "./components/PageState";
import { HomePage } from "./pages/HomePage";
import { ArticlePage } from "./pages/ArticlePage";
import { CategoryPage } from "./pages/CategoryPage";
import { SearchPage } from "./pages/SearchPage";
import { CollectionPage } from "./pages/CollectionPage";
import { UtilityPage } from "./pages/UtilityPage";
import { StaticPage } from "./pages/StaticPage";

export function App() {
  const route = resolvePublicRoute(window.location.pathname);
  let page;
  switch (route.name) {
    case "home": page = <HomePage />; break;
    case "article": page = <ArticlePage slug={route.slug} />; break;
    case "category": page = <CategoryPage slug={route.slug} />; break;
    case "search": page = <SearchPage />; break;
    case "reels": page = <CollectionPage kind="reels" />; break;
    case "galleries": page = <CollectionPage kind="galleries" />; break;
    case "utility": page = <UtilityPage slug={route.slug} />; break;
    case "static": page = <StaticPage slug={route.slug} />; break;
    default: page = <NotFoundPage />;
  }
  return <><SiteHeader /><div className="site-main">{page}</div><SiteFooter /></>;
}
```

- [ ] **Step 6: Run route and shell tests**

Run:

```bash
npm run static:test -- static-site/src/router.test.ts
npm run static:build
```

Expected: route tests PASS and `static-site/dist/index.html` is generated.

- [ ] **Step 7: Commit the application shell**

```bash
git add static-site/src
git commit -m "feat: add static public application shell"
```

---

### Task 3: Live homepage

**Files:**
- Create: `static-site/src/hooks/useApiResource.ts`
- Create: `static-site/src/hooks/useApiResource.test.tsx`
- Create: `static-site/src/pages/HomePage.tsx`
- Create: `static-site/src/pages/HomePage.test.tsx`
- Create: `static-site/src/components/ArticleCard.tsx`
- Create: `static-site/src/components/ArticleSection.tsx`
- Create: `static-site/src/components/BreakingTicker.tsx`

**Interfaces:**
- Consumes: `apiGet<HomePayload>("/api/v1/home")`.
- Produces: a live public homepage matching the established hero, ticker, section-grid, sidebar, and provincial-news hierarchy.

- [ ] **Step 1: Write the resource-hook test**

```tsx
// static-site/src/hooks/useApiResource.test.tsx
import { renderHook, waitFor } from "@testing-library/react";
import { describe, expect, it, vi } from "vitest";
import { useApiResource } from "./useApiResource";

describe("useApiResource", () => {
  it("exposes loading then data", async () => {
    const loader = vi.fn().mockResolvedValue({ value: 7 });
    const { result } = renderHook(() => useApiResource(loader, []));
    expect(result.current.loading).toBe(true);
    await waitFor(() => expect(result.current.data).toEqual({ value: 7 }));
    expect(result.current.error).toBeNull();
  });
});
```

- [ ] **Step 2: Implement the resource hook**

```ts
// static-site/src/hooks/useApiResource.ts
import { useCallback, useEffect, useState, type DependencyList } from "react";

export function useApiResource<T>(
  loader: () => Promise<T>,
  dependencies: DependencyList,
) {
  const [data, setData] = useState<T | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<Error | null>(null);

  const load = useCallback(async () => {
    setLoading(true);
    setError(null);
    try {
      setData(await loader());
    } catch (cause) {
      setError(cause instanceof Error ? cause : new Error("Request failed"));
    } finally {
      setLoading(false);
    }
  }, dependencies);

  useEffect(() => { void load(); }, [load]);
  return { data, loading, error, retry: load };
}
```

- [ ] **Step 3: Write the homepage behavior test**

Mock `/api/v1/home` with one breaking item, one featured article, and one `samachar` article. Assert that the Nepali titles, article URLs, and loading-to-content transition render. Mock a rejected request and assert that the retry button appears.

- [ ] **Step 4: Build homepage presentation components**

`ArticleCard` accepts `{ article: Article; featured?: boolean }` and links to `/articles/${article.slug}`. `ArticleSection` accepts a title, slug, article list, and `featured | grid | list` layout. `BreakingTicker` renders the breaking-news items as links and uses recent articles when the breaking array is empty.

- [ ] **Step 5: Implement the live homepage**

```tsx
// static-site/src/pages/HomePage.tsx
import { apiGet } from "../api/client";
import type { HomePayload } from "../api/types";
import { useApiResource } from "../hooks/useApiResource";
import { LoadingState, ErrorState, EmptyState } from "../components/PageState";
import { BreakingTicker } from "../components/BreakingTicker";
import { ArticleSection } from "../components/ArticleSection";

const sections = [
  ["ताजा समाचार", "samachar", "featured"],
  ["फिचर", "feature", "featured"],
  ["कभर स्टोरी", "cover-story", "grid"],
  ["प्रविधि", "prabidhi", "featured"],
  ["अन्तर्वार्ता", "antarvaarta", "list"],
  ["खेलकुद", "khelkud", "featured"],
] as const;

export function HomePage() {
  const state = useApiResource(
    () => apiGet<HomePayload>("/api/v1/home"),
    [],
  );
  if (state.loading) return <LoadingState />;
  if (state.error) return <ErrorState retry={state.retry} />;
  if (!state.data) return <EmptyState />;
  return (
    <main>
      <BreakingTicker items={state.data.breakingNews} fallback={state.data.trending} />
      <ArticleSection title="प्रमुख समाचार" slug="" articles={state.data.featured} layout="featured" />
      {sections.map(([title, slug, layout]) => (
        <ArticleSection
          key={slug}
          title={title}
          slug={slug}
          articles={state.data.categoryGroups[slug] || []}
          layout={layout}
        />
      ))}
    </main>
  );
}
```

Append these sections after the category loop so every Laravel home payload group has a visible consumer:

```tsx
<ArticleSection title="ट्रेन्डिङ" slug="" articles={state.data.trending} layout="list" />
<ArticleSection title="धेरै प्रतिक्रिया" slug="" articles={state.data.mostCommented} layout="list" />
<ArticleSection title="सम्पादकको रोजाइ" slug="" articles={state.data.editorPicks} layout="grid" />
<ArticleSection title="लोकप्रिय पुराना समाचार" slug="" articles={state.data.olderArticles} layout="grid" />

{Object.entries(state.data.provinceGroups).map(([province, articles]) => (
  <ArticleSection
    key={province}
    title={province}
    slug={province}
    articles={articles}
    layout="list"
  />
))}

<section className="content-section">
  <h2>रिल्स</h2>
  <div className="media-grid">
    {state.data.reels.map((reel) => {
      const id = String(reel.id || "");
      const title = String(reel.title || reel.caption || "गोर्खाली रिल");
      return <a key={id} href={`/reels#${id}`} className="media-card">{title}</a>;
    })}
  </div>
</section>

<section className="content-section">
  <h2>लाइभ स्कोर</h2>
  <div className="score-grid">
    {state.data.matches.map((match) => {
      const record = match as Record<string, any>;
      const id = String(match.id || "");
      const home = String(record.home_team?.name || record.homeTeam?.name || "Home");
      const away = String(record.away_team?.name || record.awayTeam?.name || "Away");
      return <div key={id} className="score-card">{home} — {away}</div>;
    })}
  </div>
</section>
```

- [ ] **Step 6: Run homepage tests and build**

Run:

```bash
npm run static:test -- static-site/src/hooks/useApiResource.test.tsx static-site/src/pages/HomePage.test.tsx
npm run static:build
```

Expected: tests PASS and the build succeeds without Prisma imports.

- [ ] **Step 7: Commit the live homepage**

```bash
git add static-site/src/hooks static-site/src/pages/HomePage* static-site/src/components
git commit -m "feat: load static homepage from Laravel"
```

---

### Task 4: Article, category, and search routes

**Files:**
- Create: `static-site/src/pages/ArticlePage.tsx`
- Create: `static-site/src/pages/ArticlePage.test.tsx`
- Create: `static-site/src/pages/CategoryPage.tsx`
- Create: `static-site/src/pages/CategoryPage.test.tsx`
- Create: `static-site/src/pages/SearchPage.tsx`
- Create: `static-site/src/components/Pagination.tsx`

**Interfaces:**
- Consumes: article-by-slug, category, article-index, and search Laravel endpoints.
- Produces: live article detail, category listing, and search result routes.

- [ ] **Step 1: Write article-route tests**

Mock `/api/v1/articles/slug/example-story` and assert the article title, sanitized body container, category link, author, publish date, and featured image. Mock HTTP 404 and assert the public not-found state.

- [ ] **Step 2: Implement the article route**

```tsx
// static-site/src/pages/ArticlePage.tsx
import { apiGet, ApiRequestError } from "../api/client";
import type { Article } from "../api/types";
import { useApiResource } from "../hooks/useApiResource";
import { ErrorState, LoadingState, NotFoundPage } from "../components/PageState";

export function ArticlePage({ slug }: { slug: string }) {
  const state = useApiResource(
    () => apiGet<Article>(`/api/v1/articles/slug/${encodeURIComponent(slug)}`),
    [slug],
  );
  if (state.loading) return <LoadingState />;
  if (state.error instanceof ApiRequestError && state.error.status === 404) return <NotFoundPage />;
  if (state.error) return <ErrorState retry={state.retry} />;
  if (!state.data) return <NotFoundPage />;
  const article = state.data;
  return (
    <article className="article-detail">
      {article.category && <a href={`/categories/${article.category.slug}`}>{article.category.name}</a>}
      <h1>{article.title}</h1>
      <div className="article-meta">{article.author?.name} · {article.published_at}</div>
      {article.featured_image && <img src={article.featured_image} alt={article.title} />}
      <div className="article-body" dangerouslySetInnerHTML={{ __html: article.content || "" }} />
    </article>
  );
}
```

Laravel's existing content sanitizer remains the trusted source for stored HTML. Add a regression test containing a normal paragraph and verify it renders.

- [ ] **Step 3: Write and implement category tests**

Resolve the category from `/api/v1/categories`, then request `/api/v1/articles?category={slug}&page={page}&pageSize=12`. Render the category title, cards, empty state, and `Pagination`. The page query is read from `URLSearchParams`.

- [ ] **Step 4: Write and implement search tests**

Read `q` and `page` from `window.location.search`, request `/api/v1/search?q={q}&page={page}&pageSize=12`, preserve the query in pagination links, and show a Nepali prompt when no query is supplied.

- [ ] **Step 5: Run route tests**

Run:

```bash
npm run static:test -- static-site/src/pages/ArticlePage.test.tsx static-site/src/pages/CategoryPage.test.tsx
npm run static:test -- static-site/src/pages/SearchPage.test.tsx
npm run static:build
```

Expected: all tests PASS and build completes.

- [ ] **Step 6: Commit core public routes**

```bash
git add static-site/src/pages/ArticlePage* static-site/src/pages/CategoryPage* static-site/src/pages/SearchPage* static-site/src/components/Pagination.tsx
git commit -m "feat: add live article category and search routes"
```

---

### Task 5: Collections, utilities, and informational pages

**Files:**
- Create: `static-site/src/pages/CollectionPage.tsx`
- Create: `static-site/src/pages/UtilityPage.tsx`
- Create: `static-site/src/pages/StaticPage.tsx`
- Create: `static-site/src/pages/public-pages.test.tsx`
- Create: `static-site/src/components/UtilityPanels.tsx`

**Interfaces:**
- Consumes: reels, galleries, finance, sports, rashifal, calendar, Nepse, settings, and public static copy.
- Produces: remaining public routes defined by the approved specification.

- [ ] **Step 1: Write collection and utility tests**

Test these endpoint mappings:

```ts
expect(endpointForUtility("finance")).toEqual([
  "/api/v1/finance/exchange-rates",
  "/api/v1/finance/gold-silver",
]);
expect(endpointForUtility("sports")).toEqual([
  "/api/v1/sports/tournaments",
  "/api/v1/sports/matches",
]);
expect(endpointForUtility("rashifal")).toEqual(["/api/v1/rashifal"]);
expect(endpointForUtility("patro")).toEqual([
  "/api/v1/calendar/panchang",
  "/api/v1/calendar/holidays",
]);
expect(endpointForUtility("share-market")).toEqual(["/api/v1/nepse"]);
```

Also test that `CollectionPage` requests `/api/v1/reels` or `/api/v1/galleries` based on its `kind`.

- [ ] **Step 2: Implement collection pages**

Render responsive cards with media, title, description, and available detail link. Support loading, empty, retry, and malformed-media fallback states.

- [ ] **Step 3: Implement utility endpoint mapping and panels**

```ts
export function endpointForUtility(slug: string): string[] {
  const map: Record<string, string[]> = {
    finance: ["/api/v1/finance/exchange-rates", "/api/v1/finance/gold-silver"],
    sports: ["/api/v1/sports/tournaments", "/api/v1/sports/matches"],
    rashifal: ["/api/v1/rashifal"],
    patro: ["/api/v1/calendar/panchang", "/api/v1/calendar/holidays"],
    "share-market": ["/api/v1/nepse"],
  };
  return map[slug] || [];
}
```

`UtilityPage` performs the mapped requests in parallel and renders a focused panel for each response instead of exposing raw JSON.

- [ ] **Step 4: Implement informational pages**

`StaticPage` contains approved public copy for `about`, `privacy-policy`, `terms-of-service`, and `cookie-policy`, retaining the site header and footer. It does not fetch authenticated data.

- [ ] **Step 5: Run public-page tests and build**

Run:

```bash
npm run static:test -- static-site/src/pages/public-pages.test.tsx
npm run static:build
```

Expected: tests PASS and build completes.

- [ ] **Step 6: Commit remaining public pages**

```bash
git add static-site/src/pages/CollectionPage.tsx static-site/src/pages/UtilityPage.tsx static-site/src/pages/StaticPage.tsx static-site/src/pages/public-pages.test.tsx static-site/src/components/UtilityPanels.tsx
git commit -m "feat: add static public utility pages"
```

---

### Task 6: Apache routing and recoverable cPanel deployment

**Files:**
- Create: `static-site/deploy/.htaccess`
- Create: `scripts/deploy-static-php.ps1`
- Create: `scripts/cpanel-install-static.sh`
- Create: `tests/static-deployment.test.ts`

**Interfaces:**
- Consumes: `static-site/dist` and the existing cPanel Laravel installation.
- Produces: a timestamped public-root backup and a static-first, Laravel-backed `public_html`.

- [ ] **Step 1: Write deployment-artifact tests**

```ts
// tests/static-deployment.test.ts
import { readFileSync } from "node:fs";
import { describe, expect, it } from "vitest";

const rules = readFileSync("static-site/deploy/.htaccess", "utf8");

describe("static PHP deployment rules", () => {
  it("routes API and admin requests to Laravel", () => {
    expect(rules).toContain("RewriteRule ^api(?:/|$) laravel.php [L,QSA]");
    expect(rules).toContain("RewriteRule ^gorkhali-admin(?:/|$) laravel.php [L,QSA]");
  });

  it("serves static files before the SPA fallback", () => {
    expect(rules.indexOf("RewriteCond %{REQUEST_FILENAME} -f"))
      .toBeLessThan(rules.indexOf("RewriteRule ^ index.html [L]"));
  });
});
```

- [ ] **Step 2: Add Apache rules**

```apache
# static-site/deploy/.htaccess
DirectoryIndex index.html
Options -Indexes
RewriteEngine On

RewriteRule ^api(?:/|$) laravel.php [L,QSA]
RewriteRule ^sanctum(?:/|$) laravel.php [L,QSA]
RewriteRule ^gorkhali-admin(?:/|$) laravel.php [L,QSA]

RewriteCond %{REQUEST_FILENAME} -f [OR]
RewriteCond %{REQUEST_FILENAME} -d
RewriteRule ^ - [L]

RewriteRule ^ index.html [L]
```

- [ ] **Step 3: Add the server-side installer**

```bash
#!/usr/bin/env bash
set -Eeuo pipefail

ARCHIVE="${1:?release archive is required}"
HOME_ROOT="/home1/gorkhal1"
PUBLIC_ROOT="$HOME_ROOT/public_html"
LARAVEL_ROOT="$HOME_ROOT/gorkhali-laravel"
STAMP="$(date +%Y%m%d%H%M%S)"
STAGING="$HOME_ROOT/gorkhali-static-staging-$STAMP"

test -f "$ARCHIVE"
test -f "$LARAVEL_ROOT/public/index.php"
mkdir -p "$STAGING"
tar -xzf "$ARCHIVE" -C "$STAGING"
cp "$LARAVEL_ROOT/public/index.php" "$STAGING/laravel.php"
sed -i \
  "s#__DIR__.'/../vendor#__DIR__.'/../gorkhali-laravel/vendor#; s#__DIR__.'/../bootstrap#__DIR__.'/../gorkhali-laravel/bootstrap#" \
  "$STAGING/laravel.php"

mv "$PUBLIC_ROOT" "$HOME_ROOT/public_html.backup-$STAMP"
mv "$STAGING" "$PUBLIC_ROOT"
rm -f "$ARCHIVE"
```

The installer must leave the prior public root intact at `public_html.backup-$STAMP`.

- [ ] **Step 4: Add the Windows build-and-upload wrapper**

`deploy-static-php.ps1` must:

1. Run `npm run static:test`.
2. Run `npm run static:build`.
3. Copy `static-site/deploy/.htaccess` into `static-site/dist`.
4. Create `gorkhali-static-release.tgz`.
5. Upload the archive and `cpanel-install-static.sh` using the existing cPanel SSH key.
6. Invoke the server-side installer.
7. Exit non-zero when any step fails.

Use explicit paths rooted at the repository and the existing key `C:\Users\A C E R\.ssh\cpanel_gorkhali`; never print key material.

- [ ] **Step 5: Run deployment tests**

Run:

```bash
npm run static:test -- tests/static-deployment.test.ts
npm run static:build
```

Expected: deployment tests PASS and `static-site/dist/.htaccess` can be staged by the wrapper.

- [ ] **Step 6: Commit deployment tooling**

```bash
git add static-site/deploy/.htaccess scripts/deploy-static-php.ps1 scripts/cpanel-install-static.sh tests/static-deployment.test.ts
git commit -m "feat: add recoverable PHP static deployment"
```

---

### Task 7: Production deployment and verification

**Files:**
- Modify only if verification finds a reproducible defect in a file owned by Tasks 1-6.

**Interfaces:**
- Consumes: verified static release and cPanel credentials.
- Produces: a live public frontend with Laravel API and admin availability.

- [ ] **Step 1: Run the complete local verification suite**

Run:

```bash
npm run static:test
npm run static:build
```

Expected: all tests PASS and the static build exits 0.

- [ ] **Step 2: Deploy the static artifact**

Run:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\deploy-static-php.ps1
```

Expected: the installer prints the timestamped backup path and exits 0.

- [ ] **Step 3: Verify HTTP routing**

Run:

```powershell
curl.exe -sS -o NUL -w "HOME %{http_code}`n" https://gorkhalikhabar.com/
curl.exe -sS -o NUL -w "HEALTH %{http_code}`n" https://gorkhalikhabar.com/api/health
curl.exe -sS -o NUL -w "HOME_API %{http_code}`n" https://gorkhalikhabar.com/api/v1/home
curl.exe -sS -o NUL -w "ADMIN %{http_code}`n" https://gorkhalikhabar.com/gorkhali-admin
```

Expected:

```text
HOME 200
HEALTH 200
HOME_API 200
ADMIN 302
```

- [ ] **Step 4: Verify public content in Chrome**

Check:

- Homepage shows the full news layout rather than the service-status card.
- At least one featured article title comes from `/api/v1/home`.
- An article link opens its detail route and loads by slug.
- A category link opens a populated listing.
- Search returns API-backed results.
- Finance or sports loads its Laravel utility data.
- Unknown path displays the public not-found screen.
- Browser console has no blocking asset or API errors.
- Desktop and mobile navigation remain usable.

- [ ] **Step 5: Verify rollback evidence**

In cPanel, confirm a timestamped `/home1/gorkhal1/public_html.backup-*` directory exists and contains the pre-deployment public root. Do not delete it during this task.

- [ ] **Step 6: Commit verification fixes, if any**

If verification required a code fix, add only the affected files and commit:

```bash
git commit -m "fix: complete static PHP deployment verification"
```

If no files changed, record the deployed commit SHA in the completion report without creating an empty commit.

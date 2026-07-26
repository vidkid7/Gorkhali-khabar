# Public Detail and Footer Polish Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Hide privileged links from guests, make footer/legal content language-aware, verify newsletter submission, and improve article, gallery, and reel detail pages.

**Architecture:** Keep Next.js responsible for rendering and interaction while Laravel remains the source of public content and newsletter writes. Reuse existing detail components and translation context; add no new persistence layer.

**Tech Stack:** Next.js 16, React 19, TypeScript, Tailwind CSS, Laravel 13, Vitest, Playwright.

## Global Constraints

- Preserve existing public routes and Laravel API ownership.
- Do not expose admin navigation to unauthenticated visitors.
- Keep English/Nepali content selectable through the existing language context.
- Keep guest pages usable when optional APIs fail.

### Task 1: Guest visibility and footer copy

**Files:**
- Modify: `src/components/layout/Header.tsx`
- Modify: `src/components/layout/Footer.tsx`
- Modify: `src/i18n/locales/en.json`
- Modify: `src/i18n/locales/ne.json`

- [ ] Hide admin links unless `session?.user` has an allowed editorial role.
- [ ] Render footer legal labels and company credit through `useLanguage`.
- [ ] Add newsletter success/error copy in both locales.
- [ ] Add “Managed by AashaTech” as a normal external/brand credit without exposing admin controls.

### Task 2: Newsletter submission verification

**Files:**
- Modify: `src/components/layout/Footer.tsx`
- Test: `tests/footer-newsletter.test.ts`

- [ ] Add a failing test for successful and failed Laravel newsletter responses.
- [ ] Submit to `/api/v1/newsletter`, show a polite success/error message, clear only after success, and prevent duplicate submits while pending.
- [ ] Run the focused test and then the full frontend suite.

### Task 3: Article detail presentation

**Files:**
- Modify: `src/app/articles/[slug]/page.tsx`
- Modify: `src/components/articles/ArticleContent.tsx`

- [ ] Improve the article shell with a readable content measure, metadata grouping, image treatment, and related-content spacing.
- [ ] Preserve localization, sharing, comments/actions, and Laravel view tracking.
- [ ] Verify mobile and desktop screenshots and no layout overflow.

### Task 4: Gallery and reel detail presentation

**Files:**
- Modify: `src/app/galleries/[slug]/page.tsx`
- Modify: `src/app/reels/[slug]/page.tsx`
- Modify: `src/components/gallery/LightboxViewer.tsx`

- [ ] Add consistent media headers, count/date metadata, responsive media grids/stages, and clear empty states.
- [ ] Preserve existing lightbox and video behavior.
- [ ] Verify mobile and desktop interaction paths.

### Task 5: Full verification

- [ ] Run `npm test -- --run`, `npx tsc --noEmit`, and `npm run build`.
- [ ] Recreate frontend/web containers.
- [ ] Check guest/admin link visibility, newsletter response, and all three detail routes with Playwright.
- [ ] Run `git diff --check` and confirm no public page imports Prisma.

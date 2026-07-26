# Additive Editorial Homepage Design

## Goal

Extend the existing Gorkhali Khabar homepage with useful editorial patterns inspired by established Nepali news sites, while preserving every existing section, visual identity, route, and admin-managed content path.

## Scope

- Keep the current header, breaking-news ticker, hero, daily brief, category sections, trending blocks, provincial news, footer, and Laravel-driven ordering.
- Add a utility strip for date, weather/air quality, search, finance, and राशिफल links.
- Add a latest-updates rail and related-story presentation using existing article data.
- Add optional editor-picks/opinion and media strips below the current sections.
- Add reading-time and publication metadata where the existing article card supports it.
- Keep new blocks data-dependent: an empty feed hides only that block and never removes existing content.
- Seed representative menus, homepage sections, articles, breaking news, and media records so the additions are visible in local development immediately.

## Data and admin behavior

- Homepage sections remain controlled by the existing `homepage_sections` table and admin CRUD.
- New seeded sections use stable keys and sort orders after the existing latest/politics/sports sections.
- Existing rows are preserved by idempotent seed logic; no destructive cleanup or replacement is allowed.
- Existing role boundaries and audit logging remain unchanged.

## Frontend behavior

- The existing homepage remains the primary layout.
- New sections render only when their corresponding Laravel payload contains data.
- Existing fallback sections remain available if the API is unavailable.
- New menu links are read from Laravel menus and retain the existing bundled fallback.
- All new UI must pass strict TypeScript checking and existing frontend tests.

## Acceptance criteria

1. Existing homepage sections and navigation are still present.
2. Seed runs twice without duplicating or changing existing records.
3. New seeded content is visible on the local homepage.
4. Admin can reorder or deactivate the added homepage sections.
5. Backend and frontend tests pass; strict TypeScript and production build pass.
6. No existing article, category, media, or permission data is deleted.

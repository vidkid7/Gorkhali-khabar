# Static Frontend Parity Design

## Goal

Make the cPanel-deployable public frontend visually and functionally match the current Next.js public site while preserving the existing Laravel/PHP API, database, and `/gorkhali-admin` administration system.

## Hosting Constraint

The production cPanel account does not expose a Node.js application runtime. Public pages must therefore remain browser-rendered static assets in `public_html`, with Apache rewriting API and admin paths to Laravel. The Laravel application remains outside the document root and is booted by `static-site/deploy/laravel.php`.

## Architecture

The `static-site` Vite application is the production public frontend. It will be expanded to reuse the information architecture, visual tokens, responsive layout, and route coverage of the Next.js public UI under `src/app` and `src/components`.

The static frontend will continue to request live data from same-origin Laravel endpoints under `/api/v1`. It must not call Next.js API routes, use server actions, depend on NextAuth, or require a Node.js server. `/gorkhali-admin` and `/api/*` remain Apache/Laravel routes and are not captured by the SPA fallback.

## Public UI Scope

- Header, utility row, primary navigation, mobile menu, search treatment, ticker, and footer match the current Next.js public presentation.
- Homepage matches the current editorial hierarchy: hero deck, latest updates, article rails, editorial sections, media/reels, regional and utility modules, and advertisement placements where data is available.
- Article and category archive pages preserve the current detail/reading experience and responsive layout.
- Existing public routes continue working through the static router; unavailable Next-only routes show a branded, useful fallback rather than a generic error.
- Desktop, tablet, and mobile layouts use the same breakpoints and interaction expectations as the current Next.js site.

## Data and Admin Boundaries

- Laravel remains the source of truth for articles, categories, breaking news, ads, newsletters, media, and public configuration.
- The static frontend reads the Laravel API only; content updates made in `/gorkhali-admin` must appear without rebuilding frontend assets.
- Authentication and administrative workflows remain Laravel-owned and outside the static SPA.

## Deployment

1. Build `static-site` into `static-site/dist`.
2. Copy the deploy rules and Laravel front controller into the release artifact.
3. Replace only public static assets in the cPanel document root, retaining the deployed Laravel directory and `.env`.
4. Verify direct-origin homepage, API, representative article/category routes, and admin redirect before relying on public DNS.

## Verification

- Static route and API-client tests cover the expanded router and Laravel data mapping.
- The static build completes without errors.
- Visual checks compare the local static frontend against the local Next.js public routes at desktop and mobile widths.
- Direct-origin smoke checks confirm `/`, `/api/v1/home`, an article, a category, and `/gorkhali-admin` behave as expected.

## Non-Goals

- No Node.js runtime is introduced on cPanel.
- No Laravel data model, API contract, or admin authorization changes are required for frontend parity.
- No replacement of the existing domain, database, or admin route.

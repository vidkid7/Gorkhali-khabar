# PHP-Compatible Static Frontend Design

## Goal

Serve the existing Gorkhali Khabar public news interface from a PHP-only cPanel account while retaining Laravel as the live API and administration application.

## Scope

The first release covers the public site only:

- Homepage
- Article and category browsing
- Reels and galleries
- Search
- Finance, sports, calendar, rashifal, and other public utility pages
- Static informational pages
- Responsive header, footer, navigation, imagery, and public widgets

Authenticated public features are excluded:

- Reader login and registration
- Bookmarks
- Comment submission and voting
- Reader profiles

Laravel's `/gorkhali-admin` administration interface remains active and is not replaced by the static frontend.

## Architecture

The public Next.js interface becomes a static HTML, CSS, and JavaScript bundle. Components that currently query Prisma or use Next.js route handlers will instead request data from the Laravel API in the browser.

The deployed cPanel document root contains:

- Exported public frontend assets and route directories
- Laravel's public assets
- A Laravel front controller retained under a distinct PHP filename
- Apache rewrite rules that serve static routes first and send API and admin requests to Laravel

The Laravel application remains outside `public_html` at `/home1/gorkhal1/gorkhali-laravel`.

## Request Routing

Apache applies these rules in order:

1. Existing static files and directories are served directly.
2. `/api/*`, `/sanctum/*`, `/gorkhali-admin`, and `/gorkhali-admin/*` execute Laravel.
3. Exported public routes resolve to their generated `index.html` files.
4. Unknown public URLs render the exported not-found page.

The public root `/` serves the exported news homepage rather than Laravel's service-status view.

## Data Flow

Public components call same-origin Laravel endpoints:

- Homepage: `/api/v1/home`
- Articles: `/api/v1/articles` and `/api/v1/articles/slug/{slug}`
- Categories: `/api/v1/categories`
- Search: `/api/v1/search`
- Reels: `/api/v1/reels`
- Galleries: `/api/v1/galleries`
- Settings and navigation data: `/api/v1/settings`, `/api/v1/quick-links`, and `/api/v1/tags`
- Public utilities: the existing finance, sports, calendar, rashifal, Nepse, advertisement, newsletter, and trending endpoints

The HTML bundle supplies layout and loading states immediately. API responses populate current content after page load, allowing editors to publish through Laravel without rebuilding the frontend.

## Frontend Conversion

Server-only Prisma calls, Next.js request APIs, and Next.js API route dependencies are removed from exported public routes. Public page modules use focused client data hooks backed by a shared Laravel API client.

Dynamic public URLs use an exported client route shell:

- Article shell reads the article slug from the browser URL and loads the article by slug.
- Category shell reads the category slug and loads its article listing.
- Reel and gallery detail shells resolve their identifiers through Laravel.

The export excludes Next.js admin, authentication, API route-handler, RSS route-handler, and upload route-handler output. Laravel continues to own administration and API behavior.

## Error Handling

Every API-backed public page provides:

- A deterministic loading state
- A clear empty state when the API returns no content
- A retry action for network or server failures
- A not-found state for unknown public records

API error details are not printed into the public page. Laravel continues to log server-side failures.

## Deployment

The production build runs locally or on a suitable build runner, not on cPanel. Deployment uploads only the exported frontend artifact and its assets.

The deployment procedure:

1. Build and verify the static frontend.
2. Back up the current `public_html`.
3. Copy the exported bundle into `public_html`.
4. Restore the Laravel front controller and rewrite configuration.
5. Preserve Laravel `.env`, SQLite data, uploads, storage, and admin routes.
6. Clear Laravel caches only when backend files change.

Deployment never starts the cPanel Node.js application.

## Verification

Completion requires:

- Static export completes without Next.js server-runtime dependencies.
- `/` renders the full news homepage rather than the Laravel status card.
- Homepage content is returned from `/api/v1/home`.
- An article URL and category URL render from Laravel data.
- Search and at least one public utility endpoint work.
- `/api/health` returns HTTP 200.
- `/gorkhali-admin` remains reachable and redirects to its login flow.
- Unknown public routes render the public not-found page.
- Browser console contains no blocking asset or API errors.
- Desktop and mobile layouts remain usable.

## Rollback

Before replacing `public_html`, deployment creates a timestamped backup. Rollback restores that directory and leaves `/home1/gorkhal1/gorkhali-laravel` and its database unchanged.

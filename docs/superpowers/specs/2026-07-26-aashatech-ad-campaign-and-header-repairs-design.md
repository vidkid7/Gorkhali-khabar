# AashaTech Ad Campaign and Header Repairs Design

## Purpose

Add a balanced, admin-managed AashaTech advertising campaign to Gorkhali Khabar and repair three related public-header problems:

1. The breaking-news headline becomes invisible during most of its animation.
2. Public reader login and registration can be mistaken for staff access.
3. Laravel-managed navigation replaces service utilities such as Patro.

The campaign links to `https://www.aashatech.com/` and uses the existing Laravel advertisement, placement, impression, click, and admin-management system.

## Campaign Direction

The campaign presents AashaTech as Nepal's technology partner for web systems, mobile applications, custom software, AI automation, and digital delivery. The visual language will be professional and technology-focused, with clean digital-system imagery that complements the restrained editorial appearance of Gorkhali Khabar.

Generated imagery will not contain important copy. Exact copy, the AashaTech name, and the call to action will be added deterministically during asset preparation so spelling and alignment remain reliable.

Approved campaign copy:

- `Digital Systems That Transform How Organizations Work`
- `Start a Project`
- `aashatech.com`
- `विज्ञापन / Advertisement`

## Generated Assets

Three final bitmap assets will be stored under `public/images/ads/aashatech/`:

| Asset | Intended placement | Final ratio |
| --- | --- | --- |
| AashaTech leaderboard | Header, in-article, and footer | 728×90 |
| AashaTech section banner | Between homepage content groups | 970×90 |
| AashaTech sidebar creative | Article and listing sidebars | 300×250 |

Image generation supplies the campaign illustration or photographic background. Final sizing and exact typographic copy will be composed locally without stretching the generated imagery. All assets must remain legible on high-density displays and retain useful focal areas when responsive layouts reduce their displayed width.

## Advertisement Data

The existing Laravel positions remain the source of truth:

- `HEADER`
- `SIDEBAR`
- `IN_ARTICLE`
- `FOOTER`
- `BETWEEN_SECTIONS`

One active AashaTech advertisement will be seeded or upserted for each position. Each record will use:

- Target URL: `https://www.aashatech.com/`
- A position-appropriate generated image
- No expiry date
- Active status
- A stable campaign identifier so seeding is idempotent

The public components will continue using the Laravel advertisement API. Impressions are recorded after an advertisement is selected, and clicks are recorded before the external page opens. Administrators can disable, replace, schedule, or delete the campaign through the existing ad management panel.

## Placement Rules

Ad density is balanced:

- One header leaderboard below the breaking-news area and before the main editorial lead.
- One between-sections banner after every two or three homepage content groups.
- One sidebar creative on layouts that provide a real sidebar without narrowing the article body.
- One in-article leaderboard near the middle of a sufficiently long article.
- One footer leaderboard above the newsletter section.

Rules:

- Advertisements must never interrupt the headline, article metadata, gallery media, reel player, navigation, or breaking-news ticker.
- Advertisements must include a visible `विज्ञापन / Advertisement` disclosure.
- Mobile layouts render wide banners at full available width while preserving aspect ratio.
- The 300×250 creative moves into the normal document flow when a sidebar is unavailable.
- Failed ad requests or failed images collapse the placement without leaving an empty box.
- External links use a new tab with `noopener`, `noreferrer`, and `sponsored`.

## Breaking-News Repair

The current ticker moves one short item from its starting position to fully outside the viewport, leaving an empty red bar until the animation restarts.

The repaired ticker will:

- Duplicate its display sequence when required for a continuous loop.
- Size the moving track to its content.
- Use a seamless half-track translation rather than moving the entire sequence beyond view.
- Pause on pointer hover and keyboard focus.
- Render a static, fully visible headline when reduced motion is requested.
- Keep the breaking label fixed.
- Preserve working links to the associated article.

If no breaking-news records are active, the entire breaking strip remains absent.

## Reader and Staff Authentication

Public header actions will be labeled:

- `पाठक लगइन` / `Reader Login`
- `पाठक दर्ता` / `Reader Registration`

They continue using:

- `/auth/login`
- `/auth/register`
- Laravel API authentication
- Default reader role for new registrations

Staff access remains separate:

- The hidden Laravel admin login stays under the configured admin path.
- Admin pages continue enforcing `ADMIN`, `EDITOR`, or `AUTHOR` roles.
- No public admin link is restored.
- A reader account cannot enter the staff panel.

The implementation will verify public registration, reader login, staff denial for reader roles, and successful staff authentication without exposing seeded credentials.

## Navigation Repair

Laravel-managed editorial links remain authoritative for news categories and their order. Fixed service utilities are merged into the returned navigation instead of being discarded:

- Patro
- Rashifal
- Share Market
- Forex
- Photo Gallery

Desktop behavior:

- Primary editorial links remain in the main row.
- Secondary editorial links remain under `अन्य / More`.
- Service utilities appear in a compact, always-discoverable service area or within the More menu at widths where the row cannot fit them.

Mobile behavior:

- Patro remains in the bottom navigation.
- All service utilities remain available in the menu drawer.

Duplicate URLs are removed during the merge, with an administrator-managed item taking precedence over its bundled equivalent.

## Components and Boundaries

- `AdSlot` is the reusable public advertisement renderer and owns loading, disclosure, image behavior, and tracking.
- Homepage layout decides only where balanced placements appear.
- Article presentation decides whether the article is long enough for an in-article placement.
- Navigation data merges managed editorial entries with bundled service utilities.
- `BreakingNewsTicker` owns the looping and reduced-motion behavior.
- Laravel seed data owns the initial AashaTech campaign records and generated asset URLs.

No unrelated visual redesign or authentication restructuring is included.

## Error Handling

- Ad API failures and missing artwork do not block page rendering.
- Impression and click tracking failures do not block navigation.
- Navigation API failure falls back to the complete bundled navigation.
- Empty breaking-news data removes the ticker.
- Invalid or unauthorized staff access follows the existing protected-login flow.

## Verification

Automated checks will cover:

- Managed navigation retains fixed service utilities and removes duplicates.
- Breaking-news markup produces a continuous repeated track and a reduced-motion-safe presentation.
- Homepage placement cadence follows the balanced rule.
- Article ads appear only when the content qualifies.
- Seed execution creates exactly one active campaign per configured position.
- Advertisement API filtering, impression tracking, and click tracking continue to pass.
- Reader registration creates a reader account and staff routes reject it.

Live checks will cover:

- Desktop widths of 1280px and 1024px.
- Mobile widths of 390px and 375px.
- No horizontal overflow.
- Every advertisement image loads and links to AashaTech.
- Every rendered advertisement has a visible disclosure.
- Header, between-section, sidebar, in-article, and footer placements appear where their layouts support them.
- Breaking-news text remains visible throughout the loop and pauses correctly.
- Patro and the other utilities are discoverable on desktop and mobile.
- Public reader auth is visually distinct from the hidden staff login.

## Acceptance Criteria

The work is complete when:

1. Three generated AashaTech campaign assets are stored in the project.
2. The Laravel seed creates or updates an active advertisement for every configured placement.
3. Balanced placements render across the public homepage and qualifying detail pages.
4. Ads are disclosed, responsive, tracked, externally linked, and manageable by administrators.
5. The breaking-news bar never becomes an unexplained empty red strip.
6. Public authentication is clearly labeled for readers and remains role-separated from staff access.
7. Patro, Rashifal, Share Market, Forex, and Photo Gallery are available in responsive navigation.
8. Production build, frontend tests, Laravel tests, and live responsive checks pass.

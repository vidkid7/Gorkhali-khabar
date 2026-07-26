# Current-state frontend/backend contract audit

Audited from the checked-out sources on 2026-07-26. This is an inventory, not a claim that the Laravel API is deployed or contract-compatible. `rg --files src/app/api -g route.*` returned **52** Next route files.

## Next API routes (complete inventory)

Status meanings: **implemented** = Laravel has the matching operation; **partial** = endpoint exists but route shape/operation differs; **missing** = no Laravel operation; **proxy** = NextAuth-only adapter, to replace with a Laravel/Sanctum client rather than port verbatim.

| Next route file | verbs | Laravel status and target |
|---|---|---|
| `auth/[...nextauth]/route.ts` | NextAuth | proxy — replace UI session client with `/v1/auth/login`, `session`, `logout`, and Google redirect/callback |
| `v1/articles/route.ts` | GET, POST | implemented — `ArticleController@index,store` |
| `v1/articles/[id]/route.ts` | GET, PUT, DELETE | implemented — `show,update,destroy` |
| `v1/articles/[id]/view/route.ts` | POST | implemented — `recordView` |
| `v1/categories/route.ts` | GET, POST, PUT, DELETE | implemented — `CategoryController` |
| `v1/tags/route.ts` | GET | implemented — `TagController@index` |
| `v1/search/route.ts` | GET | implemented — `SearchController@index` |
| `v1/trending/route.ts` | GET | implemented — `TrendingController@index` |
| `v1/settings/route.ts` | GET | implemented — `SettingController@index` |
| `v1/quick-links/route.ts` | GET | implemented — `QuickLinkController@index` |
| `v1/comments/route.ts` | GET, POST | implemented — `CommentController@index,store` |
| `v1/comments/[id]/route.ts` | PATCH | implemented — `CommentController@update` |
| `v1/comments/[id]/vote/route.ts` | POST | implemented — `CommentVoteController@store` |
| `v1/bookmarks/route.ts` | GET, POST | implemented — `BookmarkController@index,store` |
| `v1/bookmarks/[articleId]/route.ts` | DELETE | implemented — `BookmarkController@destroy` |
| `v1/newsletter/route.ts` | POST | implemented — `NewsletterController@store` |
| `v1/media/route.ts` | GET, POST | implemented — `MediaController@index,store` |
| `v1/media/[id]/route.ts` | PUT, DELETE | implemented — `MediaController@update,destroy` |
| `v1/galleries/route.ts` | GET, POST | implemented — `GalleryController@index,store` |
| `v1/galleries/[id]/route.ts` | GET, PUT, DELETE | implemented — `GalleryController@show,update,destroy` |
| `v1/reels/route.ts` | GET, POST | implemented — `ReelController@index,store` |
| `v1/reels/[id]/route.ts` | GET, PUT, DELETE | implemented — `ReelController@show,update,destroy` |
| `v1/ads/route.ts` | GET, POST | implemented — `AdvertisementController@index,store` |
| `v1/ads/[id]/route.ts` | PUT, DELETE | implemented — `AdvertisementController@update,destroy` |
| `v1/ads/[id]/click/route.ts` | POST | implemented — `AdvertisementController@track(clicks)` |
| `v1/ads/[id]/impression/route.ts` | POST | implemented — `AdvertisementController@track(impressions)` |
| `v1/ads/positions/route.ts` | GET, POST | implemented — `AdvertisementController@positions,storePosition` |
| `v1/calendar/holidays/route.ts` | GET | implemented — `CalendarController@holidays` |
| `v1/calendar/panchang/route.ts` | GET | implemented — `CalendarController@panchang` |
| `v1/rashifal/route.ts` | GET | implemented — `RashifalController@index` |
| `v1/nepse/route.ts` | GET | implemented — `NepseController@index` |
| `v1/finance/exchange-rates/route.ts` | GET | implemented — `FinanceController@exchangeRates` |
| `v1/finance/gold-silver/route.ts` | GET | implemented — `FinanceController@goldSilver` |
| `v1/sports/tournaments/route.ts` | GET, POST | implemented — `SportsController@tournaments,storeTournament` |
| `v1/sports/matches/route.ts` | GET, POST | implemented — `SportsController@matches,storeMatch` |
| `v1/sports/matches/[id]/route.ts` | PUT | implemented — `SportsController@updateMatch` |
| `v1/auth/register/route.ts` | POST | implemented — `AuthController@register` |
| `v1/auth/forgot-password/route.ts` | POST | implemented — `AuthController@forgotPassword` |
| `v1/auth/reset-password/route.ts` | POST | implemented — `AuthController@resetPassword` |
| `v1/auth/verify-email/route.ts` | GET | partial — Laravel accepts GET **and** POST; check token/query response format |
| `v1/auth/send-verification/route.ts` | POST | implemented — `AuthController@sendVerification` |
| `v1/admin/settings/route.ts` | GET, PUT | implemented — `AdminPrimitiveController@settingsIndex,settingsUpdate` |
| `v1/admin/tags/route.ts` | POST, PUT, DELETE | implemented — `AdminPrimitiveController` |
| `v1/admin/quick-links/route.ts` | GET, POST | implemented — `AdminPrimitiveController@linksIndex,linksStore` |
| `v1/admin/quick-links/[id]/route.ts` | PATCH, DELETE | implemented — `linksUpdate,linksDestroy` |
| `v1/admin/breaking-news/route.ts` | POST | implemented — `breakingNewsStore` |
| `v1/admin/breaking-news/[id]/route.ts` | PATCH, DELETE | implemented — `breakingNewsUpdate,breakingNewsDestroy` |
| `v1/admin/users/[id]/role/route.ts` | PATCH | implemented — `userRoleUpdate` |
| `v1/admin/holidays/route.ts` | GET, POST, PUT, DELETE | implemented — `CalendarController` admin methods |
| `v1/admin/rashifal/route.ts` | GET, POST, PUT, DELETE | implemented — `RashifalController` admin methods |
| `v1/admin/forex/route.ts` | GET, POST, PUT, DELETE | implemented — `FinanceController` forex methods |
| `v1/admin/gold-silver/route.ts` | GET, POST, PUT, DELETE | implemented — `FinanceController` metals methods |

## Direct frontend backend dependencies

| dependency | direct callers/source | migration consequence |
|---|---|---|
| Prisma client/database | `src/lib/prisma.ts`; every `src/app/api/v1/**/route.ts`; `src/lib/site-config.ts`; `src/lib/public-articles.ts` | Replace route handlers with a typed Laravel client; remove server-side Prisma reads only after response-contract tests pass. |
| Prisma types | API routes plus `src/lib/auth.ts`, `src/lib/auth-helpers.ts`, `src/app/admin/articles/page.tsx` | Define shared transport DTOs; do not expose Eloquent models directly. |
| NextAuth | `src/lib/auth.ts`, `src/lib/auth-helpers.ts`, auth route, login/profile/header/sidebar/session provider, `src/proxy.ts` | Replace with Sanctum-cookie/token session client and refit browser guards. |
| storage | `src/lib/storage.ts`, media handlers | Laravel disk is `public`; retain Cloudinary/S3 decision and map upload URL/output semantics. |
| server-only services | `src/lib/redis.ts`, `email.ts`, `audit.ts`, `site-config.ts`, `public-articles.ts` | Laravel equivalents are cache/mail/audit services; confirm queues and cache deployment. |
| environment | `DATABASE_URL`, NextAuth/Auth URLs, Google IDs, Cloudinary/S3/Azure vars, Redis, SMTP, site/admin path vars | Move runtime secrets to Laravel deployment env; browser only receives explicitly public values. No secret values were inspected. |

## Laravel route implementation map

`backend/routes/api.php` contains all API registration. Read routes use `throttle:reads`; tracking uses `throttle:tracking`; writes add `auth:sanctum`, `active.session`, role middleware, and normally `throttle:writes`. Auth uses `throttle:auth`/`login`; newsletter and comments have dedicated throttles.

### Complete Laravel API route inventory

The following **99 verb/path registrations** are an explicit inventory of `backend/routes/api.php`. Middleware is listed exactly at the route-registration level; framework/global middleware is not repeated.

```text
GET /v1/status — inline ApiResponse closure — none
GET /v1/articles — ArticleController@index — throttle:reads
GET /v1/articles/slug/{slug} — ArticleController@showBySlug — throttle:reads
GET /v1/articles/{id} — ArticleController@show — throttle:reads
POST /v1/articles/{id}/view — ArticleController@recordView — throttle:tracking
POST /v1/articles — ArticleController@store — auth:sanctum, active.session, role:AUTHOR,EDITOR,ADMIN, throttle:writes
PUT /v1/articles/{id} — ArticleController@update — auth:sanctum, active.session, role:AUTHOR,EDITOR,ADMIN, throttle:writes
DELETE /v1/articles/{id} — ArticleController@destroy — auth:sanctum, active.session, role:ADMIN, throttle:writes
GET /v1/categories — CategoryController@index — throttle:reads
GET /v1/home — HomeController@index — throttle:reads
GET /v1/tags — TagController@index — throttle:reads
GET /v1/quick-links — QuickLinkController@index — throttle:reads
GET /v1/search — SearchController@index — throttle:reads
GET /v1/trending — TrendingController@index — throttle:reads
GET /v1/settings — SettingController@index — throttle:reads
GET /v1/rashifal — RashifalController@index — throttle:reads
GET /v1/calendar/holidays — CalendarController@holidays — throttle:reads
GET /v1/calendar/panchang — CalendarController@panchang — throttle:reads
GET /v1/finance/exchange-rates — FinanceController@exchangeRates — throttle:reads
GET /v1/finance/gold-silver — FinanceController@goldSilver — throttle:reads
GET /v1/sports/tournaments — SportsController@tournaments — throttle:reads
GET /v1/sports/matches — SportsController@matches — throttle:reads
GET /v1/nepse — NepseController@index — throttle:reads
POST /v1/sports/tournaments — SportsController@storeTournament — auth:sanctum, active.session, role:ADMIN, throttle:writes
POST /v1/sports/matches — SportsController@storeMatch — auth:sanctum, active.session, role:ADMIN, throttle:writes
PUT /v1/sports/matches/{id} — SportsController@updateMatch — auth:sanctum, active.session, role:ADMIN, throttle:writes
POST /v1/newsletter — NewsletterController@store — throttle:newsletter
GET /v1/media — MediaController@index — auth:sanctum, active.session, role:ADMIN,EDITOR,AUTHOR
POST /v1/media — MediaController@store — auth:sanctum, active.session, role:ADMIN,EDITOR,AUTHOR, throttle:writes
PUT /v1/media/{id} — MediaController@update — auth:sanctum, active.session, role:ADMIN,EDITOR,AUTHOR, throttle:writes
DELETE /v1/media/{id} — MediaController@destroy — auth:sanctum, active.session, role:ADMIN, throttle:writes
GET /v1/galleries — GalleryController@index — throttle:reads
GET /v1/galleries/{id} — GalleryController@show — throttle:reads
POST /v1/galleries — GalleryController@store — auth:sanctum, active.session, role:ADMIN, throttle:writes
PUT /v1/galleries/{id} — GalleryController@update — auth:sanctum, active.session, role:ADMIN, throttle:writes
DELETE /v1/galleries/{id} — GalleryController@destroy — auth:sanctum, active.session, role:ADMIN, throttle:writes
GET /v1/reels — ReelController@index — throttle:reads
GET /v1/reels/{id} — ReelController@show — throttle:reads
POST /v1/reels — ReelController@store — auth:sanctum, active.session, role:ADMIN, throttle:writes
PUT /v1/reels/{id} — ReelController@update — auth:sanctum, active.session, role:ADMIN, throttle:writes
DELETE /v1/reels/{id} — ReelController@destroy — auth:sanctum, active.session, role:ADMIN, throttle:writes
GET /v1/ads — AdvertisementController@index — throttle:reads
GET /v1/ads/positions — AdvertisementController@positions — throttle:reads
POST /v1/ads/positions — AdvertisementController@storePosition — auth:sanctum, active.session, role:ADMIN, throttle:writes
POST /v1/ads/{id}/click — AdvertisementController@track(clicks) closure — throttle:tracking
POST /v1/ads/{id}/impression — AdvertisementController@track(impressions) closure — throttle:tracking
POST /v1/ads — AdvertisementController@store — auth:sanctum, active.session, role:ADMIN, throttle:writes
PUT /v1/ads/{id} — AdvertisementController@update — auth:sanctum, active.session, role:ADMIN, throttle:writes
DELETE /v1/ads/{id} — AdvertisementController@destroy — auth:sanctum, active.session, role:ADMIN, throttle:writes
POST /v1/categories — CategoryController@store — auth:sanctum, active.session, role:ADMIN,EDITOR, throttle:writes
PUT /v1/categories — CategoryController@update — auth:sanctum, active.session, role:ADMIN,EDITOR, throttle:writes
DELETE /v1/categories — CategoryController@destroy — auth:sanctum, active.session, role:ADMIN, throttle:writes
GET /v1/comments — CommentController@index — throttle:reads
POST /v1/comments — CommentController@store — auth:sanctum, active.session, throttle:comments
PATCH /v1/comments/{id} — CommentController@update — auth:sanctum, active.session, role:ADMIN,EDITOR, throttle:writes
POST /v1/comments/{id}/vote — CommentVoteController@store — auth:sanctum, active.session, throttle:writes
GET /v1/bookmarks — BookmarkController@index — auth:sanctum, active.session
POST /v1/bookmarks — BookmarkController@store — auth:sanctum, active.session, throttle:writes
DELETE /v1/bookmarks/{articleId} — BookmarkController@destroy — auth:sanctum, active.session, throttle:writes
GET /v1/admin/rashifal — RashifalController@adminIndex — auth:sanctum, active.session, role:ADMIN,EDITOR
POST /v1/admin/rashifal — RashifalController@store — auth:sanctum, active.session, role:ADMIN,EDITOR, throttle:writes
PUT /v1/admin/rashifal — RashifalController@update — auth:sanctum, active.session, role:ADMIN,EDITOR, throttle:writes
DELETE /v1/admin/rashifal — RashifalController@destroy — auth:sanctum, active.session, role:ADMIN, throttle:writes
GET /v1/admin/holidays — CalendarController@adminIndex — auth:sanctum, active.session, role:ADMIN,EDITOR
POST /v1/admin/holidays — CalendarController@store — auth:sanctum, active.session, role:ADMIN,EDITOR, throttle:writes
PUT /v1/admin/holidays — CalendarController@update — auth:sanctum, active.session, role:ADMIN,EDITOR, throttle:writes
DELETE /v1/admin/holidays — CalendarController@destroy — auth:sanctum, active.session, role:ADMIN, throttle:writes
GET /v1/admin/forex — FinanceController@forexIndex — auth:sanctum, active.session, role:ADMIN,EDITOR
POST /v1/admin/forex — FinanceController@forexStore — auth:sanctum, active.session, role:ADMIN,EDITOR, throttle:writes
PUT /v1/admin/forex — FinanceController@forexUpdate — auth:sanctum, active.session, role:ADMIN,EDITOR, throttle:writes
DELETE /v1/admin/forex — FinanceController@forexDestroy — auth:sanctum, active.session, role:ADMIN, throttle:writes
GET /v1/admin/gold-silver — FinanceController@metalsIndex — auth:sanctum, active.session, role:ADMIN,EDITOR
POST /v1/admin/gold-silver — FinanceController@metalsStore — auth:sanctum, active.session, role:ADMIN,EDITOR, throttle:writes
PUT /v1/admin/gold-silver — FinanceController@metalsUpdate — auth:sanctum, active.session, role:ADMIN,EDITOR, throttle:writes
DELETE /v1/admin/gold-silver — FinanceController@metalsDestroy — auth:sanctum, active.session, role:ADMIN, throttle:writes
POST /v1/admin/tags — AdminPrimitiveController@tagsStore — auth:sanctum, active.session, role:ADMIN,EDITOR, throttle:writes
PUT /v1/admin/tags — AdminPrimitiveController@tagsUpdate — auth:sanctum, active.session, role:ADMIN,EDITOR, throttle:writes
DELETE /v1/admin/tags — AdminPrimitiveController@tagsDestroy — auth:sanctum, active.session, role:ADMIN,EDITOR, throttle:writes
GET /v1/admin/quick-links — AdminPrimitiveController@linksIndex — auth:sanctum, active.session, role:ADMIN,EDITOR
POST /v1/admin/quick-links — AdminPrimitiveController@linksStore — auth:sanctum, active.session, role:ADMIN, throttle:writes
PATCH /v1/admin/quick-links/{id} — AdminPrimitiveController@linksUpdate — auth:sanctum, active.session, role:ADMIN, throttle:writes
DELETE /v1/admin/quick-links/{id} — AdminPrimitiveController@linksDestroy — auth:sanctum, active.session, role:ADMIN, throttle:writes
GET /v1/admin/settings — AdminPrimitiveController@settingsIndex — auth:sanctum, active.session, role:ADMIN
PUT /v1/admin/settings — AdminPrimitiveController@settingsUpdate — auth:sanctum, active.session, role:ADMIN, throttle:writes
POST /v1/admin/breaking-news — AdminPrimitiveController@breakingNewsStore — auth:sanctum, active.session, role:ADMIN,EDITOR, throttle:writes
PATCH /v1/admin/breaking-news/{id} — AdminPrimitiveController@breakingNewsUpdate — auth:sanctum, active.session, role:ADMIN,EDITOR, throttle:writes
DELETE /v1/admin/breaking-news/{id} — AdminPrimitiveController@breakingNewsDestroy — auth:sanctum, active.session, role:ADMIN, throttle:writes
PATCH /v1/admin/users/{id}/role — AdminPrimitiveController@userRoleUpdate — auth:sanctum, active.session, role:ADMIN, throttle:writes
POST /v1/auth/register — AuthController@register — throttle:auth
POST /v1/auth/forgot-password — AuthController@forgotPassword — throttle:auth
POST /v1/auth/reset-password — AuthController@resetPassword — throttle:auth
POST /v1/auth/verify-email — AuthController@verifyEmail — throttle:auth
GET /v1/auth/verify-email — AuthController@verifyEmail — throttle:auth
POST /v1/auth/login — AuthController@login — throttle:login
GET /v1/auth/session — AuthController@session — auth:sanctum, active.session
POST /v1/auth/logout — AuthController@logout — auth:sanctum
POST /v1/auth/send-verification — AuthController@sendVerification — auth:sanctum, active.session, throttle:auth
GET /v1/auth/google/redirect — SocialAuthController@redirect — web
GET /v1/auth/google/callback — SocialAuthController@callback — web
```

| route/controller family | models | migration / tests |
|---|---|---|
| Article, home, search, trending, category, tag | `Article`, `Category`, `Tag`, `ArticleTag`, `BreakingNews`, `PageView` | `2026_07_23_000000_create_compatible_news_schema.php`; `Content/*ApiTest.php`, `ArticleApiTest.php` |
| Comment, vote, bookmark | `Comment`, `CommentVote`, `Bookmark` | compatible schema; `EngagementApiTest.php` |
| Gallery, reel, media, advertisement | `Gallery`, `GalleryImage`, `Reel`, `MediaFile`, `Advertisement`, `AdPosition` | compatible schema; `EditorialContentApiTest.php`, `Media/*ApiTest.php`, `AdvertisementApiTest.php` |
| Calendar, rashifal, finance, Nepse, sports | `Holiday`, `PanchangData`, `Rashifal`, `ForexRate`, `GoldSilverPrice`, `Tournament`, `MatchRecord` | compatible schema; `Utility/*ApiTest.php` |
| settings, links, breaking news, admin primitives | `SiteSetting`, `QuickLink`, `BreakingNews`, `User`, `AuditLog` | compatible schema; `Admin/*ApiTest.php` |
| authentication/social | `User`, `Account`, `Session`, password/verification token models | compatible schema; `Auth/*Test.php`, `MiddlewareTest.php` |

There are no `backend/app/Policies` or `backend/app/Http/Resources` directories. Authorization is route/middleware/controller based (`RequireRole`, `EnsureActiveSession`); API serialization is `App\\Support\\ApiResponse`. The migration above is the application schema migration; Laravel framework migrations create users/cache/jobs. `routes/web.php` only serves the welcome view.

## Settings source of truth

The intended mutable source is `site_settings` (`SiteSetting` / `SiteSettings`, JSON `value`), read publicly by `SettingController@index` and administered by `AdminPrimitiveController`. The old seed defines these known keys: `site_name`, `site_tagline`, `site_logo`, `site_favicon`, `primary_color`, contact/registration/social keys, `homepage_section_order`, `features_comments`, `features_bookmarks`, `features_reels`, `features_galleries`, and `copyright_text`. Frontend fallbacks live in `src/lib/site-config.ts` and UI expects those keys in `src/app/admin/settings/page.tsx`; reconcile only through the API, never duplicate settings in frontend env.

## Schema and platform gaps

| area | observed incompatibility / risk | repair |
|---|---|---|
| database engine | Prisma/Next deploy assumes `DATABASE_URL`; backend example is PostgreSQL (`DB_CONNECTION=pgsql`), despite the migration goal mentioning Laravel/MySQL | Decide engine before deployment; current migration uses PostgreSQL-oriented `timestampTz`, so MySQL needs a reviewed compatibility pass. |
| IDs and relations | Prisma client types and Next handlers rely on string IDs and Prisma nested relation/include shapes | Laravel `LegacyModel`/`UsesStringPrimaryKey` preserves IDs, but add contract tests for each nested response. |
| JSON and enum values | Prisma JSON settings/content and generated enums vs Laravel JSON casts/string columns | Verify casts and exact enum casing (`READER`, roles, statuses) against frontend DTOs. |
| timestamps | Prisma dates serialize ISO values; Laravel schema uses timezone timestamps | Standardize ISO-8601 API formatting and test it. |
| auth sessions | NextAuth cookies/tables vs Sanctum plus `active.session` and Laravel `Session` model | Browser session, CSRF, expiry, Google callback and proxy guard need an integration migration. |
| files | Next storage utility supports Cloudinary/S3 placeholders; Laravel defaults to public disk | Choose a shared object store and preserve media URLs before cutover. |

## Prioritized gaps

| priority | exact files | action |
|---|---|---|
| P0 | `src/lib/auth.ts`, `src/proxy.ts`, `src/app/api/auth/[...nextauth]/route.ts`, `backend/routes/api.php` | Implement and test a Laravel/Sanctum session client; replace NextAuth-dependent UI/guards. |
| P0 | `backend/.env.example`, `backend/config/database.php`, `backend/database/migrations/2026_07_23_000000_create_compatible_news_schema.php` | Resolve PostgreSQL-vs-MySQL target before data migration; do not run the current schema unreviewed on MySQL. |
| P1 | all `src/app/api/v1/**/route.ts`, `backend/routes/api.php` | Replace direct-Prisma route handlers with one typed Laravel client, retaining request/response fixture tests. |
| P1 | `src/lib/storage.ts`, `backend/config/filesystems.php`, `backend/app/Http/Controllers/Api/V1/MediaController.php` | Select and configure one durable media provider; test multipart/upload URL behavior. |
| P1 | `src/lib/site-config.ts`, `src/contexts/SiteConfigContext.tsx`, `backend/app/Http/Controllers/Api/V1/SettingController.php` | Make `site_settings` API authoritative and compare the complete known-key set. |
| P2 | `backend/app/Http/Middleware/RequireRole.php`, `EnsureActiveSession.php`, `backend/tests/Feature/Api/V1/*` | Add route-by-route frontend contract coverage, especially errors, pagination, and role failures. |

## Verification performed

* `rg --files src/app/api -g 'route.*'` returned 52 route files, each listed above.
* Read `backend/routes/api.php`; every registered Laravel API family is mapped above, including auth prefix routes.
* Inventoried direct backend imports/environment references with `rg`; no secret values are recorded here.

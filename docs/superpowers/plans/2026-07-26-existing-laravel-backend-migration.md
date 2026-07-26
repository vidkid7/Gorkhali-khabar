# Existing Laravel Backend Migration Plan

## Goal

Preserve the existing Next.js 16 / React 19 frontend design, content, routes, and user-facing behavior while making the existing Laravel backend the authoritative backend for production. Reuse working Laravel code, upgrade it to Laravel 13 / PHP 8.3, target MySQL, preserve application settings, and remove production dependence on Prisma, NextAuth, PostgreSQL, and Next.js backend route handlers.

## Non-negotiable constraints

- Do not redesign or visually restyle the Next.js frontend.
- Preserve all existing settings and migrate their values.
- Reuse useful existing Laravel models, controllers, migrations, services, policies, and tests.
- Laravel owns authentication, authorization, validation, database writes, media metadata, admin APIs, enquiries, analytics, and settings.
- MySQL is the production database.
- Secrets stay in environment variables and are never committed.
- Each task must add or update tests before its implementation where practical.
- Each task is independently reviewed before moving to the next task.

## Task 1: Produce a current-state backend/frontend contract audit

**Deliverables**

- Create `docs/migration/current-state-audit.md`.
- Inventory every `app/api/**/route.*` endpoint and classify it as:
  - already implemented by Laravel,
  - partially implemented by Laravel,
  - missing in Laravel,
  - frontend-only proxy that can be replaced by the Laravel client.
- Inventory direct frontend imports of Prisma, NextAuth, database helpers, server-only repositories, storage services, and backend environment variables.
- Map Laravel routes to controllers, middleware, models, migrations, policies, resources, and tests.
- Record all settings keys and their current source of truth.
- Record schema incompatibilities between Prisma/PostgreSQL assumptions and Laravel/MySQL.
- Add a prioritized gap table with exact files and recommended reuse/repair action.

**Rules**

- Read-only analysis except for the audit document.
- Do not delete, upgrade, or refactor runtime code in this task.
- Do not include secret values.

**Verification**

- All Next.js API route files appear in the audit.
- All Laravel API routes appear in the audit.
- `git diff --check` passes.

## Task 2: Upgrade and stabilize the Laravel foundation

**Deliverables**

- Upgrade the backend to Laravel 13 and PHP 8.3-compatible dependencies.
- Preserve Sanctum, Socialite, authorization, queues, mail, caching, and existing app behavior.
- Make MySQL the documented/default production database while retaining a test-friendly local configuration.
- Repair migrations so a fresh MySQL database can migrate without manual edits.
- Update backend environment examples without secrets.
- Add or update foundation smoke tests.

**Verification**

- Composer dependency validation passes.
- Backend unit and feature tests pass.
- A fresh database migration succeeds.

## Task 3: Preserve and migrate application data and settings

**Deliverables**

- Define a repeatable import path from the legacy Prisma/PostgreSQL-shaped data into Laravel/MySQL.
- Preserve stable identifiers, slugs, timestamps, publication state, user roles, settings, media references, enquiries, and analytics where available.
- Make imports idempotent and transactional where possible.
- Add validation/reporting for skipped or malformed rows.
- Add fixture-based importer tests.

**Verification**

- Re-running the importer does not create duplicates.
- Settings and representative content records match their source fixtures.

## Task 4: Add the typed Next.js-to-Laravel API boundary

**Deliverables**

- Add one shared frontend API client and server-side client for Laravel.
- Centralize base URL, credentials, CSRF, error normalization, pagination, and response typing.
- Keep the existing page/component interfaces stable so visual code does not need redesign.
- Add environment examples for browser and server access without secrets.
- Add client contract tests.

**Verification**

- The frontend build and type check pass.
- Contract tests cover success, validation, unauthenticated, forbidden, and server-error responses.

## Task 5: Move authentication and authorization to Laravel

**Deliverables**

- Replace production NextAuth/database session ownership with Laravel Sanctum.
- Preserve login, logout, registration if enabled, password reset, email verification, Google login if configured, session persistence, and role checks.
- Keep existing frontend forms and visual states.
- Enforce authorization in Laravel policies/middleware, not only in the UI.
- Add end-to-end auth and authorization tests.

**Verification**

- Guest, authenticated user, editor, and administrator boundaries are tested.
- Protected frontend routes and Laravel endpoints agree on access.

## Task 6: Migrate public content reads without changing the UI

**Deliverables**

- Connect the existing homepage, article, category, tag, search, author, page, menu, and related-content views to Laravel.
- Preserve metadata, canonical URLs, structured data, sitemap behavior, pagination, filters, and caching semantics.
- Preserve current loading, empty, and error presentation.
- Add representative page/data integration tests.

**Verification**

- Existing URLs render equivalent content from Laravel.
- Frontend snapshots or screenshots show no intentional visual changes.

## Task 7: Complete interactive and community backend features

**Deliverables**

- Move comments, reactions, bookmarks, follows, newsletter, contact/enquiry, reports, notifications, and view tracking to Laravel where those features exist in the frontend.
- Reuse existing Laravel implementations and fill only audited gaps.
- Add rate limiting, validation, anti-abuse controls, and authorization.
- Add feature tests for allowed and rejected flows.

**Verification**

- Each interactive frontend control calls Laravel and handles validation/errors.
- Abuse and permission boundaries are tested.

## Task 8: Complete admin and settings APIs

**Deliverables**

- Connect all existing admin screens to Laravel for users, roles, articles, categories, tags, pages, menus, settings, enquiries, moderation, analytics, and operational controls represented in the UI.
- Preserve every setting key and existing value.
- Add audit logging for sensitive mutations where supported by the current design.
- Add policy and validation tests.

**Verification**

- Every admin screen has a mapped Laravel endpoint.
- Settings round-trip without key/value loss.
- Unauthorized mutations fail server-side.

## Task 9: Consolidate media handling

**Deliverables**

- Use Laravel as the authority for upload authorization and media metadata.
- Preserve existing Cloudinary assets and references when configured.
- Support secure upload/delete/transform flows required by the current frontend.
- Validate file type, size, ownership, and deletion permissions.
- Add media service tests with external calls mocked.

**Verification**

- Existing images still render.
- Upload and deletion flows are permission-tested.

## Task 10: Remove legacy production backend paths

**Deliverables**

- Replace or remove migrated Next.js API handlers.
- Remove production Prisma, NextAuth, PostgreSQL, and duplicate database-access dependencies only after usage reaches zero.
- Keep only deliberate Next.js framework endpoints that are not backend business logic.
- Remove dead environment variables and update examples/documentation.
- Add a guard check preventing new direct Prisma imports in frontend runtime code.

**Verification**

- Repository search finds no unintended production imports/usages.
- Frontend build, lint, type check, and tests pass.
- Laravel tests pass.

## Task 11: Prepare cPanel deployment and operations

**Deliverables**

- Document and script the same-account deployment layout for Node.js frontend plus PHP/Laravel backend.
- Include MySQL creation/import, environment variables, storage links, permissions, queues/scheduler, cache warming, health checks, rollback, and release order.
- Ensure secrets are entered on the host and not stored in Git.
- Add health/readiness endpoints suitable for deployment verification.

**Verification**

- A clean release can be assembled from the repository.
- Health checks validate frontend, Laravel, database, cache, and storage dependencies.

## Task 12: Final regression, integration, and main-branch delivery

**Deliverables**

- Run the full frontend/backend test matrix and production builds.
- Run data migration rehearsal against sanitized fixtures.
- Verify representative public, authenticated, admin, media, enquiry, analytics, and settings journeys.
- Review the complete diff for design preservation and secret leakage.
- Merge the isolated branch into `main` and push `main` to `vidkid7/Gorkhali-khabar` only after all required checks pass.

**Verification**

- All required checks pass with recorded evidence.
- Git working tree is clean.
- Remote `main` contains the verified commit.

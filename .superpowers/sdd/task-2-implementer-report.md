# Task 2 implementer report

## Commit

Implementation commit: `008ad36471235ef42869ed54b72f39122316b27f`

## Files changed

- `.env.example`
- `README.md`
- `backend/.env.example`
- `backend/README.md`
- `backend/app/Http/Controllers/Admin/DashboardController.php`
- `backend/composer.json`
- `backend/composer.lock`
- `backend/config/database.php`
- `backend/tests/Feature/Admin/DashboardSystemHealthTest.php`
- `compose.yaml`
- `docker/php/Dockerfile`
- `scripts/verify-compose.ps1`

No migration file required a change: the existing four Laravel migrations completed
successfully on MySQL 8.4.

## Baseline and red/green evidence

### Untouched baseline

- `docker compose config --quiet` exited `0`.
- The original `docker compose build backend` failed before Composer or tests could
  run. PHP 8.4 reached the extension build, then `pdo_sqlite` configuration failed
  with `Package 'sqlite3' not found`. This established that the old
  PHP 8.4/PostgreSQL image was not runnable as checked out.

### Docker runtime red/green

- Red: the first target-image command
  `docker compose run --rm --no-deps backend php -v` failed with
  `exec /usr/local/bin/app-entrypoint: no such file or directory`.
- Fix: normalize the copied Windows entrypoint to LF in the image and make it
  executable.
- Green: the same container path then reported `PHP 8.3.32`; the Artisan version
  command reported `Laravel Framework 13.22.0`.
- Red: the first full Artisan test run completed assertions but reported 93
  identical warnings because PHPUnit 12 surfaced the missing container `.env`
  read.
- Fix: create an empty, image-local `.env` before package discovery. Runtime
  values continue to come from Compose environment variables; no secret or
  generated `.env` file is tracked.
- Green: the final full test run reported `95 passed (520 assertions)` with no
  warnings.

### Backend behavior test red/green

- Test added first:
  `backend/tests/Feature/Admin/DashboardSystemHealthTest.php`.
- Red command:
  `docker compose run --rm --no-deps backend php artisan test tests/Feature/Admin/DashboardSystemHealthTest.php`
  failed `1` test because expected `MySQL` was actual `PostgreSQL`.
- Minimal runtime fix: change the backend admin database health label to `MySQL`
  in both success and failure results.
- Green rerun: `1 passed (2 assertions)`.

### Migration portability

- `docker compose up -d --wait mysql redis` reached healthy state for both
  services.
- `docker compose run --rm backend php artisan migrate:fresh --force` completed
  all four migrations:
  - `0001_01_01_000000_create_users_table`
  - `0001_01_01_000001_create_cache_table`
  - `0001_01_01_000002_create_jobs_table`
  - `2026_07_23_000000_create_compatible_news_schema`
- Because the real MySQL fresh migration passed, no speculative migration
  rewrite was made.

## Final configuration and verification evidence

- `docker compose config --quiet` — exit `0`.
- `docker compose --progress quiet build backend` — exit `0`.
- Container PHP — `PHP 8.3.32`.
- Container framework — `Laravel Framework 13.22.0`.
- Installed database extensions — `PDO`, `pdo_mysql`, and `pdo_sqlite`.
- MySQL/Redis startup — both services healthy.
- MySQL fresh migration — `4/4` migrations passed.
- Full Docker backend suite — `95 passed (520 assertions)`, zero warnings.
- `docker compose run --rm --no-deps backend composer validate --strict` —
  `./composer.json is valid`.
- `git diff --check` — exit `0`.
- Frontend UI scope check across `src/**`, `public/**`, `static-site/**`,
  `Dockerfile.frontend`, and `next.config.ts` — `FRONTEND_UI_DIFF=NONE`.
- Targeted Docker/environment search found no `postgres`, `pgsql`,
  `POSTGRES_*`, `pdo_pgsql`, or `postgres_data` references.
- `compose.yaml` contains no frontend `DATABASE_URL`.
- `.superpowers/sdd/progress.md` was not changed.

## Dependency and configuration result

- PHP requirement: `^8.3`, with Composer resolution platform fixed at `8.3.0`
  so a newer developer machine cannot lock PHP-8.4-only transitive packages.
- Laravel framework: `v13.22.0`.
- Laravel Tinker: `v3.0.2`.
- PHPUnit: `12.5.32`.
- Symfony runtime packages resolved to PHP-8.3-compatible `7.4.x` releases.
- Docker database: MySQL `8.4`; PostgreSQL service, variables, health check,
  extension, and volume were removed.
- The frontend receives only API/site URLs, not database credentials.
- Explicit compatibility names are retained:
  `SESSION_COOKIE=gorkhali_session`,
  `CACHE_PREFIX=gorkhali-khabar-cache-`, and
  `REDIS_PREFIX=gorkhali-khabar-database-`.

## Remaining risks

- The repository still contains legacy Prisma/PostgreSQL frontend backend paths
  outside this task. Compose intentionally no longer gives the frontend a
  `DATABASE_URL`; the planned typed Laravel API boundary and removal of legacy
  Next.js database handlers remain required in later migration tasks.
- This task proves a fresh MySQL schema, not import fidelity for existing
  PostgreSQL/Prisma data. Data import and reconciliation remain a separate task.
- Existing `timestampTz` schema declarations migrate successfully on MySQL, but
  source-data timezone normalization should be checked during the import
  rehearsal.
- The committed MySQL passwords are local-development defaults only. Deployment
  must supply unique `APP_KEY`, MySQL credentials, and third-party secrets
  through the host environment.
- The obsolete PostgreSQL Docker volume may still exist on a developer machine
  from earlier runs, but it is no longer declared or attached. It was not
  deleted because that could destroy recoverable legacy data.

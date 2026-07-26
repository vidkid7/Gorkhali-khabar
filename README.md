# गोर्खाली खबर (Gorkhali Khabar)

A full-featured Nepali news portal with:

- **Public site** — Next.js 16 + Prisma (the user-facing site at <http://localhost:8080>)
- **Admin panel and API** — Laravel 13 + PHP 8.3 + MySQL (mounted at `/gorkhali-admin`, configurable)

## Features

### Public site (Next.js)
- 🌐 Bilingual (Nepali + English)
- 📰 News, categories, articles, video, photo gallery
- 📅 Nepali calendar (Patro) with holidays, rashifal, gold/silver, forex
- 🔐 NextAuth-based admin (legacy Next.js admin pages)

### Admin panel (Laravel Blade)
- 🔐 Session-based auth at `/gorkhali-admin` (configurable, masked URL)
- 👥 Roles: ADMIN / EDITOR / AUTHOR / READER
- 📰 Articles, categories, tags, comments, breaking news, web stories, panchang
- 🖼 Media library, galleries, gallery images, reels
- 🏆 Sports (tournaments, teams, matches)
- 💰 Finance (forex, gold-silver)
- 📅 Reference (rashifal, holidays, quick links)
- 📊 Analytics, bookmarks, audit log
- ⚙️ Settings, newsletter, ads, users
- 🌗 Light / dark theme with brand-matched Devanagari typography

## Project structure

```
.
├── backend/          # Laravel 13 backend (PHP 8.3, API, controllers, models, Blade views)
├── src/              # Next.js 16 public site (App Router, Prisma client)
├── prisma/           # Prisma schema + seed
├── public/           # Static assets (logos, icons, manifests)
├── tests/            # Vitest tests for the frontend
├── docker/
│   ├── nginx/        # nginx config (routes /api, /gorkhali-admin, /storage to Laravel; everything else to Next.js)
│   └── php/          # PHP-FPM 8.3 Dockerfile
├── scripts/          # Utility scripts
├── compose.yaml      # Docker stack: frontend, backend, worker, scheduler, web (nginx), MySQL, Redis, Mailpit
├── Dockerfile.frontend
├── .env / .env.example
├── .gitignore
└── README.md
```

## Quick start (Docker)

```bash
# Copy and edit env
cp .env.example .env

# Build and start the stack
docker compose up -d --build

# Wait for the MySQL healthcheck (~10s)
# Run Laravel migrations + seed (first time only)
docker compose exec backend php artisan migrate --force
docker compose exec backend php artisan db:seed --force
```

The frontend container is not given database credentials or a database URL. Browser and
server-side frontend data access must go through the Laravel API at `API_INTERNAL_URL`.

Visit:
- Public site: <http://localhost:8080/>
- API health: <http://localhost:8080/api/health>
- **Admin panel: <http://localhost:8080/gorkhali-admin/login>**
  - Default seed user: `admin@gorkhali.com` / `Admin@12345`
- NextAuth admin (legacy): <http://localhost:8080/admin> (route depends on frontend config)
- Mailpit: <http://localhost:8025>

## Customizing the admin URL

The Laravel admin is mounted at `ADMIN_PATH` (default `gorkhali-admin`). The legacy `/admin` prefix returns a 404 from nginx so the URL can't be guessed:

```bash
# .env
ADMIN_PATH=studio-control
```

Then restart `web` and `backend`:

```bash
docker compose up -d --force-recreate web backend
```

## Configuration

All runtime config is in `.env` (see `.env.example`):

| Variable | Default | Notes |
|---|---|---|
| `APP_NAME` | Gorkhali Khabar | Display name |
| `APP_URL` | `http://localhost:8080` | Used for URL generation behind nginx |
| `ADMIN_PATH` | `gorkhali-admin` | Laravel admin URL prefix |
| `MYSQL_*` | local development values | MySQL container database, user, password, and root password |
| `DB_*` | derived from `MYSQL_*` by Compose | Laravel MySQL connection |
| `REDIS_*` | — | Cache / sessions / queue |
| `SESSION_DRIVER` | redis | Session storage |
| `MAIL_*` | mailpit | Local mail catcher at <http://localhost:8025> |
| `MEDIA_STORAGE_DRIVER` | `local` | Set to `cloudinary` for Cloudinary-backed image and video uploads |
| `CLOUDINARY_*` | — | Cloud name, API key, API secret, and optional folder; keep real values out of source control |
| `NEXT_PUBLIC_SITE_URL` | `http://localhost:8080` | Public site URL (used by Next.js) |
| `API_INTERNAL_URL` | `http://web` | Backend URL (used by Next.js) |

For local development, keep `MEDIA_STORAGE_DRIVER=local`. For production media
delivery, set `MEDIA_STORAGE_DRIVER=cloudinary` and provide all four
`CLOUDINARY_*` values through the deployment environment. Existing local media
is not migrated automatically.

## Deployment

The stack is plain Docker — deploy anywhere that runs Compose (Railway, Fly, Hetzner, plain VPS).

```bash
docker compose up -d --build
docker compose exec backend php artisan migrate --force
```

## License

Private project. Not for redistribution.

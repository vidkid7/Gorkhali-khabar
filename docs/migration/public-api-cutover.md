# Public API cutover

The public Next.js pages retain their existing `/api/v1/...` URLs and response
handling. The reverse proxy now routes every `/api/v1/` request directly to the
Laravel PHP-FPM service. This makes Laravel the production owner of public
content reads without changing components, styles, layouts, copy, or routes.

The older Next.js route handlers remain in the repository only as a temporary
development fallback until the legacy-backend cleanup task removes Prisma and
NextAuth runtime paths. They are not reachable through the production nginx
configuration.

Validate the proxy configuration and Laravel route table with:

```powershell
docker compose config --quiet
docker compose run --rm --no-deps backend php artisan route:list --path=api/v1
```

After starting the full stack, verify representative public responses:

```powershell
Invoke-WebRequest http://localhost:8080/api/v1/status
Invoke-WebRequest http://localhost:8080/api/v1/settings
Invoke-WebRequest "http://localhost:8080/api/v1/articles?pageSize=8"
Invoke-WebRequest http://localhost:8080/api/v1/home
```

The API health check remains `/api/health`; Sanctum's CSRF endpoint remains
`/sanctum/csrf-cookie`.

# PostgreSQL/Prisma to Laravel/MySQL data migration

This workflow preserves the existing records and settings without committing
production data to Git. Run it first with a sanitized copy, then repeat during a
short maintenance window for the final cutover.

## Safety contract

- Keep exports outside the repository, or under the ignored
  `private-migration-data/` directory.
- Treat the export as sensitive because it contains user password hashes and
  application data.
- Active sessions and reset/verification tokens are intentionally not moved.
  Users must sign in again after cutover.
- OAuth account identity is preserved, but provider access, refresh, and ID
  tokens are cleared.
- Import into a fresh migrated MySQL database before running default seeders.
- Take a MySQL backup before importing into any non-empty environment.

## 1. Export the source PostgreSQL database

Set `DATABASE_URL` only in the shell or a private environment file. Then run:

```powershell
npm ci
$env:DATABASE_URL = "postgresql://source-connection-from-secret-store"
npm run data:export -- C:\secure-migration\legacy-export.json
Remove-Item Env:\DATABASE_URL
```

The exporter uses a fixed table allowlist, a serializable read transaction, and
writes the file with owner-only permissions where the platform supports them.

## 2. Prepare a fresh MySQL destination

For the local Docker rehearsal:

```powershell
docker compose up -d --wait mysql redis
docker compose run --rm backend php artisan migrate:fresh --force
```

For cPanel, create the MySQL database/user in cPanel, grant all privileges for
that database, configure Laravel's private `.env`, and run:

```bash
php artisan migrate --force
```

Do not run the default seeder before importing a full source export. That avoids
natural-key conflicts with source categories, settings, and quick links.

## 3. Validate with a dry run

Mount the private export read-only:

```powershell
docker compose run --rm `
  -v "C:\secure-migration:/import:ro" `
  backend php artisan legacy:import-json /import/legacy-export.json --dry-run
```

The importer rejects unknown tables and columns, validates stable keys, performs
the complete operation inside one transaction, and rolls every write back.

## 4. Import and verify

```powershell
docker compose run --rm `
  -v "C:\secure-migration:/import:ro" `
  backend php artisan legacy:import-json /import/legacy-export.json

docker compose run --rm backend php artisan app:verify-legacy-schema
```

Run the same import a second time during rehearsal. Counts must remain stable;
the importer updates rows using their source stable IDs and creates no
duplicates.

Compare at least:

- row counts per exported/imported table,
- user IDs, roles, password hashes, and active flags,
- article IDs, slugs, publication state, and timestamps,
- all `site_settings` keys and JSON values,
- media URLs and metadata,
- page-view/analytics counts,
- categories, tags, menus/quick links, advertisements, and utility data.

## 5. Final cutover and rollback

1. Put the old site into maintenance/read-only mode.
2. Create the final PostgreSQL export.
3. Back up the current MySQL destination.
4. Run the dry run, then the committed import.
5. Verify counts/settings and representative Laravel API responses.
6. Start queues/scheduler and switch traffic only after verification.

If verification fails, keep traffic on the old site, restore the MySQL backup,
correct the sanitized rehearsal, and repeat. Never edit the production export
by hand.

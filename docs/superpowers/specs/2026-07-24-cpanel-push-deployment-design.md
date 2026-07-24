# Gorkhali Khabar cPanel Push Deployment

## Goal

Deploy every push to `main` automatically to the authenticated cPanel account for
`gorkhalikhabar.com`, while keeping production-only secrets outside Git.

## Architecture

1. cPanel Git Version Control hosts an empty, cPanel-managed repository.
2. GitHub Actions runs on every push to `main` and pushes that commit to the
   cPanel-managed repository over SSH.
3. cPanel's managed-repository `post-receive` hook runs the checked-in
   `.cpanel.yml` tasks automatically.
4. The deployment tasks synchronize source into the Node application root,
   preserve the server-only `.env`, install dependencies, generate Prisma,
   build Next.js, and restart Passenger only after a successful build.
5. cPanel Setup Node.js App runs the site with Node 22.23 in production mode.

The existing PHP deployment remains available until the new Node application
passes smoke checks. The deployment target never copies `.env` from Git and
never stores credentials in the repository.

## Data Flow

```text
git push origin main
  -> GitHub Actions checkout
  -> SSH push to cPanel managed repository
  -> cPanel post-receive hook
  -> .cpanel.yml
  -> sync/build/restart in /home1/gorkhal1/gorkhali-khabar
  -> gorkhalikhabar.com
```

## Credentials

- GitHub Actions stores a dedicated private deployment key as a repository
  secret.
- cPanel stores only the matching public key in the account's authorized SSH
  keys.
- The cPanel repository URL, SSH username, host, and port are GitHub Actions
  secrets.
- Application environment variables remain in the server-side `.env`.

## Failure Handling

- The deployment uses a single-run lock to prevent concurrent builds.
- Git synchronization is fast-forward-only.
- A failed dependency install, Prisma generation, or production build stops the
  deployment before the Passenger restart marker is touched.
- Deployment output is retained in cPanel/GitHub Actions logs for diagnosis.
- The previous running process remains available when the new build fails.

## Acceptance Checks

- A cPanel-managed repository exists and has the deployment hook enabled.
- A push to `main` starts the GitHub Actions deployment job.
- The cPanel repository receives the same commit SHA.
- `.cpanel.yml` runs successfully and records a deployed SHA.
- The Node application is registered with Node 22.23, production mode, domain
  `gorkhalikhabar.com`, and startup file `server.js`.
- `https://gorkhalikhabar.com/` returns the new site.
- A representative public API route and authentication page respond without a
  5xx error.
- A second no-op push does not trigger an unnecessary rebuild.

# Gorkhali Khabar (गोर्खाली खबर)

A full-featured Nepali news portal built with Next.js 16, Prisma, and PostgreSQL.

## Features
- 🌐 Bilingual (Nepali + English)
- 📰 Full news portal with categories, articles, video, photo gallery
- 📅 Nepali calendar (Patro) with holidays, rashifal, gold/silver, forex, date converter
- 🔐 Admin panel with role-based access (Admin / Editor / Author)
- 🎨 Glassmorphic liquid design system
- ⚡ Next.js 16 with Turbopack

## Deploy on Railway

### 1. Fork / Clone this repo

### 2. Create a new Railway project
- Add a **PostgreSQL** service (Railway auto-injects `DATABASE_URL`)
- Add a **Web Service** pointing to this repo

### 3. Set environment variables in Railway
Copy from `.env.example` and fill in:
```
DATABASE_URL         # auto-injected by Railway PostgreSQL service
NEXTAUTH_URL         # https://your-app.railway.app
NEXTAUTH_SECRET      # run: node -e "console.log(require('crypto').randomBytes(32).toString('hex'))"
AUTH_SECRET          # same command, different value
NEXT_PUBLIC_SITE_URL # https://your-app.railway.app
```

### 4. Railway will auto-run on push:
```
npm ci && npx prisma generate && npm run build
npx prisma db push && npm start
```

### 5. Seed the database (first deploy only)
In Railway shell or locally with prod `DATABASE_URL`:
```bash
npx prisma db seed
```

## Local Development

```bash
cp .env.example .env
# Fill in .env values

npm install
npx prisma db push
npx prisma db seed
npm run dev
```

Open [http://localhost:3000](http://localhost:3000)

**Default credentials (after seeding):**
- Admin: `admin@newsportal.com` / `Admin@12345`
- Editor: `editor@newsportal.com` / `Editor@12345`
- Author: `author@newsportal.com` / `Author@12345`

## Admin Panel

Visit `/admin` — requires ADMIN or EDITOR role.


This project uses [`next/font`](https://nextjs.org/docs/app/building-your-application/optimizing/fonts) to automatically optimize and load [Geist](https://vercel.com/font), a new font family for Vercel.

## Learn More

To learn more about Next.js, take a look at the following resources:

- [Next.js Documentation](https://nextjs.org/docs) - learn about Next.js features and API.
- [Learn Next.js](https://nextjs.org/learn) - an interactive Next.js tutorial.

You can check out [the Next.js GitHub repository](https://github.com/vercel/next.js) - your feedback and contributions are welcome!

## Deploy on Vercel

The easiest way to deploy your Next.js app is to use the [Vercel Platform](https://vercel.com/new?utm_medium=default-template&filter=next.js&utm_source=create-next-app&utm_campaign=create-next-app-readme) from the creators of Next.js.

Check out our [Next.js deployment documentation](https://nextjs.org/docs/app/building-your-application/deploying) for more details.

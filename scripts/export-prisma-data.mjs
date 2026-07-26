import { chmod, mkdir, writeFile } from "node:fs/promises";
import { dirname, resolve } from "node:path";
import process from "node:process";
import { Prisma, PrismaClient } from "@prisma/client";

const FORMAT = "gorkhali-legacy-export-v1";

// Fixed identifiers only. Never accept table names from command-line input.
const TABLES = [
  "users",
  "accounts",
  "categories",
  "tags",
  "articles",
  "article_tags",
  "comments",
  "comment_votes",
  "bookmarks",
  "media_files",
  "tournaments",
  "teams",
  "matches",
  "reels",
  "galleries",
  "gallery_images",
  "ad_positions",
  "advertisements",
  "breaking_news",
  "web_stories",
  "page_views",
  "site_settings",
  "newsletter_subscriptions",
  "quick_links",
  "audit_logs",
  "holidays",
  "panchang_data",
  "gold_silver_prices",
  "forex_rates",
  "rashifal",
];

const outputArgument = process.argv[2];

if (!outputArgument) {
  console.error(
    "Usage: npm run data:export -- <private-output-path>/legacy-export.json",
  );
  process.exit(2);
}

if (!process.env.DATABASE_URL) {
  console.error("DATABASE_URL must point to the source PostgreSQL database.");
  process.exit(2);
}

const outputPath = resolve(outputArgument);
const prisma = new PrismaClient();

try {
  const tables = await prisma.$transaction(
    async (transaction) => {
      const exported = {};

      for (const table of TABLES) {
        const rows = await transaction.$queryRawUnsafe(
          `SELECT * FROM "${table}" ORDER BY 1`,
        );

        if (table === "accounts") {
          for (const row of rows) {
            row.refresh_token = null;
            row.access_token = null;
            row.id_token = null;
          }
        }

        exported[table] = rows;
      }

      return exported;
    },
    {
      isolationLevel: Prisma.TransactionIsolationLevel.Serializable,
      maxWait: 10_000,
      timeout: 300_000,
    },
  );

  const payload = {
    format: FORMAT,
    exported_at: new Date().toISOString(),
    tables,
  };

  await mkdir(dirname(outputPath), { recursive: true });
  await writeFile(
    outputPath,
    `${JSON.stringify(payload, null, 2)}\n`,
    { encoding: "utf8", mode: 0o600 },
  );
  await chmod(outputPath, 0o600);

  const total = Object.values(tables).reduce(
    (count, rows) => count + rows.length,
    0,
  );
  console.log(`Exported ${total} rows from ${TABLES.length} tables.`);
  console.log(`Private export written to ${outputPath}`);
  console.log(
    "Active sessions and reset/verification tokens were not exported; OAuth tokens were cleared.",
  );
} finally {
  await prisma.$disconnect();
}

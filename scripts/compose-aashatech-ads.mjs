import fs from "node:fs/promises";
import path from "node:path";
import sharp from "sharp";

const [leaderboardSource, sectionSource, sidebarSource] = process.argv.slice(2);

if (!leaderboardSource || !sectionSource || !sidebarSource) {
  throw new Error(
    "Usage: node scripts/compose-aashatech-ads.mjs <leaderboard-source> <section-source> <sidebar-source>",
  );
}

const outputDirectory = path.resolve("public/images/ads/aashatech");
await fs.mkdir(outputDirectory, { recursive: true });

function wideOverlay(width) {
  const buttonX = width - 160;
  return Buffer.from(`
    <svg width="${width}" height="90" viewBox="0 0 ${width} 90" xmlns="http://www.w3.org/2000/svg">
      <defs>
        <linearGradient id="shade" x1="0" y1="0" x2="1" y2="0">
          <stop offset="0" stop-color="#020b1f" stop-opacity="0.96"/>
          <stop offset="0.52" stop-color="#020b1f" stop-opacity="0.70"/>
          <stop offset="0.78" stop-color="#020b1f" stop-opacity="0.12"/>
          <stop offset="1" stop-color="#020b1f" stop-opacity="0"/>
        </linearGradient>
      </defs>
      <rect width="${width}" height="90" fill="url(#shade)"/>
      <text x="22" y="28" font-family="Arial, Helvetica, sans-serif" font-size="24" font-weight="800">
        <tspan fill="#ffffff">Aasha</tspan><tspan fill="#ef233c">Tech</tspan>
      </text>
      <text x="22" y="48" fill="#dce8ff" font-family="Arial, Helvetica, sans-serif" font-size="11.5" font-weight="600">
        Digital Systems That Transform How Organizations Work
      </text>
      <text x="22" y="69" fill="#8fb8ff" font-family="Arial, Helvetica, sans-serif" font-size="10" font-weight="700">
        aashatech.com
      </text>
      <rect x="${buttonX}" y="53" width="138" height="25" rx="4" fill="#e00019"/>
      <text x="${buttonX + 69}" y="70" text-anchor="middle" fill="#ffffff" font-family="Arial, Helvetica, sans-serif" font-size="10.5" font-weight="800">
        START A PROJECT →
      </text>
    </svg>
  `);
}

const sidebarOverlay = Buffer.from(`
  <svg width="300" height="250" viewBox="0 0 300 250" xmlns="http://www.w3.org/2000/svg">
    <defs>
      <linearGradient id="shade" x1="0" y1="0" x2="1" y2="0">
        <stop offset="0" stop-color="#020b1f" stop-opacity="0.98"/>
        <stop offset="0.58" stop-color="#020b1f" stop-opacity="0.78"/>
        <stop offset="1" stop-color="#020b1f" stop-opacity="0.18"/>
      </linearGradient>
      <linearGradient id="bottom" x1="0" y1="0" x2="0" y2="1">
        <stop offset="0.45" stop-color="#020b1f" stop-opacity="0"/>
        <stop offset="1" stop-color="#020b1f" stop-opacity="0.92"/>
      </linearGradient>
    </defs>
    <rect width="300" height="250" fill="url(#shade)"/>
    <rect width="300" height="250" fill="url(#bottom)"/>
    <text x="20" y="38" font-family="Arial, Helvetica, sans-serif" font-size="28" font-weight="800">
      <tspan fill="#ffffff">Aasha</tspan><tspan fill="#ef233c">Tech</tspan>
    </text>
    <text x="20" y="67" fill="#ffffff" font-family="Arial, Helvetica, sans-serif" font-size="13" font-weight="700">
      <tspan x="20" dy="0">Digital systems built</tspan>
      <tspan x="20" dy="18">around how you work.</tspan>
    </text>
    <text x="20" y="112" fill="#a9c9ff" font-family="Arial, Helvetica, sans-serif" font-size="10.5" font-weight="600">
      WEB · MOBILE · SOFTWARE · AI
    </text>
    <rect x="20" y="190" width="142" height="30" rx="5" fill="#e00019"/>
    <text x="91" y="210" text-anchor="middle" fill="#ffffff" font-family="Arial, Helvetica, sans-serif" font-size="11" font-weight="800">
      START A PROJECT →
    </text>
    <text x="20" y="238" fill="#b8cff5" font-family="Arial, Helvetica, sans-serif" font-size="10" font-weight="700">
      aashatech.com
    </text>
  </svg>
`);

async function compose(source, filename, width, height, overlay) {
  await sharp(source)
    .resize(width, height, { fit: "cover", position: "attention" })
    .composite([{ input: overlay }])
    .webp({ quality: 90, smartSubsample: true })
    .toFile(path.join(outputDirectory, filename));
}

await Promise.all([
  compose(
    leaderboardSource,
    "aashatech-leaderboard.webp",
    728,
    90,
    wideOverlay(728),
  ),
  compose(
    sectionSource,
    "aashatech-section-banner.webp",
    970,
    90,
    wideOverlay(970),
  ),
  compose(
    sidebarSource,
    "aashatech-sidebar.webp",
    300,
    250,
    sidebarOverlay,
  ),
]);

console.log(outputDirectory);

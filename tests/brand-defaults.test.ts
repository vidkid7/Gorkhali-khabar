import { readFileSync } from "node:fs";
import { resolve } from "node:path";
import { describe, expect, it } from "vitest";
import { defaultSiteConfig } from "@/lib/site-config-defaults";

describe("Gorkhali Khabar brand defaults", () => {
  it("uses the approved publication identity and wordmark", () => {
    expect(defaultSiteConfig.site_name).toEqual({ ne: "गोर्खाली खबर", en: "Gorkhali Khabar" });
    expect(defaultSiteConfig.site_logo).toBe("/icons/logo.png");
    expect(defaultSiteConfig.primary_color).toBe("#07579B");
    expect(defaultSiteConfig.copyright_text.en).toContain("Gorkhali Khabar");
  });

  it("contains no old publication identity in reader-facing brand files", () => {
    const files = [
      "src/app/layout.tsx",
      "src/app/page.tsx",
      "src/components/ui/LoadingScreen.tsx",
      "src/app/articles/[slug]/page.tsx",
      "src/app/categories/[slug]/page.tsx",
      "src/app/about/page.tsx",
      "src/app/privacy-policy/page.tsx",
      "src/app/terms-of-service/page.tsx",
      "src/app/cookie-policy/page.tsx",
    ];
    const stale = /Namaste\s*Express|NamasteXpress|नमस्ते एक्सप्रेस/i;
    for (const file of files) {
      expect(readFileSync(resolve(file), "utf8"), file).not.toMatch(stale);
    }
  });

  it("uses the approved identity in the web manifest", () => {
    const manifest = JSON.parse(readFileSync(resolve("public/manifest.json"), "utf8"));
    expect(manifest.name).toBe("Gorkhali Khabar");
    expect(manifest.short_name).toBe("Gorkhali");
    expect(manifest.theme_color).toBe("#07579B");
  });
});
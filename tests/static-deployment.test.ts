import { readFileSync } from "node:fs";
import { describe, expect, it } from "vitest";

const rules = readFileSync("static-site/deploy/.htaccess", "utf8");

describe("static PHP deployment rules", () => {
  it("routes API and admin requests to Laravel", () => {
    expect(rules).toContain("RewriteRule ^api(?:/|$) laravel.php [L,QSA]");
    expect(rules).toContain(
      "RewriteRule ^gorkhali-admin(?:/|$) laravel.php [L,QSA]",
    );
  });

  it("serves static files before the SPA fallback", () => {
    expect(rules.indexOf("RewriteCond %{REQUEST_FILENAME} -f")).toBeLessThan(
      rules.indexOf("RewriteRule ^ index.html [L]"),
    );
  });
});

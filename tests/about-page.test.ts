import fs from "node:fs";
import path from "node:path";
import { describe, expect, it } from "vitest";

const source = fs.readFileSync(
  path.join(process.cwd(), "src/app/about/page.tsx"),
  "utf8",
);

describe("About page", () => {
  it("provides isolated Nepali and English editorial experiences", () => {
    expect(source).toContain('className="language-ne');
    expect(source).toContain('className="language-en');
    expect(source).toContain('data-testid="about-values"');
    expect(source).toContain('data-testid="about-standards"');
    expect(source).toContain('data-testid="about-contact"');
    expect(source).toContain('title="About Us"');
    expect(source).toContain('label: "Home"');
  });

  it("uses configured contact details and keeps the shared shell", () => {
    expect(source).toContain("<Header />");
    expect(source).toContain("<Footer />");
    expect(source).toContain("config.contact_email");
    expect(source).toContain("config.contact_phone");
  });
});

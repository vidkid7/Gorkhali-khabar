import { readFileSync } from "node:fs";
import { describe, expect, it } from "vitest";

const seeder = readFileSync(
  "backend/database/seeders/LegacyCompatibleSeeder.php",
  "utf8",
);

describe("Laravel compatibility seeder", () => {
  it("does not force a string id into an existing integer users table", () => {
    expect(seeder).toContain("Schema::getColumnType('users', 'id')");
    expect(seeder).toContain("if ($userIdType !== 'integer'");
    expect(seeder).toContain("unset($admin['id'])");
  });
});

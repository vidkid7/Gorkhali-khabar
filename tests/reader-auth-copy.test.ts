import { describe, expect, it } from "vitest";
import ne from "@/i18n/locales/ne.json";
import en from "@/i18n/locales/en.json";

describe("public reader authentication copy", () => {
  it("distinguishes public reader actions from staff authentication", () => {
    expect(ne.common.readerLogin).toBe("पाठक लगइन");
    expect(ne.common.readerRegister).toBe("पाठक दर्ता");
    expect(en.common.readerLogin).toBe("Reader Login");
    expect(en.common.readerRegister).toBe("Reader Registration");
  });
});

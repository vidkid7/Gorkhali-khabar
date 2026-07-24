import { describe, expect, it } from "vitest";
import { MOBILE_NAV_ITEMS, PUBLIC_NAV_ITEMS } from "@/components/layout/navigation-data";
import en from "@/i18n/locales/en.json";
import ne from "@/i18n/locales/ne.json";

describe("public navigation", () => {
  it("keeps primary categories unique", () => {
    expect(new Set(PUBLIC_NAV_ITEMS.map((item) => item.href)).size).toBe(PUBLIC_NAV_ITEMS.length);
  });

  it("uses the approved five mobile destinations", () => {
    expect(MOBILE_NAV_ITEMS.map((item) => item.key)).toEqual([
      "home",
      "latest",
      "search",
      "patro",
      "profile",
    ]);
  });

  it("translates every mobile destination", () => {
    for (const item of MOBILE_NAV_ITEMS) {
      expect(en.nav, `en.nav.${item.key}`).toHaveProperty(item.key);
      expect(ne.nav, `ne.nav.${item.key}`).toHaveProperty(item.key);
    }
  });

  it("provides translated shell controls and drawer groups", () => {
    for (const key of ["services", "topics", "account"] as const) {
      expect(en.nav, `en.nav.${key}`).toHaveProperty(key);
      expect(ne.nav, `ne.nav.${key}`).toHaveProperty(key);
    }

    for (const key of ["menu", "close", "searchNews"] as const) {
      expect(en.common, `en.common.${key}`).toHaveProperty(key);
      expect(ne.common, `ne.common.${key}`).toHaveProperty(key);
    }
  });
});

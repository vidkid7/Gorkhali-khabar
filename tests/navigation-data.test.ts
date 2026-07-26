import { describe, expect, it, vi } from "vitest";
import { getPublicNavItems } from "@/components/layout/navigation-data";

describe("getPublicNavItems", () => {
  it("maps active Laravel menus into header navigation groups", async () => {
    const get = vi.fn().mockResolvedValue([
      { id: "home", location: "header", label: "गृहपृष्ठ", label_en: "Home", href: "/", sort_order: 0 },
      { id: "world", location: "mobile", label: "विश्व", label_en: "World", href: "/categories/world", sort_order: 1 },
      { id: "footer", location: "footer", label: "About", label_en: "About", href: "/page/about", sort_order: 2 },
    ]);

    const items = await getPublicNavItems({ get });

    expect(get).toHaveBeenCalledWith("/api/v1/menus?location=header");
    expect(items).toEqual([
      expect.objectContaining({ key: "home", group: "primary", label: "गृहपृष्ठ" }),
      expect.objectContaining({ key: "patro", group: "service", href: "/patro" }),
      expect.objectContaining({ key: "horoscope", group: "service", href: "/rashifal" }),
      expect.objectContaining({ key: "shareMarket", group: "service", href: "/share-market" }),
      expect.objectContaining({ key: "forex", group: "service", href: "/patro/forex" }),
      expect.objectContaining({ key: "photoGallery", group: "service", href: "/galleries" }),
    ]);
  });

  it("prefers a managed item when its URL duplicates a fixed service", async () => {
    const get = vi.fn().mockResolvedValue([
      { id: "managed-patro", location: "header", label: "सेवा पात्रो", label_en: "Service Calendar", href: "/patro", sort_order: 0 },
    ]);

    const items = await getPublicNavItems({ get });

    expect(items.filter((item) => item.href === "/patro")).toHaveLength(1);
    expect(items.find((item) => item.href === "/patro")).toEqual(
      expect.objectContaining({ key: "managed-patro", label: "सेवा पात्रो" }),
    );
  });

  it("requests and maps footer menus when a location is supplied", async () => {
    const get = vi.fn().mockResolvedValue([
      { id: "about", location: "footer", label: "हाम्रोबारे", label_en: "About", href: "/page/about", sort_order: 1 },
      { id: "header", location: "header", label: "गृहपृष्ठ", label_en: "Home", href: "/", sort_order: 2 },
    ]);

    const items = await getPublicNavItems({ get }, "footer");

    expect(get).toHaveBeenCalledWith("/api/v1/menus?location=footer");
    expect(items).toEqual([
      expect.objectContaining({ key: "about", label_en: "About", href: "/page/about" }),
    ]);
  });

  it("keeps editorial verticals in the More group", async () => {
    const items = await getPublicNavItems({
      get: vi.fn().mockResolvedValue([
        { id: "investigations", location: "header", label: "अनुसन्धान", label_en: "Investigations", href: "/categories/anveshan", sort_order: 160 },
      ]),
    });

    expect(items.find((item) => item.href === "/categories/anveshan")).toEqual(
      expect.objectContaining({ group: "secondary", label_en: "Investigations" }),
    );
  });
});

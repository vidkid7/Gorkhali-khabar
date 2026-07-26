import { describe, expect, it, vi } from "vitest";
import { getHomePayload, getHomepageSections } from "@/lib/home-api";

describe("getHomePayload", () => {
  it("loads homepage data from Laravel instead of Prisma", async () => {
    const get = vi.fn().mockResolvedValue({
      featured: [],
      categoryGroups: {},
      trending: [],
      mostCommented: [],
      reels: [],
      matches: [],
      olderArticles: [],
      editorPicks: [],
      provinceGroups: {},
      breakingNews: [],
    });

    const payload = await getHomePayload({ get });

    expect(get).toHaveBeenCalledWith("/api/v1/home");
    expect(payload.featured).toEqual([]);
  });

  it("loads the admin-managed homepage section order from Laravel", async () => {
    const get = vi.fn().mockResolvedValue([
      {
        id: "section-1",
        section_key: "politics",
        title: "राजनीति",
        category_slug: "rajniti",
        layout: "featured",
        sort_order: 1,
        is_active: true,
      },
    ]);

    const sections = await getHomepageSections({ get });

    expect(get).toHaveBeenCalledWith("/api/v1/homepage-sections");
    expect(sections[0].category_slug).toBe("rajniti");
  });
});

import { render, screen } from "@testing-library/react";
import { beforeEach, describe, expect, it, vi } from "vitest";
import { apiGet } from "../api/client";
import * as resourceHook from "../hooks/useApiResource";
import { HomePage } from "./HomePage";

vi.mock("../api/client", () => ({ apiGet: vi.fn() }));

const article = {
  id: "a1",
  slug: "मुख्य-कथा",
  title: "आजको प्रमुख समाचार",
  title_en: null,
  excerpt: "संक्षिप्त विवरण",
  excerpt_en: null,
  featured_image: null,
  reading_time: 3,
  published_at: "2026-07-24T10:00:00Z",
  view_count: 12,
  comment_count: 2,
  category: { id: "c1", slug: "samachar", name: "समाचार", name_en: null, color: null },
};

describe("HomePage", () => {
  beforeEach(() => {
    vi.restoreAllMocks();
    vi.mocked(apiGet).mockReset();
  });

  it("renders live breaking and featured content", async () => {
    vi.mocked(apiGet).mockResolvedValue({
      breakingNews: [{ id: "b1", title: "ब्रेकिङ समाचार", title_en: null, article: { slug: article.slug } }],
      featured: [article],
      categoryGroups: { samachar: [article] },
      trending: [],
      mostCommented: [],
      reels: [],
      matches: [],
      olderArticles: [],
      editorPicks: [],
      provinceGroups: {},
    });

    render(<HomePage />);
    expect(screen.getByRole("status")).toBeInTheDocument();
    expect(await screen.findByText("ब्रेकिङ समाचार")).toBeInTheDocument();
    expect(screen.getAllByText("आजको प्रमुख समाचार").length).toBeGreaterThan(0);
    expect(screen.getAllByRole("link", { name: /आजको प्रमुख समाचार/ })[0])
      .toHaveAttribute("href", `/articles/${article.slug}`);
  });

  it("offers retry after a failed request", async () => {
    const retry = vi.fn();
    vi.spyOn(resourceHook, "useApiResource").mockReturnValue({
      data: null,
      loading: false,
      error: new Error("offline"),
      retry,
    });
    render(<HomePage />);
    expect(
      await screen.findByRole("button", { name: "पुनः प्रयास गर्नुहोस्" }),
    ).toBeInTheDocument();
  });
});

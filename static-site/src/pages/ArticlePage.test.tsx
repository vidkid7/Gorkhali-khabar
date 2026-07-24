import { render, screen } from "@testing-library/react";
import { describe, expect, it, vi } from "vitest";
import { apiGet } from "../api/client";
import { ArticlePage } from "./ArticlePage";

vi.mock("../api/client", async (importOriginal) => {
  const original = await importOriginal<typeof import("../api/client")>();
  return { ...original, apiGet: vi.fn() };
});

describe("ArticlePage", () => {
  it("renders a published article response", async () => {
    vi.mocked(apiGet).mockResolvedValue({
      id: "1",
      slug: "example-story",
      title: "उदाहरण समाचार",
      title_en: null,
      excerpt: "विवरण",
      excerpt_en: null,
      content: "<p>विश्वसनीय सामग्री</p>",
      featured_image: "/story.jpg",
      reading_time: 4,
      published_at: "2026-07-24T10:00:00Z",
      view_count: 10,
      comment_count: 0,
      category: { id: "c1", slug: "samachar", name: "समाचार", name_en: null, color: null },
      author: { name: "सम्पादक" },
    });
    render(<ArticlePage slug="example-story" />);
    expect(await screen.findByRole("heading", { name: "उदाहरण समाचार" })).toBeInTheDocument();
    expect(screen.getByText("विश्वसनीय सामग्री")).toBeInTheDocument();
    expect(apiGet).toHaveBeenCalledWith("/api/v1/articles/slug/example-story");
  });
});

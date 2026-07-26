import { render, screen } from "@testing-library/react";
import { describe, expect, it } from "vitest";
import { CategoryDiscovery } from "@/components/categories/CategoryDiscovery";
import type { HomeArticle } from "@/lib/home-api";

const articles: HomeArticle[] = [
  {
    id: "article-1",
    slug: "sample-one",
    title: "नमूना पहिलो समाचार",
    category: { name: "समाचार", slug: "samachar", color: "#07579B" },
    author: { name: "Gorkhali Admin" },
  },
  {
    id: "article-2",
    slug: "sample-two",
    title: "नमूना दोस्रो समाचार",
    category: { name: "समाचार", slug: "samachar", color: "#07579B" },
    author: { name: "Gorkhali Admin" },
  },
  {
    id: "article-3",
    slug: "sample-three",
    title: "नमूना तेस्रो समाचार",
    category: { name: "समाचार", slug: "samachar", color: "#07579B" },
    author: { name: "Gorkhali Admin" },
  },
];

describe("CategoryDiscovery", () => {
  it("shows a topic strip and a three-story most-read rail", () => {
    render(<CategoryDiscovery articles={articles} activeSlug="samachar" accentColor="#07579B" />);

    expect(screen.getByTestId("category-topic-strip")).toBeVisible();
    expect(screen.getByText("धेरै पढिएको")).toBeVisible();
    expect(screen.getAllByTestId("most-read-item")).toHaveLength(3);
    expect(screen.getByRole("link", { name: "राजनीति" })).toHaveAttribute("href", "/categories/rajniti");
    expect(screen.getByRole("link", { name: "नमूना पहिलो समाचार" })).toHaveAttribute("href", "/articles/sample-one");
  });
});

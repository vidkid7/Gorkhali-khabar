import { render, screen } from "@testing-library/react";
import { describe, expect, it, vi } from "vitest";
import { apiGet } from "../api/client";
import { CategoryPage } from "./CategoryPage";

vi.mock("../api/client", async (importOriginal) => {
  const original = await importOriginal<typeof import("../api/client")>();
  return { ...original, apiGet: vi.fn() };
});

describe("CategoryPage", () => {
  it("resolves a slug and requests its category id", async () => {
    vi.mocked(apiGet).mockImplementation(async (path) => {
      if (path === "/api/v1/categories") {
        return [{ id: "cat-7", slug: "samachar", name: "समाचार", name_en: null, color: null }];
      }
      return { data: [], page: 1, totalPages: 1, total: 0 };
    });
    render(<CategoryPage slug="samachar" />);
    expect(await screen.findByRole("heading", { name: "समाचार" })).toBeInTheDocument();
    expect(apiGet).toHaveBeenCalledWith(
      "/api/v1/articles?category=cat-7&page=1&pageSize=12",
    );
  });
});

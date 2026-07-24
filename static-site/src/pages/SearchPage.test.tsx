import { render, screen } from "@testing-library/react";
import { beforeEach, describe, expect, it, vi } from "vitest";
import { apiGet } from "../api/client";
import { SearchPage } from "./SearchPage";

vi.mock("../api/client", () => ({ apiGet: vi.fn() }));

describe("SearchPage", () => {
  beforeEach(() => window.history.replaceState({}, "", "/search?q=नेपाल"));

  it("requests and labels API-backed results", async () => {
    vi.mocked(apiGet).mockResolvedValue({
      data: [],
      page: 1,
      totalPages: 1,
      total: 0,
    });
    render(<SearchPage />);
    expect(await screen.findByRole("heading", { name: "खोज परिणाम" })).toBeInTheDocument();
    expect(apiGet).toHaveBeenCalledWith(
      `/api/v1/search?q=${encodeURIComponent("नेपाल")}&page=1&pageSize=12`,
    );
  });
});

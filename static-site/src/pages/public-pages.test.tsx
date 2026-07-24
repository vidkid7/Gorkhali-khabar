import { render, screen } from "@testing-library/react";
import { describe, expect, it, vi } from "vitest";
import { apiGet } from "../api/client";
import { CollectionPage } from "./CollectionPage";
import { ApiRequestError } from "../api/client";
import { endpointForUtility, loadUtilityPayloads } from "./UtilityPage";
import { StaticPage } from "./StaticPage";

vi.mock("../api/client", async (importOriginal) => {
  const original = await importOriginal<typeof import("../api/client")>();
  return { ...original, apiGet: vi.fn() };
});

describe("remaining public pages", () => {
  it("maps utilities to the Laravel endpoints", () => {
    expect(endpointForUtility("finance")).toEqual([
      "/api/v1/finance/exchange-rates",
      "/api/v1/finance/gold-silver",
    ]);
    expect(endpointForUtility("sports")).toEqual([
      "/api/v1/sports/tournaments",
      "/api/v1/sports/matches",
    ]);
    expect(endpointForUtility("rashifal")).toEqual(["/api/v1/rashifal"]);
    expect(endpointForUtility("patro")).toEqual([
      "/api/v1/calendar/panchang",
      "/api/v1/calendar/holidays",
    ]);
    expect(endpointForUtility("share-market")).toEqual(["/api/v1/nepse"]);
  });

  it("loads the requested public collection", async () => {
    vi.mocked(apiGet).mockResolvedValue([]);
    render(<CollectionPage kind="reels" />);
    expect(await screen.findByRole("heading", { name: "रिल्स" })).toBeInTheDocument();
    expect(apiGet).toHaveBeenCalledWith("/api/v1/reels");
  });

  it("treats an empty utility endpoint as an empty panel", async () => {
    vi.mocked(apiGet).mockRejectedValue(
      new ApiRequestError(404, "No forex data"),
    );
    await expect(loadUtilityPayloads(["/api/v1/finance/exchange-rates"]))
      .resolves.toEqual([
        { endpoint: "/api/v1/finance/exchange-rates", data: null },
      ]);
  });

  it("renders policy copy without an API request", () => {
    render(<StaticPage slug="privacy-policy" />);
    expect(screen.getByRole("heading", { name: "गोपनीयता नीति" })).toBeInTheDocument();
  });
});

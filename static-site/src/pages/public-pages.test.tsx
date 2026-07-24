import { render, screen } from "@testing-library/react";
import { describe, expect, it, vi } from "vitest";
import { apiGet } from "../api/client";
import { CollectionPage } from "./CollectionPage";
import { endpointForUtility } from "./UtilityPage";
import { StaticPage } from "./StaticPage";

vi.mock("../api/client", () => ({ apiGet: vi.fn() }));

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

  it("renders policy copy without an API request", () => {
    render(<StaticPage slug="privacy-policy" />);
    expect(screen.getByRole("heading", { name: "गोपनीयता नीति" })).toBeInTheDocument();
  });
});

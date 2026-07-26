import { render, screen, waitFor } from "@testing-library/react";
import { afterEach, describe, expect, it, vi } from "vitest";
import { AdSlot } from "@/components/ads/AdSlot";

const campaign = {
  id: "aashatech-header",
  title: "AashaTech Digital Systems",
  image_url: "/images/ads/aashatech/aashatech-leaderboard.webp",
  target_url: "https://www.aashatech.com/",
  position: { type: "HEADER", width: 728, height: 90 },
};

afterEach(() => {
  vi.unstubAllGlobals();
});

describe("AdSlot", () => {
  it("renders a disclosed sponsored link and tracks one impression", async () => {
    const fetchMock = vi.fn(async (input: RequestInfo | URL) => {
      const url = String(input);
      if (url.includes("/impression")) {
        return new Response(JSON.stringify({ success: true, data: { impressions: 1 } }), {
          status: 200,
          headers: { "Content-Type": "application/json" },
        });
      }

      return new Response(JSON.stringify({ success: true, data: [campaign] }), {
        status: 200,
        headers: { "Content-Type": "application/json" },
      });
    });
    vi.stubGlobal("fetch", fetchMock);

    render(<AdSlot position="HEADER" />);

    expect(await screen.findByText("विज्ञापन / Advertisement")).toBeVisible();
    expect(screen.getByTestId("ad-HEADER")).toHaveAttribute("data-layout", "leaderboard");
    expect(screen.getByRole("link"))
      .toHaveAttribute("href", "https://www.aashatech.com/");
    expect(screen.getByRole("link")).toHaveClass("max-w-[728px]");
    expect(screen.getByRole("img")).toHaveClass("object-contain");
    expect(screen.getByRole("link")).toHaveAttribute(
      "rel",
      expect.stringContaining("sponsored"),
    );
    await waitFor(() => {
      expect(fetchMock).toHaveBeenCalledWith(
        "/api/v1/ads/aashatech-header/impression",
        expect.objectContaining({ method: "POST" }),
      );
    });
  });

  it("collapses when the API has no active advertisement", async () => {
    vi.stubGlobal(
      "fetch",
      vi.fn(async () =>
        new Response(JSON.stringify({ success: true, data: [] }), {
          status: 200,
          headers: { "Content-Type": "application/json" },
        }),
      ),
    );

    const { container } = render(<AdSlot position="HEADER" />);

    await waitFor(() => expect(fetch).toHaveBeenCalled());
    expect(container).toBeEmptyDOMElement();
  });

  it("constrains sidebar advertisements to their intended creative width", async () => {
    const sidebarCampaign = {
      ...campaign,
      id: "aashatech-sidebar",
      position: { type: "SIDEBAR", width: 300, height: 250 },
    };
    vi.stubGlobal(
      "fetch",
      vi.fn(async (input: RequestInfo | URL) => {
        const url = String(input);
        return new Response(
          JSON.stringify({
            success: true,
            data: url.includes("/impression") ? { impressions: 1 } : [sidebarCampaign],
          }),
          { status: 200, headers: { "Content-Type": "application/json" } },
        );
      }),
    );

    render(<AdSlot position="SIDEBAR" />);

    expect(await screen.findByTestId("ad-SIDEBAR")).toHaveAttribute("data-layout", "sidebar");
    expect(screen.getByRole("link")).toHaveClass("max-w-[300px]");
  });
});

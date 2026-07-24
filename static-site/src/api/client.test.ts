import { afterEach, describe, expect, it, vi } from "vitest";
import { apiGet, ApiRequestError } from "./client";

afterEach(() => vi.unstubAllGlobals());

describe("apiGet", () => {
  it("unwraps successful Laravel responses", async () => {
    vi.stubGlobal(
      "fetch",
      vi.fn().mockResolvedValue(
        new Response(
          JSON.stringify({
            success: true,
            data: { service: "gorkhali-api" },
          }),
          {
            status: 200,
            headers: { "Content-Type": "application/json" },
          },
        ),
      ),
    );

    await expect(
      apiGet<{ service: string }>("/api/v1/status"),
    ).resolves.toEqual({ service: "gorkhali-api" });
  });

  it("throws a stable public error for failed requests", async () => {
    vi.stubGlobal(
      "fetch",
      vi.fn().mockResolvedValue(
        new Response(
          JSON.stringify({ success: false, error: "Not found" }),
          {
            status: 404,
            headers: { "Content-Type": "application/json" },
          },
        ),
      ),
    );

    await expect(apiGet("/api/v1/missing")).rejects.toEqual(
      new ApiRequestError(404, "Not found"),
    );
  });
});

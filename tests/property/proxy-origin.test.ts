import { describe, expect, it } from "vitest";
import { NextRequest } from "next/server";
import { proxy } from "@/proxy";

describe("Proxy origin validation", () => {
  it("allows same public origin when Railway forwards the custom host", () => {
    const request = new NextRequest(
      new Request("https://namaste-express-production.up.railway.app/api/v1/articles", {
        method: "POST",
        headers: {
          origin: "https://www.namastexpress.org",
          "x-forwarded-host": "www.namastexpress.org",
        },
      })
    );

    const response = proxy(request);

    expect(response.status).not.toBe(403);
  });

  it("rejects unsafe API requests from unknown origins", () => {
    const request = new NextRequest(
      new Request("https://namaste-express-production.up.railway.app/api/v1/articles", {
        method: "POST",
        headers: {
          origin: "https://attacker.example",
          "x-forwarded-host": "www.namastexpress.org",
        },
      })
    );

    const response = proxy(request);

    expect(response.status).toBe(403);
  });
});

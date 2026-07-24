import { describe, expect, it } from "vitest";
import { resolvePublicRoute } from "./router";

describe("resolvePublicRoute", () => {
  it.each([
    ["/", { name: "home" }],
    ["/articles/example-story", { name: "article", slug: "example-story" }],
    ["/categories/politics", { name: "category", slug: "politics" }],
    ["/search", { name: "search" }],
    ["/sports", { name: "utility", slug: "sports" }],
  ])("resolves %s", (path, expected) => {
    expect(resolvePublicRoute(path)).toEqual(expected);
  });

  it("returns not-found for unknown paths", () => {
    expect(resolvePublicRoute("/not-a-public-route")).toEqual({
      name: "not-found",
    });
  });
});

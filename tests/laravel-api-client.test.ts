import { afterEach, describe, expect, it, vi } from "vitest";
import {
  LaravelApiError,
  createLaravelApiClient,
  resolveLaravelApiBaseUrl,
} from "@/lib/api/laravel";

afterEach(() => {
  vi.unstubAllGlobals();
  vi.restoreAllMocks();
  delete process.env.API_INTERNAL_URL;
  delete process.env.NEXT_PUBLIC_LARAVEL_API_URL;
});

describe("Laravel API client", () => {
  it("uses a relative browser URL and includes credentials", async () => {
    const fetcher = vi.fn().mockResolvedValue(
      new Response(JSON.stringify({ success: true, data: { site_name: "Gorkhali" } }), {
        status: 200,
        headers: { "Content-Type": "application/json" },
      }),
    );
    const client = createLaravelApiClient({ fetcher, baseUrl: "" });

    await expect(client.get<{ site_name: string }>("/api/v1/settings")).resolves.toEqual({
      site_name: "Gorkhali",
    });
    expect(fetcher).toHaveBeenCalledWith(
      "/api/v1/settings",
      expect.objectContaining({ credentials: "include", method: "GET" }),
    );
  });

  it("resolves the private server base URL without duplicate slashes", () => {
    expect(
      resolveLaravelApiBaseUrl({
        server: true,
        internalUrl: "http://web/",
        publicUrl: "https://public.example.test/",
      }),
    ).toBe("http://web");
    expect(
      resolveLaravelApiBaseUrl({
        server: false,
        internalUrl: "http://private/",
        publicUrl: "https://public.example.test/",
      }),
    ).toBe("https://public.example.test");
  });

  it.each([
    [400, "Validation failed", { email: ["Email is required"] }],
    [401, "Authentication required", undefined],
    [403, "Forbidden", undefined],
    [500, "Internal server error", undefined],
  ])("normalizes a %i Laravel error", async (status, message, errors) => {
    const fetcher = vi.fn().mockResolvedValue(
      new Response(JSON.stringify({ success: false, error: message, errors }), {
        status,
        headers: { "Content-Type": "application/json" },
      }),
    );
    const client = createLaravelApiClient({ fetcher, baseUrl: "" });

    const rejection = client.get("/api/v1/protected");
    await expect(rejection).rejects.toBeInstanceOf(LaravelApiError);
    await expect(rejection).rejects.toMatchObject({ status, message, errors });
  });

  it("normalizes network failures without leaking the original error", async () => {
    const client = createLaravelApiClient({
      fetcher: vi.fn().mockRejectedValue(new Error("private upstream detail")),
      baseUrl: "",
    });

    await expect(client.get("/api/v1/status")).rejects.toMatchObject({
      status: 0,
      message: "Unable to reach the server.",
    });
  });

  it("serializes JSON but leaves FormData untouched", async () => {
    const fetcher = vi.fn().mockImplementation(() =>
      Promise.resolve(
        new Response(JSON.stringify({ success: true, data: {} })),
      ),
    );
    const client = createLaravelApiClient({ fetcher, baseUrl: "" });

    await client.post("/api/v1/articles", { title: "Title" }, { csrf: false });
    const jsonInit = fetcher.mock.calls[0][1] as RequestInit;
    expect(jsonInit.body).toBe(JSON.stringify({ title: "Title" }));
    expect(new Headers(jsonInit.headers).get("Content-Type")).toBe("application/json");

    const form = new FormData();
    form.append("file", new Blob(["image"]), "image.png");
    await client.post("/api/v1/media", form, { csrf: false });
    const formInit = fetcher.mock.calls[1][1] as RequestInit;
    expect(formInit.body).toBe(form);
    expect(new Headers(formInit.headers).has("Content-Type")).toBe(false);
  });

  it("acquires Sanctum CSRF and forwards the decoded XSRF token", async () => {
    const fetcher = vi
      .fn()
      .mockResolvedValueOnce(new Response(null, { status: 204 }))
      .mockResolvedValueOnce(
        new Response(JSON.stringify({ success: true, data: { id: "article-1" } })),
      );
    const client = createLaravelApiClient({
      fetcher,
      baseUrl: "",
      readXsrfToken: () => "token%3Dvalue",
    });

    await client.post("/api/v1/articles", { title: "Title" });

    expect(fetcher.mock.calls[0][0]).toBe("/sanctum/csrf-cookie");
    expect(fetcher.mock.calls[1][0]).toBe("/api/v1/articles");
    expect(
      new Headers((fetcher.mock.calls[1][1] as RequestInit).headers).get("X-XSRF-TOKEN"),
    ).toBe("token=value");
  });

  it("returns undefined for an empty successful response", async () => {
    const client = createLaravelApiClient({
      fetcher: vi.fn().mockResolvedValue(new Response(null, { status: 204 })),
      baseUrl: "",
    });

    await expect(client.delete("/api/v1/bookmarks/article-1", { csrf: false })).resolves.toBeUndefined();
  });
});

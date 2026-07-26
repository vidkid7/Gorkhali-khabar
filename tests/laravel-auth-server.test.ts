import { beforeEach, describe, expect, it, vi } from "vitest";

const cookiesMock = vi.fn();

vi.mock("next/headers", () => ({
  cookies: cookiesMock,
}));

describe("Laravel server authorization", () => {
  beforeEach(() => {
    vi.resetModules();
    vi.restoreAllMocks();
    process.env.API_INTERNAL_URL = "http://laravel.test";
    process.env.SESSION_COOKIE = "gorkhali_session";
  });

  it("forwards only the Laravel session cookie and enforces roles", async () => {
    cookiesMock.mockResolvedValue({
      has: (name: string) => name === "gorkhali_session",
      get: (name: string) =>
        name === "gorkhali_session"
          ? { name, value: "session-value" }
          : undefined,
      getAll: () => [
        { name: "gorkhali_session", value: "session-value" },
        { name: "tracking", value: "must-not-be-forwarded" },
      ],
    });
    const fetcher = vi.fn().mockImplementation(() =>
      Promise.resolve(
        new Response(
          JSON.stringify({
            success: true,
            data: {
              user: {
                id: "editor-1",
                name: "Editor",
                email: "editor@example.test",
                image: null,
                role: "EDITOR",
                email_verified: null,
                session_version: 1,
              },
            },
          }),
          { status: 200 },
        ),
      ),
    );
    vi.stubGlobal("fetch", fetcher);
    const { requireRole } = await import("@/lib/auth-helpers");

    await expect(requireRole(["EDITOR"])).resolves.toMatchObject({
      error: null,
      session: { user: { role: "EDITOR" } },
    });
    await expect(requireRole(["ADMIN"])).resolves.toMatchObject({
      error: "forbidden",
      session: null,
    });
    expect(
      new Headers((fetcher.mock.calls[0][1] as RequestInit).headers).get(
        "Cookie",
      ),
    ).toBe("gorkhali_session=session-value");
  });

  it("does not call Laravel when the session cookie is absent", async () => {
    cookiesMock.mockResolvedValue({
      has: () => false,
      getAll: () => [],
    });
    const fetcher = vi.fn();
    vi.stubGlobal("fetch", fetcher);
    const { requireAuth } = await import("@/lib/auth-helpers");

    await expect(requireAuth()).resolves.toEqual({
      error: "unauthorized",
      session: null,
    });
    expect(fetcher).not.toHaveBeenCalled();
  });
});

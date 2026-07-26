import { act, renderHook, waitFor } from "@testing-library/react";
import type { ReactNode } from "react";
import { describe, expect, it, vi } from "vitest";
import {
  LaravelAuthProvider,
  type LaravelAuthService,
  useLaravelAuth,
} from "@/contexts/LaravelAuthContext";

const user = {
  id: "user-1",
  name: "Editor",
  email: "editor@example.test",
  image: null,
  role: "EDITOR" as const,
  email_verified: "2026-01-01T00:00:00.000Z",
  session_version: 2,
};

function wrapper(service: LaravelAuthService) {
  return function AuthWrapper({ children }: { children: ReactNode }) {
    return (
      <LaravelAuthProvider service={service}>{children}</LaravelAuthProvider>
    );
  };
}

describe("Laravel auth context", () => {
  it("loads an authenticated Laravel session", async () => {
    const service: LaravelAuthService = {
      session: vi.fn().mockResolvedValue({ user }),
      login: vi.fn(),
      logout: vi.fn(),
      googleRedirect: vi.fn(),
    };
    const { result } = renderHook(() => useLaravelAuth(), {
      wrapper: wrapper(service),
    });

    expect(result.current.status).toBe("loading");
    await waitFor(() => expect(result.current.status).toBe("authenticated"));
    expect(result.current.data?.user).toEqual(user);
  });

  it("treats a missing session as unauthenticated", async () => {
    const service: LaravelAuthService = {
      session: vi.fn().mockResolvedValue(null),
      login: vi.fn(),
      logout: vi.fn(),
      googleRedirect: vi.fn(),
    };
    const { result } = renderHook(() => useLaravelAuth(), {
      wrapper: wrapper(service),
    });

    await waitFor(() => expect(result.current.status).toBe("unauthenticated"));
    expect(result.current.data).toBeNull();
  });

  it("updates state after login and logout", async () => {
    const service: LaravelAuthService = {
      session: vi.fn().mockResolvedValue(null),
      login: vi.fn().mockResolvedValue({ user }),
      logout: vi.fn().mockResolvedValue(undefined),
      googleRedirect: vi.fn(),
    };
    const { result } = renderHook(() => useLaravelAuth(), {
      wrapper: wrapper(service),
    });
    await waitFor(() => expect(result.current.status).toBe("unauthenticated"));

    await act(() => result.current.login("editor@example.test", "Password1!"));
    expect(result.current.status).toBe("authenticated");
    expect(result.current.data?.user.role).toBe("EDITOR");

    await act(() => result.current.logout());
    expect(result.current.status).toBe("unauthenticated");
    expect(result.current.data).toBeNull();
  });

  it("delegates Google login without changing the current UI state", async () => {
    const googleRedirect = vi.fn();
    const service: LaravelAuthService = {
      session: vi.fn().mockResolvedValue(null),
      login: vi.fn(),
      logout: vi.fn(),
      googleRedirect,
    };
    const { result } = renderHook(() => useLaravelAuth(), {
      wrapper: wrapper(service),
    });
    await waitFor(() => expect(result.current.status).toBe("unauthenticated"));

    act(() => result.current.googleRedirect("/profile"));
    expect(googleRedirect).toHaveBeenCalledWith("/profile");
  });
});

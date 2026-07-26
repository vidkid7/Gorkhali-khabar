"use client";

import {
  createContext,
  useCallback,
  useContext,
  useEffect,
  useMemo,
  useState,
  type ReactNode,
} from "react";
import {
  LaravelApiError,
  laravelApi,
} from "@/lib/api/laravel";
import type { LaravelSession } from "@/lib/auth-types";

export type {
  LaravelSession,
  LaravelUser,
  LaravelUserRole,
} from "@/lib/auth-types";

export type LaravelAuthStatus =
  | "loading"
  | "authenticated"
  | "unauthenticated";

export interface LaravelAuthService {
  session(): Promise<LaravelSession | null>;
  login(email: string, password: string): Promise<LaravelSession>;
  logout(): Promise<void>;
  googleRedirect(callbackUrl: string): void;
}

interface LaravelAuthContextValue {
  data: LaravelSession | null;
  status: LaravelAuthStatus;
  login(email: string, password: string): Promise<LaravelSession>;
  logout(): Promise<void>;
  refresh(): Promise<LaravelSession | null>;
  googleRedirect(callbackUrl: string): void;
}

function safeCallbackUrl(value: string): string {
  if (
    !value.startsWith("/") ||
    value.startsWith("//") ||
    value.includes("\\")
  ) {
    return "/";
  }

  return value;
}

export const laravelAuthService: LaravelAuthService = {
  async session() {
    try {
      return await laravelApi.get<LaravelSession>("/api/v1/auth/session", {
        cache: "no-store",
        csrf: false,
      });
    } catch (error) {
      if (error instanceof LaravelApiError && error.status === 401) {
        return null;
      }

      throw error;
    }
  },

  login(email, password) {
    return laravelApi.post<LaravelSession>("/api/v1/auth/login", {
      email,
      password,
    });
  },

  async logout() {
    await laravelApi.post<void>("/api/v1/auth/logout");
  },

  googleRedirect(callbackUrl) {
    const safeUrl = safeCallbackUrl(callbackUrl);
    window.sessionStorage.setItem("gorkhali_auth_callback", safeUrl);
    window.location.assign("/api/v1/auth/google/redirect");
  },
};

const LaravelAuthContext = createContext<LaravelAuthContextValue | null>(null);

export function LaravelAuthProvider({
  children,
  service = laravelAuthService,
}: {
  children: ReactNode;
  service?: LaravelAuthService;
}) {
  const [data, setData] = useState<LaravelSession | null>(null);
  const [status, setStatus] = useState<LaravelAuthStatus>("loading");

  const refresh = useCallback(async () => {
    try {
      const session = await service.session();
      setData(session);
      setStatus(session ? "authenticated" : "unauthenticated");
      return session;
    } catch {
      setData(null);
      setStatus("unauthenticated");
      return null;
    }
  }, [service]);

  useEffect(() => {
    void refresh();
  }, [refresh]);

  const login = useCallback(
    async (email: string, password: string) => {
      const session = await service.login(email, password);
      setData(session);
      setStatus("authenticated");
      return session;
    },
    [service],
  );

  const logout = useCallback(async () => {
    try {
      await service.logout();
    } finally {
      setData(null);
      setStatus("unauthenticated");
    }
  }, [service]);

  const googleRedirect = useCallback(
    (callbackUrl: string) => service.googleRedirect(callbackUrl),
    [service],
  );

  const value = useMemo<LaravelAuthContextValue>(
    () => ({
      data,
      status,
      login,
      logout,
      refresh,
      googleRedirect,
    }),
    [data, googleRedirect, login, logout, refresh, status],
  );

  return (
    <LaravelAuthContext.Provider value={value}>
      {children}
    </LaravelAuthContext.Provider>
  );
}

export function useLaravelAuth(): LaravelAuthContextValue {
  const value = useContext(LaravelAuthContext);

  if (!value) {
    throw new Error("useLaravelAuth must be used within LaravelAuthProvider");
  }

  return value;
}

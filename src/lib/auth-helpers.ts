import { cookies } from "next/headers";
import { NextResponse } from "next/server";
import {
  LaravelApiError,
  createLaravelApiClient,
  resolveLaravelApiBaseUrl,
} from "@/lib/api/laravel";
import type {
  LaravelSession,
  LaravelUserRole,
} from "@/lib/auth-types";
import type { ApiResponse } from "@/types";

export async function getSession(): Promise<LaravelSession | null> {
  const cookieStore = await cookies();
  const sessionCookieName = process.env.SESSION_COOKIE || "gorkhali_session";

  if (!cookieStore.has(sessionCookieName)) {
    return null;
  }

  const sessionCookie = cookieStore.get(sessionCookieName);
  const cookieHeader = sessionCookie
    ? `${sessionCookie.name}=${sessionCookie.value}`
    : "";
  const baseUrl = resolveLaravelApiBaseUrl({
    server: true,
    internalUrl: process.env.API_INTERNAL_URL,
    publicUrl: process.env.NEXT_PUBLIC_LARAVEL_API_URL,
  });
  const client = createLaravelApiClient({ baseUrl });

  try {
    return await client.get<LaravelSession>("/api/v1/auth/session", {
      headers: { Cookie: cookieHeader },
      cache: "no-store",
      csrf: false,
    });
  } catch (error) {
    if (error instanceof LaravelApiError && error.status === 401) {
      return null;
    }

    throw error;
  }
}

export async function requireAuth() {
  const session = await getSession();

  if (!session?.user) {
    return { error: "unauthorized" as const, session: null };
  }

  return { error: null, session };
}

export async function requireRole(roles: LaravelUserRole[]) {
  const { error, session } = await requireAuth();

  if (error) {
    return { error, session: null };
  }
  if (!roles.includes(session.user.role)) {
    return { error: "forbidden" as const, session: null };
  }

  return { error: null, session };
}

export function unauthorizedResponse() {
  return NextResponse.json<ApiResponse>(
    { success: false, error: "प्रमाणीकरण आवश्यक छ" },
    { status: 401 },
  );
}

export function forbiddenResponse() {
  return NextResponse.json<ApiResponse>(
    { success: false, error: "अनुमति छैन" },
    { status: 403 },
  );
}

export function notFoundResponse(message = "स्रोत फेला परेन") {
  return NextResponse.json<ApiResponse>(
    { success: false, error: message },
    { status: 404 },
  );
}

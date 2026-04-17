import { auth } from "@/lib/auth";
import { UserRole } from "@prisma/client";
import { NextResponse } from "next/server";
import type { ApiResponse } from "@/types";

export async function getSession() {
  return await auth();
}

export async function requireAuth() {
  const session = await auth();
  if (!session?.user) {
    return { error: "unauthorized" as const, session: null };
  }
  return { error: null, session };
}

export async function requireRole(roles: UserRole[]) {
  const { error, session } = await requireAuth();
  if (error) return { error, session: null };
  if (!roles.includes(session!.user.role)) {
    return { error: "forbidden" as const, session: null };
  }
  return { error: null, session: session! };
}

export function unauthorizedResponse() {
  return NextResponse.json<ApiResponse>(
    { success: false, error: "प्रमाणीकरण आवश्यक छ" },
    { status: 401 }
  );
}

export function forbiddenResponse() {
  return NextResponse.json<ApiResponse>(
    { success: false, error: "अनुमति छैन" },
    { status: 403 }
  );
}

import { NextRequest, NextResponse } from "next/server";
import { prisma } from "@/lib/prisma";
import { requireRole, unauthorizedResponse, forbiddenResponse } from "@/lib/auth-helpers";
import { auditLog } from "@/lib/audit";
import type { ApiResponse } from "@/types";

export async function PATCH(
  request: NextRequest,
  { params }: { params: Promise<{ id: string }> }
) {
  try {
    const { error, session } = await requireRole(["ADMIN"]);
    if (error === "unauthorized") return unauthorizedResponse();
    if (error === "forbidden") return forbiddenResponse();

    const { id } = await params;
    const body = await request.json();
    const { role } = body;

    if (!role || !["READER", "AUTHOR", "EDITOR", "ADMIN"].includes(role)) {
      return NextResponse.json<ApiResponse>(
        { success: false, error: "Invalid role" },
        { status: 400 }
      );
    }

    // Prevent self-role change
    if (id === session!.user.id) {
      return NextResponse.json<ApiResponse>(
        { success: false, error: "Cannot change your own role" },
        { status: 400 }
      );
    }

    const user = await prisma.user.findUnique({ where: { id } });
    if (!user) {
      return NextResponse.json<ApiResponse>(
        { success: false, error: "User not found" },
        { status: 404 }
      );
    }

    const oldRole = user.role;
    const updated = await prisma.user.update({
      where: { id },
      data: { role },
      select: { id: true, name: true, email: true, role: true },
    });

    await auditLog({
      adminId: session!.user.id,
      action: "UPDATE",
      entity: "User",
      entityId: id,
      oldValue: { role: oldRole },
      newValue: { role },
    });

    return NextResponse.json<ApiResponse>({ success: true, data: updated });
  } catch (error) {
    console.error("User role PATCH error:", error);
    return NextResponse.json<ApiResponse>(
      { success: false, error: "Failed to update role" },
      { status: 500 }
    );
  }
}

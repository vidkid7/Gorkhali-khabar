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
    const { error, session } = await requireRole(["ADMIN", "EDITOR"]);
    if (error === "unauthorized") return unauthorizedResponse();
    if (error === "forbidden") return forbiddenResponse();

    const { id } = await params;
    const body = await request.json();

    const item = await prisma.breakingNews.findUnique({ where: { id } });
    if (!item) {
      return NextResponse.json<ApiResponse>(
        { success: false, error: "Not found" },
        { status: 404 }
      );
    }

    const updated = await prisma.breakingNews.update({
      where: { id },
      data: { is_active: body.is_active },
    });

    await auditLog({
      adminId: session!.user.id,
      action: "UPDATE",
      entity: "BreakingNews",
      entityId: id,
      oldValue: { is_active: item.is_active },
      newValue: { is_active: body.is_active },
    });

    return NextResponse.json<ApiResponse>({ success: true, data: updated });
  } catch (error) {
    console.error("BreakingNews PATCH error:", error);
    return NextResponse.json<ApiResponse>(
      { success: false, error: "Failed to update" },
      { status: 500 }
    );
  }
}

import { NextRequest, NextResponse } from "next/server";
import { prisma } from "@/lib/prisma";
import { requireRole, unauthorizedResponse, forbiddenResponse } from "@/lib/auth-helpers";
import { auditLog } from "@/lib/audit";
import type { ApiResponse } from "@/types";

export async function POST(request: NextRequest) {
  try {
    const { error, session } = await requireRole(["ADMIN", "EDITOR"]);
    if (error === "unauthorized") return unauthorizedResponse();
    if (error === "forbidden") return forbiddenResponse();

    const body = await request.json();
    const { title, article_id, expires_at } = body;

    if (!title) {
      return NextResponse.json<ApiResponse>(
        { success: false, error: "Title is required" },
        { status: 400 }
      );
    }

    const item = await prisma.breakingNews.create({
      data: {
        title,
        article_id: article_id || null,
        expires_at: expires_at ? new Date(expires_at) : null,
        is_active: true,
      },
    });

    await auditLog({
      adminId: session!.user.id,
      action: "CREATE",
      entity: "BreakingNews",
      entityId: item.id,
      newValue: { title },
    });

    return NextResponse.json<ApiResponse>(
      { success: true, data: item },
      { status: 201 }
    );
  } catch (error) {
    console.error("BreakingNews POST error:", error);
    return NextResponse.json<ApiResponse>(
      { success: false, error: "Failed to create breaking news" },
      { status: 500 }
    );
  }
}

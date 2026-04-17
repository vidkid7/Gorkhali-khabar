import { NextRequest, NextResponse } from "next/server";
import { prisma } from "@/lib/prisma";
import { categorySchema } from "@/lib/validations";
import { requireRole, unauthorizedResponse, forbiddenResponse } from "@/lib/auth-helpers";
import { auditLog } from "@/lib/audit";
import type { ApiResponse } from "@/types";

export async function GET() {
  try {
    const categories = await prisma.category.findMany({
      where: { is_active: true },
      orderBy: { sort_order: "asc" },
      include: {
        _count: { select: { articles: true } },
        children: {
          where: { is_active: true },
          orderBy: { sort_order: "asc" },
          include: { _count: { select: { articles: true } } },
        },
      },
    });

    return NextResponse.json<ApiResponse>({ success: true, data: categories });
  } catch (error) {
    console.error("Categories GET error:", error);
    return NextResponse.json<ApiResponse>(
      { success: false, error: "वर्गहरू प्राप्त गर्दा त्रुटि भयो" },
      { status: 500 }
    );
  }
}

export async function POST(request: NextRequest) {
  try {
    const { error, session } = await requireRole(["ADMIN", "EDITOR"]);
    if (error === "unauthorized") return unauthorizedResponse();
    if (error === "forbidden") return forbiddenResponse();

    const body = await request.json();
    const parsed = categorySchema.safeParse(body);

    if (!parsed.success) {
      return NextResponse.json<ApiResponse>(
        { success: false, error: parsed.error.issues[0].message },
        { status: 400 }
      );
    }

    const existing = await prisma.category.findUnique({
      where: { slug: parsed.data.slug },
    });
    if (existing) {
      return NextResponse.json<ApiResponse>(
        { success: false, error: "यो स्लग पहिले नै प्रयोग भइसकेको छ" },
        { status: 409 }
      );
    }

    const category = await prisma.category.create({
      data: parsed.data,
      include: { _count: { select: { articles: true } } },
    });

    await auditLog({
      adminId: session!.user.id,
      action: "CREATE",
      entity: "Category",
      entityId: category.id,
      newValue: { name: category.name, slug: category.slug },
    });

    return NextResponse.json<ApiResponse>(
      { success: true, data: category },
      { status: 201 }
    );
  } catch (error) {
    console.error("Category POST error:", error);
    return NextResponse.json<ApiResponse>(
      { success: false, error: "वर्ग सिर्जना गर्दा त्रुटि भयो" },
      { status: 500 }
    );
  }
}

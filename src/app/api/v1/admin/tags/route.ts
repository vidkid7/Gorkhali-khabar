import { NextRequest, NextResponse } from "next/server";
import { prisma } from "@/lib/prisma";
import { requireRole, unauthorizedResponse, forbiddenResponse } from "@/lib/auth-helpers";
import type { ApiResponse } from "@/types";

function toSlug(name: string): string {
  return name
    .toLowerCase()
    .trim()
    .replace(/[\s_]+/g, "-")
    .replace(/[^\w-]/g, "")
    .replace(/--+/g, "-");
}

export async function POST(request: NextRequest) {
  try {
    const { error } = await requireRole(["ADMIN", "EDITOR"]);
    if (error === "unauthorized") return unauthorizedResponse();
    if (error === "forbidden") return forbiddenResponse();

    const body = await request.json();
    const { name, name_en } = body;

    if (!name || typeof name !== "string" || !name.trim()) {
      return NextResponse.json<ApiResponse>(
        { success: false, error: "ट्याग नाम आवश्यक छ" },
        { status: 400 }
      );
    }

    const slug = toSlug(name);
    if (!slug) {
      return NextResponse.json<ApiResponse>(
        { success: false, error: "अमान्य ट्याग नाम" },
        { status: 400 }
      );
    }

    const existing = await prisma.tag.findUnique({ where: { slug } });
    if (existing) {
      return NextResponse.json<ApiResponse>(
        { success: false, error: "यो ट्याग पहिले नै अवस्थित छ" },
        { status: 409 }
      );
    }

    const tag = await prisma.tag.create({
      data: { name: name.trim(), name_en: name_en?.trim() || null, slug },
    });

    return NextResponse.json<ApiResponse>({ success: true, data: tag }, { status: 201 });
  } catch (error) {
    console.error("Admin tags POST error:", error);
    return NextResponse.json<ApiResponse>(
      { success: false, error: "ट्याग सिर्जना गर्दा त्रुटि भयो" },
      { status: 500 }
    );
  }
}

export async function DELETE(request: NextRequest) {
  try {
    const { error } = await requireRole(["ADMIN", "EDITOR"]);
    if (error === "unauthorized") return unauthorizedResponse();
    if (error === "forbidden") return forbiddenResponse();

    const { searchParams } = new URL(request.url);
    const id = searchParams.get("id");

    if (!id) {
      return NextResponse.json<ApiResponse>(
        { success: false, error: "ट्याग ID आवश्यक छ" },
        { status: 400 }
      );
    }

    await prisma.tag.delete({ where: { id } });

    return NextResponse.json<ApiResponse>({ success: true, data: null });
  } catch (error) {
    console.error("Admin tags DELETE error:", error);
    return NextResponse.json<ApiResponse>(
      { success: false, error: "ट्याग मेट्दा त्रुटि भयो" },
      { status: 500 }
    );
  }
}

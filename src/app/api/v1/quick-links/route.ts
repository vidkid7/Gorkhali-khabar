import { NextResponse } from "next/server";
import { prisma } from "@/lib/prisma";
import type { ApiResponse } from "@/types";

export const dynamic = "force-dynamic";

// Public read-only listing of active "Today's Tools" / quick links.
export async function GET() {
  try {
    const links = await prisma.quickLink.findMany({
      where: { is_active: true },
      orderBy: [{ sort_order: "asc" }, { created_at: "asc" }],
      select: {
        slug: true,
        href: true,
        title_ne: true,
        title_en: true,
        description_ne: true,
        description_en: true,
        icon_key: true,
        accent_color: true,
      },
    });

    return NextResponse.json<ApiResponse<typeof links>>({
      success: true,
      data: links,
    });
  } catch (error) {
    console.error("Public quick-links GET error:", error);
    return NextResponse.json<ApiResponse>(
      { success: false, error: "लिंकहरू प्राप्त गर्दा त्रुटि भयो" },
      { status: 500 },
    );
  }
}

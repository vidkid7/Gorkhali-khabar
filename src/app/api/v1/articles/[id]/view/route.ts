import { NextRequest, NextResponse } from "next/server";
import { prisma } from "@/lib/prisma";
import { headers } from "next/headers";
import type { ApiResponse } from "@/types";

export async function POST(
  _request: NextRequest,
  { params }: { params: Promise<{ id: string }> }
) {
  try {
    const { id } = await params;

    const article = await prisma.article.findUnique({ where: { id } });
    if (!article) {
      return NextResponse.json<ApiResponse>(
        { success: false, error: "लेख फेला परेन" },
        { status: 404 }
      );
    }

    const headersList = await headers();
    const ip = headersList.get("x-forwarded-for")?.split(",")[0]?.trim() || null;
    const userAgent = headersList.get("user-agent") || null;
    const referrer = headersList.get("referer") || null;

    const [, updatedArticle] = await prisma.$transaction([
      prisma.pageView.create({
        data: {
          page_url: `/articles/${article.slug}`,
          article_id: id,
          ip_address: ip,
          user_agent: userAgent,
          referrer: referrer,
        },
      }),
      prisma.article.update({
        where: { id },
        data: { view_count: { increment: 1 } },
        select: { id: true, view_count: true },
      }),
    ]);

    return NextResponse.json<ApiResponse>(
      { success: true, data: { view_count: updatedArticle.view_count } }
    );
  } catch (error) {
    console.error("View POST error:", error);
    return NextResponse.json<ApiResponse>(
      { success: false, error: "भ्यू रेकर्ड गर्दा त्रुटि भयो" },
      { status: 500 }
    );
  }
}

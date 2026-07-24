import { NextRequest, NextResponse } from "next/server";
import { prisma } from "@/lib/prisma";
import { requireRole, unauthorizedResponse, forbiddenResponse } from "@/lib/auth-helpers";
import { auditLog } from "@/lib/audit";
import type { ApiResponse } from "@/types";

export async function GET(request: NextRequest) {
  try {
    const { searchParams } = request.nextUrl;
    const active = searchParams.get("active");

    const where: Record<string, unknown> = {};
    if (active === "true") where.is_active = true;
    if (active === "false") where.is_active = false;

    const tournaments = await prisma.tournament.findMany({
      where,
      orderBy: { created_at: "desc" },
      include: {
        _count: { select: { matches: true } },
      },
    });

    return NextResponse.json<ApiResponse>({ success: true, data: tournaments });
  } catch (error) {
    console.error("Tournaments GET error:", error);
    return NextResponse.json<ApiResponse>(
      { success: false, error: "टुर्नामेन्टहरू प्राप्त गर्दा त्रुटि भयो" },
      { status: 500 }
    );
  }
}

export async function POST(request: NextRequest) {
  try {
    const { error, session } = await requireRole(["ADMIN"]);
    if (error === "unauthorized") return unauthorizedResponse();
    if (error === "forbidden") return forbiddenResponse();

    const body = await request.json();
    const { name, name_en, slug, sport_type, start_date, end_date } = body;

    if (!name || !slug || !sport_type) {
      return NextResponse.json<ApiResponse>(
        { success: false, error: "नाम, स्लग र खेल प्रकार आवश्यक छ" },
        { status: 400 }
      );
    }

    const existing = await prisma.tournament.findUnique({ where: { slug } });
    if (existing) {
      return NextResponse.json<ApiResponse>(
        { success: false, error: "यो स्लग पहिले नै प्रयोग भइसकेको छ" },
        { status: 409 }
      );
    }

    const tournament = await prisma.tournament.create({
      data: {
        name,
        name_en: name_en || null,
        slug,
        sport_type,
        start_date: start_date ? new Date(start_date) : null,
        end_date: end_date ? new Date(end_date) : null,
      },
    });

    await auditLog({
      adminId: session!.user.id,
      action: "CREATE",
      entity: "Tournament",
      entityId: tournament.id,
      newValue: { name: tournament.name, slug: tournament.slug },
    });

    return NextResponse.json<ApiResponse>(
      { success: true, data: tournament },
      { status: 201 }
    );
  } catch (error) {
    console.error("Tournament POST error:", error);
    return NextResponse.json<ApiResponse>(
      { success: false, error: "टुर्नामेन्ट सिर्जना गर्दा त्रुटि भयो" },
      { status: 500 }
    );
  }
}

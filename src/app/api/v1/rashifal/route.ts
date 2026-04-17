import { NextRequest, NextResponse } from "next/server";
import prisma from "@/lib/prisma";

export async function GET(req: NextRequest) {
  try {
    const { searchParams } = new URL(req.url);
    const sign = searchParams.get("sign") ?? undefined;

    const latest = await prisma.rashifal.findFirst({
      orderBy: { ad_date: "desc" },
      select: { ad_date: true },
    });

    if (!latest) {
      return NextResponse.json({ success: false, error: "No rashifal data" }, { status: 404 });
    }

    const where: Record<string, unknown> = { ad_date: latest.ad_date };
    if (sign) where.sign = sign;

    const data = await prisma.rashifal.findMany({
      where,
      orderBy: { sign: "asc" },
    });

    return NextResponse.json({
      success: true,
      data,
      date: latest.ad_date,
    }, {
      headers: { "Cache-Control": "public, s-maxage=86400, stale-while-revalidate=172800" }
    });
  } catch {
    return NextResponse.json({ success: false, error: "Failed to fetch rashifal" }, { status: 500 });
  }
}

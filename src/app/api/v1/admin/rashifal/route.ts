import { NextRequest, NextResponse } from "next/server";
import { prisma } from "@/lib/prisma";
import { requireRole, unauthorizedResponse, forbiddenResponse } from "@/lib/auth-helpers";

export async function GET() {
  const entries = await prisma.rashifal.findMany({ orderBy: [{ ad_date: "desc" }, { sign: "asc" }], take: 24 });
  return NextResponse.json({ success: true, data: entries });
}

export async function POST(req: NextRequest) {
  const { error } = await requireRole(["ADMIN", "EDITOR"]);
  if (error === "unauthorized") return unauthorizedResponse();
  if (error === "forbidden") return forbiddenResponse();

  try {
    const body = await req.json();
    const entry = await prisma.rashifal.create({
      data: {
        sign: body.sign,
        sign_ne: body.sign_ne || null,
        bs_year: Number(body.bs_year) || 2082,
        bs_month: Number(body.bs_month) || 1,
        bs_day: Number(body.bs_day) || 1,
        ad_date: new Date(body.ad_date || body.date),
        prediction: body.prediction,
        prediction_en: body.prediction_en || null,
        rating: body.rating ? Number(body.rating) : null,
      },
    });
    return NextResponse.json({ success: true, data: entry });
  } catch {
    return NextResponse.json({ success: false, error: "Failed to create rashifal entry" }, { status: 400 });
  }
}

export async function DELETE(req: NextRequest) {
  const { error } = await requireRole(["ADMIN"]);
  if (error === "unauthorized") return unauthorizedResponse();
  if (error === "forbidden") return forbiddenResponse();

  const id = req.nextUrl.searchParams.get("id");
  if (!id) return NextResponse.json({ success: false, error: "Missing id" }, { status: 400 });
  await prisma.rashifal.delete({ where: { id } });
  return NextResponse.json({ success: true });
}

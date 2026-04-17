import { NextRequest, NextResponse } from "next/server";
import { prisma } from "@/lib/prisma";
import { requireRole, unauthorizedResponse, forbiddenResponse } from "@/lib/auth-helpers";

export async function GET() {
  const holidays = await prisma.holiday.findMany({ orderBy: [{ bs_year: "desc" }, { bs_month: "asc" }, { bs_day: "asc" }] });
  return NextResponse.json({ success: true, data: holidays });
}

export async function POST(req: NextRequest) {
  const { error } = await requireRole(["ADMIN", "EDITOR"]);
  if (error === "unauthorized") return unauthorizedResponse();
  if (error === "forbidden") return forbiddenResponse();

  try {
    const body = await req.json();
    const holiday = await prisma.holiday.create({
      data: {
        title: body.title,
        title_en: body.title_en || null,
        bs_year: Number(body.bs_year),
        bs_month: Number(body.bs_month),
        bs_day: Number(body.bs_day),
        ad_date: new Date(body.ad_date),
        type: body.type || "public",
        is_public: body.is_public ?? true,
        description: body.description || null,
        description_en: body.description_en || null,
      },
    });
    return NextResponse.json({ success: true, data: holiday });
  } catch {
    return NextResponse.json({ success: false, error: "Failed to create holiday" }, { status: 400 });
  }
}

export async function DELETE(req: NextRequest) {
  const { error } = await requireRole(["ADMIN"]);
  if (error === "unauthorized") return unauthorizedResponse();
  if (error === "forbidden") return forbiddenResponse();

  const id = req.nextUrl.searchParams.get("id");
  if (!id) return NextResponse.json({ success: false, error: "Missing id" }, { status: 400 });
  await prisma.holiday.delete({ where: { id } });
  return NextResponse.json({ success: true });
}

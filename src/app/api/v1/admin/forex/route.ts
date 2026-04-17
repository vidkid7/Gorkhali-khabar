import { NextRequest, NextResponse } from "next/server";
import { prisma } from "@/lib/prisma";
import { requireRole, unauthorizedResponse, forbiddenResponse } from "@/lib/auth-helpers";

export async function GET() {
  const rates = await prisma.forexRate.findMany({ orderBy: [{ date: "desc" }, { currency: "asc" }], take: 50 });
  return NextResponse.json({ success: true, data: rates });
}

export async function POST(req: NextRequest) {
  const { error } = await requireRole(["ADMIN", "EDITOR"]);
  if (error === "unauthorized") return unauthorizedResponse();
  if (error === "forbidden") return forbiddenResponse();

  try {
    const body = await req.json();
    const rate = await prisma.forexRate.create({
      data: {
        date: new Date(body.date),
        currency: String(body.currency).toUpperCase(),
        currency_name: body.currency_name || null,
        unit: Number(body.unit) || 1,
        buy: body.buy ? Number(body.buy) : null,
        sell: body.sell ? Number(body.sell) : null,
      },
    });
    return NextResponse.json({ success: true, data: rate });
  } catch {
    return NextResponse.json({ success: false, error: "Failed to create forex rate" }, { status: 400 });
  }
}

export async function DELETE(req: NextRequest) {
  const { error } = await requireRole(["ADMIN"]);
  if (error === "unauthorized") return unauthorizedResponse();
  if (error === "forbidden") return forbiddenResponse();

  const id = req.nextUrl.searchParams.get("id");
  if (!id) return NextResponse.json({ success: false, error: "Missing id" }, { status: 400 });
  await prisma.forexRate.delete({ where: { id } });
  return NextResponse.json({ success: true });
}

import { NextRequest, NextResponse } from "next/server";
import { prisma } from "@/lib/prisma";
import { requireRole, unauthorizedResponse, forbiddenResponse } from "@/lib/auth-helpers";

export async function GET() {
  const prices = await prisma.goldSilverPrice.findMany({ orderBy: { date: "desc" }, take: 30 });
  return NextResponse.json({ success: true, data: prices });
}

export async function POST(req: NextRequest) {
  const { error } = await requireRole(["ADMIN", "EDITOR"]);
  if (error === "unauthorized") return unauthorizedResponse();
  if (error === "forbidden") return forbiddenResponse();

  try {
    const body = await req.json();
    const price = await prisma.goldSilverPrice.create({
      data: {
        date: new Date(body.date),
        fine_gold: body.fine_gold ? Number(body.fine_gold) : null,
        tejabi_gold: body.tejabi_gold ? Number(body.tejabi_gold) : null,
        silver: body.silver ? Number(body.silver) : null,
        source: body.source || null,
      },
    });
    return NextResponse.json({ success: true, data: price });
  } catch {
    return NextResponse.json({ success: false, error: "Failed to create price entry" }, { status: 400 });
  }
}

export async function DELETE(req: NextRequest) {
  const { error } = await requireRole(["ADMIN"]);
  if (error === "unauthorized") return unauthorizedResponse();
  if (error === "forbidden") return forbiddenResponse();

  const id = req.nextUrl.searchParams.get("id");
  if (!id) return NextResponse.json({ success: false, error: "Missing id" }, { status: 400 });
  await prisma.goldSilverPrice.delete({ where: { id } });
  return NextResponse.json({ success: true });
}

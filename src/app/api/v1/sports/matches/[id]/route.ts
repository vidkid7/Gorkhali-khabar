import { NextRequest, NextResponse } from "next/server";
import { prisma } from "@/lib/prisma";
import { requireRole, unauthorizedResponse, forbiddenResponse } from "@/lib/auth-helpers";
import { auditLog } from "@/lib/audit";
import type { ApiResponse } from "@/types";
import type { MatchStatus } from "@prisma/client";

const VALID_STATUSES: MatchStatus[] = ["UPCOMING", "LIVE", "COMPLETED", "CANCELLED"];

export async function PUT(
  request: NextRequest,
  { params }: { params: Promise<{ id: string }> }
) {
  try {
    const { error, session } = await requireRole(["ADMIN"]);
    if (error === "unauthorized") return unauthorizedResponse();
    if (error === "forbidden") return forbiddenResponse();

    const { id } = await params;

    const existing = await prisma.match.findUnique({ where: { id } });
    if (!existing) {
      return NextResponse.json<ApiResponse>(
        { success: false, error: "खेल फेला परेन" },
        { status: 404 }
      );
    }

    const body = await request.json();
    const updateData: Record<string, unknown> = {};

    if (body.home_score !== undefined) updateData.home_score = parseInt(body.home_score);
    if (body.away_score !== undefined) updateData.away_score = parseInt(body.away_score);
    if (body.status && VALID_STATUSES.includes(body.status)) updateData.status = body.status;
    if (body.venue !== undefined) updateData.venue = body.venue;
    if (body.match_date) updateData.match_date = new Date(body.match_date);

    const match = await prisma.match.update({
      where: { id },
      data: updateData,
      include: {
        tournament: { select: { id: true, name: true, slug: true } },
        home_team: { select: { id: true, name: true, logo: true } },
        away_team: { select: { id: true, name: true, logo: true } },
      },
    });

    await auditLog({
      adminId: session!.user.id,
      action: "UPDATE",
      entity: "Match",
      entityId: match.id,
      oldValue: { home_score: existing.home_score, away_score: existing.away_score, status: existing.status },
      newValue: { home_score: match.home_score, away_score: match.away_score, status: match.status },
    });

    return NextResponse.json<ApiResponse>({ success: true, data: match });
  } catch (error) {
    console.error("Match PUT error:", error);
    return NextResponse.json<ApiResponse>(
      { success: false, error: "खेल अपडेट गर्दा त्रुटि भयो" },
      { status: 500 }
    );
  }
}

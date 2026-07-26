import { NextRequest, NextResponse } from "next/server";
import { z } from "zod";
import { prisma } from "@/lib/prisma";
import { requireRole, unauthorizedResponse, forbiddenResponse, notFoundResponse } from "@/lib/auth-helpers";
import { auditLog } from "@/lib/audit";
import type { ApiResponse } from "@/types";

const quickLinkUpdateSchema = z.object({
  href: z.string().min(1).max(255).optional(),
  title_ne: z.string().min(1).max(120).optional(),
  title_en: z.string().min(1).max(120).optional(),
  description_ne: z.string().min(1).max(255).optional(),
  description_en: z.string().min(1).max(255).optional(),
  icon_key: z.string().min(1).max(64).optional(),
  accent_color: z
    .string()
    .regex(/^#[0-9a-fA-F]{6}$/)
    .optional(),
  sort_order: z.number().int().min(0).optional(),
  is_active: z.boolean().optional(),
});

interface RouteContext {
  params: Promise<{ id: string }>;
}

// Update a quick link.
export async function PATCH(request: NextRequest, context: RouteContext) {
  const auth = await requireRole(["ADMIN"]);
  if (auth.error) return auth.error === "unauthorized" ? unauthorizedResponse() : forbiddenResponse();

  const { id } = await context.params;

  try {
    const body = await request.json();
    const parsed = quickLinkUpdateSchema.safeParse(body);
    if (!parsed.success) {
      return NextResponse.json<ApiResponse>(
        { success: false, error: parsed.error.issues.map((e) => e.message).join(", ") },
        { status: 400 },
      );
    }

    const existing = await prisma.quickLink.findUnique({ where: { id } });
    if (!existing) return notFoundResponse();

    const link = await prisma.quickLink.update({ where: { id }, data: parsed.data });
    await auditLog({
      adminId: auth.session!.user.id,
      action: "UPDATE",
      entity: "QuickLink",
      entityId: link.id,
      oldValue: existing,
      newValue: link,
    });
    return NextResponse.json<ApiResponse>({ success: true, data: link });
  } catch (error) {
    console.error("Admin quick-links PATCH error:", error);
    return NextResponse.json<ApiResponse>(
      { success: false, error: "लिंक अपडेट गर्दा त्रुटि भयो" },
      { status: 500 },
    );
  }
}

// Delete a quick link.
export async function DELETE(_request: NextRequest, context: RouteContext) {
  const auth = await requireRole(["ADMIN"]);
  if (auth.error) return auth.error === "unauthorized" ? unauthorizedResponse() : forbiddenResponse();

  const { id } = await context.params;

  try {
    const existing = await prisma.quickLink.findUnique({ where: { id } });
    if (!existing) return notFoundResponse();
    await prisma.quickLink.delete({ where: { id } });
    await auditLog({
      adminId: auth.session!.user.id,
      action: "DELETE",
      entity: "QuickLink",
      entityId: id,
      oldValue: existing,
    });
    return NextResponse.json<ApiResponse>({ success: true });
  } catch (error) {
    console.error("Admin quick-links DELETE error:", error);
    return NextResponse.json<ApiResponse>(
      { success: false, error: "लिंक हटाउँदा त्रुटि भयो" },
      { status: 500 },
    );
  }
}


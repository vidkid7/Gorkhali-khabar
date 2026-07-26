import { NextRequest, NextResponse } from "next/server";
import { z } from "zod";
import { prisma } from "@/lib/prisma";
import { requireRole, unauthorizedResponse, forbiddenResponse } from "@/lib/auth-helpers";
import { auditLog } from "@/lib/audit";
import type { ApiResponse } from "@/types";

const quickLinkSchema = z.object({
  slug: z.string().min(1).max(64).regex(/^[a-z0-9-]+$/),
  href: z.string().min(1).max(255),
  title_ne: z.string().min(1).max(120),
  title_en: z.string().min(1).max(120),
  description_ne: z.string().min(1).max(255),
  description_en: z.string().min(1).max(255),
  icon_key: z.string().min(1).max(64),
  accent_color: z
    .string()
    .regex(/^#[0-9a-fA-F]{6}$/)
    .default("#c62828"),
  sort_order: z.number().int().min(0).default(0),
  is_active: z.boolean().default(true),
});

// List quick links (admin).
export async function GET() {
  const auth = await requireRole(["ADMIN", "EDITOR"]);
  if (auth.error) return auth.error === "unauthorized" ? unauthorizedResponse() : forbiddenResponse();

  try {
    const links = await prisma.quickLink.findMany({
      orderBy: [{ sort_order: "asc" }, { created_at: "asc" }],
    });
    return NextResponse.json<ApiResponse<typeof links>>({ success: true, data: links });
  } catch (error) {
    console.error("Admin quick-links GET error:", error);
    return NextResponse.json<ApiResponse>(
      { success: false, error: "लिंकहरू प्राप्त गर्दा त्रुटि भयो" },
      { status: 500 },
    );
  }
}

// Create a quick link (admin).
export async function POST(request: NextRequest) {
  const auth = await requireRole(["ADMIN"]);
  if (auth.error) return auth.error === "unauthorized" ? unauthorizedResponse() : forbiddenResponse();

  try {
    const body = await request.json();
    const parsed = quickLinkSchema.safeParse(body);
    if (!parsed.success) {
      return NextResponse.json<ApiResponse>(
        { success: false, error: parsed.error.issues.map((e) => e.message).join(", ") },
        { status: 400 },
      );
    }
    const link = await prisma.quickLink.upsert({
      where: { slug: parsed.data.slug },
      update: parsed.data,
      create: parsed.data,
    });
    await auditLog({
      adminId: auth.session!.user.id,
      action: "UPDATE",
      entity: "QuickLink",
      entityId: link.id,
      newValue: link,
    });
    return NextResponse.json<ApiResponse>({ success: true, data: link });
  } catch (error) {
    console.error("Admin quick-links POST error:", error);
    return NextResponse.json<ApiResponse>(
      { success: false, error: "लिंक सिर्जना गर्दा त्रुटि भयो" },
      { status: 500 },
    );
  }
}


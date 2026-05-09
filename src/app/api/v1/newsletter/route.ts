import { NextRequest, NextResponse } from "next/server";
import { z } from "zod";
import { prisma } from "@/lib/prisma";
import type { ApiResponse } from "@/types";

export const dynamic = "force-dynamic";

const newsletterSchema = z.object({
  email: z.string().trim().email(),
  language: z.enum(["ne", "en"]).optional().default("ne"),
});

export async function POST(request: NextRequest) {
  try {
    const body = await request.json();
    const parsed = newsletterSchema.safeParse(body);

    if (!parsed.success) {
      return NextResponse.json<ApiResponse>(
        { success: false, error: "Valid email is required" },
        { status: 400 }
      );
    }

    const email = parsed.data.email.toLowerCase();
    const subscription = await prisma.newsletterSubscription.upsert({
      where: { email },
      update: {
        language: parsed.data.language,
        source: "footer",
        is_active: true,
      },
      create: {
        email,
        language: parsed.data.language,
        source: "footer",
      },
    });

    return NextResponse.json<ApiResponse>(
      {
        success: true,
        data: {
          id: subscription.id,
          email: subscription.email,
          is_active: subscription.is_active,
        },
      },
      { status: 201 }
    );
  } catch (error) {
    console.error("Newsletter POST error:", error);
    return NextResponse.json<ApiResponse>(
      { success: false, error: "Failed to save newsletter subscription" },
      { status: 500 }
    );
  }
}

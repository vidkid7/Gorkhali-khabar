import { NextRequest, NextResponse } from "next/server";
import crypto from "crypto";
import { prisma } from "@/lib/prisma";
import { requireAuth, unauthorizedResponse } from "@/lib/auth-helpers";
import { sendEmail, verificationEmailTemplate } from "@/lib/email";
import type { ApiResponse } from "@/types";

export async function POST(request: NextRequest) {
  try {
    const { error, session } = await requireAuth();
    if (error) return unauthorizedResponse();

    const user = await prisma.user.findUnique({
      where: { id: session!.user.id },
    });

    if (!user) {
      return NextResponse.json<ApiResponse>(
        { success: false, error: "प्रयोगकर्ता फेला परेन" },
        { status: 404 }
      );
    }

    if (user.email_verified) {
      return NextResponse.json<ApiResponse>(
        { success: false, error: "इमेल पहिले नै प्रमाणित भइसकेको छ" },
        { status: 400 }
      );
    }

    // Invalidate existing tokens
    await prisma.emailVerificationToken.updateMany({
      where: { userId: user.id, used: false },
      data: { used: true },
    });

    // Generate new token
    const token = crypto.randomBytes(32).toString("hex");
    const token_hash = crypto.createHash("sha256").update(token).digest("hex");

    await prisma.emailVerificationToken.create({
      data: {
        userId: user.id,
        token_hash,
        expires_at: new Date(Date.now() + 24 * 60 * 60 * 1000), // 24 hours
      },
    });

    const verifyUrl = `${process.env.NEXT_PUBLIC_SITE_URL}/api/v1/auth/verify-email?token=${token}`;
    const lang = (user.language as "ne" | "en") || "ne";

    await sendEmail({
      to: user.email,
      subject: lang === "ne" ? "इमेल प्रमाणीकरण" : "Email Verification",
      html: verificationEmailTemplate(user.name || "User", verifyUrl, lang),
    });

    return NextResponse.json<ApiResponse>({
      success: true,
      message: lang === "ne"
        ? "प्रमाणीकरण इमेल पठाइयो"
        : "Verification email sent",
    });
  } catch (error) {
    console.error("Send verification error:", error);
    return NextResponse.json<ApiResponse>(
      { success: false, error: "इमेल पठाउन सकिएन" },
      { status: 500 }
    );
  }
}

import { NextRequest, NextResponse } from "next/server";
import crypto from "crypto";
import { prisma } from "@/lib/prisma";
import { sendEmail, passwordResetEmailTemplate } from "@/lib/email";
import type { ApiResponse } from "@/types";

export async function POST(request: NextRequest) {
  try {
    const { email } = await request.json();

    if (!email) {
      return NextResponse.json<ApiResponse>(
        { success: false, error: "इमेल आवश्यक छ" },
        { status: 400 }
      );
    }

    const user = await prisma.user.findUnique({ where: { email } });

    // Always return success to prevent email enumeration
    if (!user) {
      return NextResponse.json<ApiResponse>({
        success: true,
        message: "यदि इमेल दर्ता छ भने, रिसेट लिंक पठाइनेछ",
      });
    }

    // Invalidate existing tokens
    await prisma.passwordResetToken.updateMany({
      where: { userId: user.id, used: false },
      data: { used: true },
    });

    const token = crypto.randomBytes(32).toString("hex");
    const token_hash = crypto.createHash("sha256").update(token).digest("hex");

    await prisma.passwordResetToken.create({
      data: {
        userId: user.id,
        token_hash,
        expires_at: new Date(Date.now() + 60 * 60 * 1000), // 1 hour
      },
    });

    const resetUrl = `${process.env.NEXT_PUBLIC_SITE_URL}/auth/reset-password?token=${token}`;
    const lang = (user.language as "ne" | "en") || "ne";

    await sendEmail({
      to: user.email,
      subject: lang === "ne" ? "पासवर्ड रिसेट" : "Password Reset",
      html: passwordResetEmailTemplate(user.name || "User", resetUrl, lang),
    });

    return NextResponse.json<ApiResponse>({
      success: true,
      message: "यदि इमेल दर्ता छ भने, रिसेट लिंक पठाइनेछ",
    });
  } catch (error) {
    console.error("Forgot password error:", error);
    return NextResponse.json<ApiResponse>(
      { success: false, error: "त्रुटि भयो" },
      { status: 500 }
    );
  }
}

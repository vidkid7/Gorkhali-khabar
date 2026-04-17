import { NextRequest, NextResponse } from "next/server";
import bcrypt from "bcryptjs";
import { prisma } from "@/lib/prisma";
import { registerSchema } from "@/lib/validations";
import type { ApiResponse } from "@/types";

export async function POST(request: NextRequest) {
  try {
    const body = await request.json();
    const parsed = registerSchema.safeParse(body);

    if (!parsed.success) {
      return NextResponse.json<ApiResponse>(
        { success: false, error: parsed.error.issues[0].message },
        { status: 400 }
      );
    }

    const { name, email, password } = parsed.data;

    const existingUser = await prisma.user.findUnique({
      where: { email },
    });

    if (existingUser) {
      return NextResponse.json<ApiResponse>(
        { success: false, error: "यो इमेल ठेगाना पहिले नै दर्ता भइसकेको छ" },
        { status: 409 }
      );
    }

    const password_hash = await bcrypt.hash(password, 12);

    const user = await prisma.user.create({
      data: {
        name,
        email,
        password_hash,
        role: "READER",
      },
    });

    return NextResponse.json<ApiResponse>(
      {
        success: true,
        data: { id: user.id, name: user.name, email: user.email },
        message: "दर्ता सफल भयो। कृपया आफ्नो इमेल प्रमाणित गर्नुहोस्।",
      },
      { status: 201 }
    );
  } catch (error) {
    console.error("Registration error:", error);
    return NextResponse.json<ApiResponse>(
      { success: false, error: "दर्ता गर्दा त्रुटि भयो" },
      { status: 500 }
    );
  }
}

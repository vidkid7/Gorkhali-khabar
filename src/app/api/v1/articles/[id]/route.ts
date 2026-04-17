import { NextRequest, NextResponse } from "next/server";
import { prisma } from "@/lib/prisma";
import { articleSchema } from "@/lib/validations";
import { requireRole, unauthorizedResponse, forbiddenResponse } from "@/lib/auth-helpers";
import { auditLog } from "@/lib/audit";
import sanitizeHtml from "sanitize-html";
import type { ApiResponse } from "@/types";

export async function GET(
  _request: NextRequest,
  { params }: { params: Promise<{ id: string }> }
) {
  try {
    const { id } = await params;

    const article = await prisma.article.findUnique({
      where: { id },
      include: {
        category: { select: { id: true, name: true, slug: true } },
        author: { select: { id: true, name: true, image: true } },
        tags: { include: { tag: { select: { id: true, name: true, slug: true } } } },
      },
    });

    if (!article) {
      return NextResponse.json<ApiResponse>(
        { success: false, error: "लेख फेला परेन" },
        { status: 404 }
      );
    }

    return NextResponse.json<ApiResponse>({ success: true, data: article });
  } catch (error) {
    console.error("Article GET error:", error);
    return NextResponse.json<ApiResponse>(
      { success: false, error: "लेख प्राप्त गर्दा त्रुटि भयो" },
      { status: 500 }
    );
  }
}

export async function PUT(
  request: NextRequest,
  { params }: { params: Promise<{ id: string }> }
) {
  try {
    const { error, session } = await requireRole(["AUTHOR", "EDITOR", "ADMIN"]);
    if (error === "unauthorized") return unauthorizedResponse();
    if (error === "forbidden") return forbiddenResponse();

    const { id } = await params;

    const existing = await prisma.article.findUnique({ where: { id } });
    if (!existing) {
      return NextResponse.json<ApiResponse>(
        { success: false, error: "लेख फेला परेन" },
        { status: 404 }
      );
    }

    // Authors can only edit their own articles
    if (session!.user.role === "AUTHOR" && existing.author_id !== session!.user.id) {
      return forbiddenResponse();
    }

    const body = await request.json();
    const parsed = articleSchema.partial().safeParse(body);

    if (!parsed.success) {
      return NextResponse.json<ApiResponse>(
        { success: false, error: parsed.error.issues[0].message },
        { status: 400 }
      );
    }

    const { tag_ids, ...updateData } = parsed.data;

    const SANITIZE_CONFIG: sanitizeHtml.IOptions = {
      allowedTags: ['p', 'br', 'strong', 'em', 'u', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
        'ul', 'ol', 'li', 'a', 'img', 'blockquote', 'pre', 'code', 'table', 'thead',
        'tbody', 'tr', 'th', 'td', 'figure', 'figcaption', 'iframe', 'video', 'source', 'div', 'span'],
      allowedAttributes: {
        '*': ['href', 'src', 'alt', 'title', 'class', 'target', 'rel',
          'width', 'height', 'style', 'frameborder', 'allowfullscreen', 'loading'],
      },
    };
    // Sanitize HTML content to prevent XSS
    if (updateData.content) {
      updateData.content = sanitizeHtml(updateData.content, SANITIZE_CONFIG);
    }
    if (updateData.content_en) {
      updateData.content_en = sanitizeHtml(updateData.content_en, SANITIZE_CONFIG);
    }

    if (updateData.content) {
      const wordCount = updateData.content
        .replace(/<[^>]*>/g, "")
        .split(/\s+/)
        .filter(Boolean).length;
      (updateData as Record<string, unknown>).word_count = wordCount;
      (updateData as Record<string, unknown>).reading_time = Math.ceil(wordCount / 200);
    }

    // Set published_at when transitioning to PUBLISHED
    if (updateData.status === "PUBLISHED" && existing.status !== "PUBLISHED") {
      (updateData as Record<string, unknown>).published_at = new Date();
    }

    const article = await prisma.article.update({
      where: { id },
      data: {
        ...updateData,
        ...(tag_ids !== undefined
          ? {
              tags: {
                deleteMany: {},
                create: tag_ids.map((tagId: string) => ({ tag_id: tagId })),
              },
            }
          : {}),
      },
      include: {
        category: { select: { id: true, name: true, slug: true } },
        author: { select: { id: true, name: true, image: true } },
        tags: { include: { tag: { select: { id: true, name: true, slug: true } } } },
      },
    });

    await auditLog({
      adminId: session!.user.id,
      action: "UPDATE",
      entity: "Article",
      entityId: article.id,
      oldValue: { title: existing.title, status: existing.status },
      newValue: { title: article.title, status: article.status },
    });

    return NextResponse.json<ApiResponse>({ success: true, data: article });
  } catch (error) {
    console.error("Article PUT error:", error);
    return NextResponse.json<ApiResponse>(
      { success: false, error: "लेख अपडेट गर्दा त्रुटि भयो" },
      { status: 500 }
    );
  }
}

export async function DELETE(
  _request: NextRequest,
  { params }: { params: Promise<{ id: string }> }
) {
  try {
    const { error, session } = await requireRole(["ADMIN"]);
    if (error === "unauthorized") return unauthorizedResponse();
    if (error === "forbidden") return forbiddenResponse();

    const { id } = await params;

    const existing = await prisma.article.findUnique({ where: { id } });
    if (!existing) {
      return NextResponse.json<ApiResponse>(
        { success: false, error: "लेख फेला परेन" },
        { status: 404 }
      );
    }

    await prisma.article.delete({ where: { id } });

    await auditLog({
      adminId: session!.user.id,
      action: "DELETE",
      entity: "Article",
      entityId: id,
      oldValue: { title: existing.title, slug: existing.slug },
    });

    return NextResponse.json<ApiResponse>({ success: true, data: { id } });
  } catch (error) {
    console.error("Article DELETE error:", error);
    return NextResponse.json<ApiResponse>(
      { success: false, error: "लेख मेटाउँदा त्रुटि भयो" },
      { status: 500 }
    );
  }
}

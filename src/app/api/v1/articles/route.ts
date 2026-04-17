import { NextRequest, NextResponse } from "next/server";
import { prisma } from "@/lib/prisma";
import { articleSchema } from "@/lib/validations";
import { requireRole, unauthorizedResponse, forbiddenResponse } from "@/lib/auth-helpers";
import { auditLog } from "@/lib/audit";
import sanitizeHtml from "sanitize-html";
import type { ApiResponse, PaginatedResponse } from "@/types";
import type { Article } from "@prisma/client";

export async function GET(request: NextRequest) {
  try {
    const { searchParams } = request.nextUrl;
    const page = Math.max(1, parseInt(searchParams.get("page") || "1"));
    const pageSize = Math.min(50, Math.max(1, parseInt(searchParams.get("pageSize") || "10")));
    const category = searchParams.get("category");
    const status = searchParams.get("status");
    const search = searchParams.get("search");
    const skip = (page - 1) * pageSize;

    const where: Record<string, unknown> = {};

    if (category) {
      where.category_id = category;
    }
    if (status) {
      where.status = status;
    } else {
      where.status = "PUBLISHED";
    }
    if (search) {
      where.OR = [
        { title: { contains: search, mode: "insensitive" } },
        { content: { contains: search, mode: "insensitive" } },
        { excerpt: { contains: search, mode: "insensitive" } },
      ];
    }

    const [articles, total] = await Promise.all([
      prisma.article.findMany({
        where,
        skip,
        take: pageSize,
        orderBy: { published_at: "desc" },
        include: {
          category: { select: { id: true, name: true, slug: true } },
          author: { select: { id: true, name: true, image: true } },
          tags: { include: { tag: { select: { id: true, name: true, slug: true } } } },
        },
      }),
      prisma.article.count({ where }),
    ]);

    const result: PaginatedResponse<Article> = {
      data: articles as Article[],
      total,
      page,
      pageSize,
      totalPages: Math.ceil(total / pageSize),
    };

    return NextResponse.json<ApiResponse<PaginatedResponse<Article>>>(
      { success: true, data: result }
    );
  } catch (error) {
    console.error("Articles GET error:", error);
    return NextResponse.json<ApiResponse>(
      { success: false, error: "लेखहरू प्राप्त गर्दा त्रुटि भयो" },
      { status: 500 }
    );
  }
}

export async function POST(request: NextRequest) {
  try {
    const { error, session } = await requireRole(["AUTHOR", "EDITOR", "ADMIN"]);
    if (error === "unauthorized") return unauthorizedResponse();
    if (error === "forbidden") return forbiddenResponse();

    const body = await request.json();
    const parsed = articleSchema.safeParse(body);

    if (!parsed.success) {
      return NextResponse.json<ApiResponse>(
        { success: false, error: parsed.error.issues[0].message },
        { status: 400 }
      );
    }

    const { tag_ids, ...articleData } = parsed.data;

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
    if (articleData.content) {
      articleData.content = sanitizeHtml(articleData.content, SANITIZE_CONFIG);
    }
    if (articleData.content_en) {
      articleData.content_en = sanitizeHtml(articleData.content_en, SANITIZE_CONFIG);
    }

    const wordCount = articleData.content
      .replace(/<[^>]*>/g, "")
      .split(/\s+/)
      .filter(Boolean).length;
    const readingTime = Math.ceil(wordCount / 200);

    const article = await prisma.article.create({
      data: {
        ...articleData,
        word_count: wordCount,
        reading_time: readingTime,
        author_id: session!.user.id,
        published_at: articleData.status === "PUBLISHED" ? new Date() : null,
        tags: tag_ids?.length
          ? { create: tag_ids.map((tagId: string) => ({ tag_id: tagId })) }
          : undefined,
      },
      include: {
        category: { select: { id: true, name: true, slug: true } },
        author: { select: { id: true, name: true, image: true } },
        tags: { include: { tag: { select: { id: true, name: true, slug: true } } } },
      },
    });

    await auditLog({
      adminId: session!.user.id,
      action: "CREATE",
      entity: "Article",
      entityId: article.id,
      newValue: { title: article.title, slug: article.slug, status: article.status },
    });

    return NextResponse.json<ApiResponse>(
      { success: true, data: article },
      { status: 201 }
    );
  } catch (error) {
    console.error("Article POST error:", error);
    return NextResponse.json<ApiResponse>(
      { success: false, error: "लेख सिर्जना गर्दा त्रुटि भयो" },
      { status: 500 }
    );
  }
}

import { Suspense } from "react";
import { notFound } from "next/navigation";
import type { Metadata } from "next";
import prisma from "@/lib/prisma";
import { Header } from "@/components/layout/Header";
import { Footer } from "@/components/layout/Footer";
import { ArticleCard } from "@/components/articles/ArticleCard";
import { ArticleContent } from "@/components/articles/ArticleContent";
import { ArticleCardSkeleton } from "@/components/ui/SkeletonLoader";

export const dynamic = "force-dynamic";

type Props = {
  params: Promise<{ slug: string }>;
};

async function getArticle(slug: string) {
  const article = await prisma.article.findUnique({
    where: { slug, status: "PUBLISHED" },
    include: {
      category: {
        select: { name: true, name_en: true, slug: true, color: true },
      },
      author: { select: { name: true, image: true } },
      tags: { include: { tag: { select: { name: true, name_en: true, slug: true } } } },
    },
  });
  return article;
}

async function getRelatedArticles(categoryId: string, excludeId: string) {
  return prisma.article.findMany({
    where: {
      status: "PUBLISHED",
      category_id: categoryId,
      id: { not: excludeId },
    },
    include: {
      category: { select: { name: true, name_en: true, slug: true, color: true } },
      author: { select: { name: true } },
    },
    orderBy: { published_at: "desc" },
    take: 4,
  });
}

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const { slug } = await params;
  const article = await getArticle(slug);
  if (!article) return { title: "Not Found" };

  return {
    title: article.title,
    description: article.excerpt || undefined,
    openGraph: {
      title: article.title,
      description: article.excerpt || undefined,
      images: article.featured_image ? [article.featured_image] : undefined,
    },
  };
}

async function incrementViewCount(id: string) {
  await prisma.article.update({
    where: { id },
    data: { view_count: { increment: 1 } },
  });
}

async function RelatedArticles({
  categoryId,
  excludeId,
}: {
  categoryId: string;
  excludeId: string;
}) {
  const related = await getRelatedArticles(categoryId, excludeId);
  if (!related.length) return null;

  return (
    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      {related.map((a) => (
        <ArticleCard
          key={a.id}
          slug={a.slug}
          title={a.title}
          title_en={a.title_en}
          featured_image={a.featured_image}
          category={a.category}
          author={a.author}
          reading_time={a.reading_time}
          published_at={a.published_at}
          view_count={a.view_count}
        />
      ))}
    </div>
  );
}

export default async function ArticlePage({ params }: Props) {
  const { slug } = await params;
  const article = await getArticle(slug);

  if (!article) notFound();

  // Fire-and-forget view count increment
  incrementViewCount(article.id).catch(() => {});

  return (
    <>
      <Header />
      <main className="mx-auto max-w-4xl px-3 sm:px-4 py-4 sm:py-6 pb-safe">
        <ArticleContent
          title={article.title}
          title_en={article.title_en}
          content={article.content}
          content_en={article.content_en}
          excerpt={article.excerpt}
          excerpt_en={article.excerpt_en}
          featured_image={article.featured_image}
          ai_summary={article.ai_summary}
          category={article.category}
          author={article.author}
          tags={article.tags.map((at) => at.tag)}
          reading_time={article.reading_time}
          word_count={article.word_count}
          view_count={article.view_count}
          published_at={article.published_at}
          slug={article.slug}
        />

        {/* Related articles */}
        <section className="mt-8 sm:mt-12">
          <Suspense
            fallback={
              <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                {Array.from({ length: 4 }).map((_, i) => (
                  <ArticleCardSkeleton key={i} />
                ))}
              </div>
            }
          >
            <RelatedArticles
              categoryId={article.category_id}
              excludeId={article.id}
            />
          </Suspense>
        </section>
      </main>
      <Footer />
    </>
  );
}

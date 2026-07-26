import { Suspense } from "react";
import { notFound } from "next/navigation";
import type { Metadata } from "next";
import { laravelApi } from "@/lib/api/laravel";
import { Header } from "@/components/layout/Header";
import { Footer } from "@/components/layout/Footer";
import { ArticleCard } from "@/components/articles/ArticleCard";
import { ArticleContent } from "@/components/articles/ArticleContent";
import { ArticleCardSkeleton } from "@/components/ui/SkeletonLoader";
import { sanitizeArticleHtml } from "@/lib/html";
import { canonicalUrl, defaultOpenGraphImage } from "@/lib/seo";
import { decodeArticleSlugParam, publicArticlePath } from "@/lib/public-articles";
import { AdSlot } from "@/components/ads/AdSlot";

export const dynamic = "force-dynamic";

type Props = {
  params: Promise<{ slug: string }>;
};

type ArticleDetail = {
  id: string;
  title: string;
  title_en?: string | null;
  slug: string;
  excerpt?: string | null;
  excerpt_en?: string | null;
  content: string;
  content_en?: string | null;
  featured_image?: string | null;
  ai_summary?: string | null;
  reading_time?: number | null;
  word_count?: number | null;
  view_count: number;
  comment_count?: number;
  published_at?: string | null;
  updated_at?: string | null;
  category_id: string;
  category: { name: string; name_en?: string | null; slug: string; color?: string | null };
  author: { name?: string | null; image?: string | null };
  tags: { name: string; name_en?: string | null; slug: string }[];
};

async function getArticle(slug: string) {
  try {
    return await laravelApi.get<ArticleDetail>(`/api/v1/articles/slug/${encodeURIComponent(slug)}`);
  } catch {
    return null;
  }
}

async function getRelatedArticles(categoryId: string, excludeId: string) {
  try {
    const response = await laravelApi.get<{ data: ArticleDetail[] }>(
      `/api/v1/articles?category=${encodeURIComponent(categoryId)}&pageSize=5`,
    );
    return (response.data ?? []).filter((article) => article.id !== excludeId).slice(0, 4);
  } catch {
    return [];
  }
}

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const { slug } = await params;
  const article = await getArticle(decodeArticleSlugParam(slug));
  if (!article) return { title: "Not Found" };

  const url = canonicalUrl(publicArticlePath(article.slug));
  return {
    title: article.title,
    description: article.excerpt || undefined,
    alternates: { canonical: url },
    openGraph: {
      title: article.title,
      description: article.excerpt || undefined,
      url,
      images: article.featured_image ? [article.featured_image] : [defaultOpenGraphImage()],
    },
    twitter: {
      card: "summary_large_image",
      title: article.title,
      description: article.excerpt || undefined,
      images: article.featured_image ? [article.featured_image] : [defaultOpenGraphImage()],
    },
  };
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
          category={{ ...a.category, color: a.category.color ?? "#07579B" }}
          author={a.author ?? {}}
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
  const article = await getArticle(decodeArticleSlugParam(slug));

  if (!article) notFound();

  return (
    <>
      <Header />
      <main className="mx-auto max-w-7xl px-3 sm:px-4 py-4 sm:py-6 pb-safe">
        <script
          type="application/ld+json"
          dangerouslySetInnerHTML={{
            __html: JSON.stringify({
              "@context": "https://schema.org",
              "@type": "NewsArticle",
              headline: article.title,
              description: article.excerpt,
              image: article.featured_image,
              datePublished: article.published_at ?? undefined,
              dateModified: article.updated_at ?? undefined,
              author: { "@type": "Person", name: article.author.name },
              publisher: {
                "@type": "Organization",
                name: "Gorkhali Khabar",
                logo: { "@type": "ImageObject", url: defaultOpenGraphImage() },
              },
              mainEntityOfPage: { "@type": "WebPage", "@id": `${process.env.NEXT_PUBLIC_SITE_URL || "http://localhost:3000"}${publicArticlePath(article.slug)}` },
              articleSection: article.category.name,
              wordCount: article.word_count,
              timeRequired: `PT${article.reading_time}M`,
            }),
          }}
        />
        <div className="grid min-w-0 gap-8 lg:grid-cols-[minmax(0,1fr)_300px] lg:items-start">
          <ArticleContent
            articleId={article.id}
            title={article.title}
            title_en={article.title_en}
            content={sanitizeArticleHtml(article.content)}
            content_en={article.content_en ? sanitizeArticleHtml(article.content_en) : null}
            excerpt={article.excerpt}
            excerpt_en={article.excerpt_en}
            featured_image={article.featured_image}
            ai_summary={article.ai_summary}
            category={{ ...article.category, color: article.category.color ?? "#07579B" }}
            author={article.author}
            tags={article.tags}
            reading_time={article.reading_time}
            word_count={article.word_count}
            view_count={article.view_count}
            published_at={article.published_at}
            slug={article.slug}
          />
          <aside className="mx-auto w-full max-w-[300px] lg:sticky lg:top-28">
            <AdSlot position="SIDEBAR" compactLabel />
          </aside>
        </div>

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

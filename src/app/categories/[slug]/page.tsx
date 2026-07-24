import { notFound } from "next/navigation";
import type { Metadata } from "next";
import prisma from "@/lib/prisma";
import { Header } from "@/components/layout/Header";
import { Footer } from "@/components/layout/Footer";
import { ArticleCard } from "@/components/articles/ArticleCard";
import { PublicPageHeader } from "@/components/ui/PublicPageHeader";
import { EditorialEmptyState } from "@/components/ui/EditorialEmptyState";
import { ArrowLeft, ArrowRight } from "lucide-react";
import Link from "next/link";
import { canonicalUrl, defaultOpenGraphImage } from "@/lib/seo";

export const dynamic = "force-dynamic";

type Props = {
  params: Promise<{ slug: string }>;
  searchParams: Promise<{ page?: string }>;
};

const PAGE_SIZE = 12;

async function getCategory(slug: string) {
  return prisma.category.findUnique({
    where: { slug, is_active: true },
    select: {
      id: true,
      name: true,
      name_en: true,
      slug: true,
      description: true,
      color: true,
    },
  });
}

async function getCategoryArticles(
  categoryId: string,
  page: number
) {
  const skip = (page - 1) * PAGE_SIZE;
  const [articles, total] = await Promise.all([
    prisma.article.findMany({
      where: { category_id: categoryId, status: "PUBLISHED" },
      orderBy: { published_at: "desc" },
      skip,
      take: PAGE_SIZE,
      include: {
        category: {
          select: { name: true, name_en: true, slug: true, color: true },
        },
        author: { select: { name: true } },
      },
    }),
    prisma.article.count({
      where: { category_id: categoryId, status: "PUBLISHED" },
    }),
  ]);
  return { articles, total, totalPages: Math.ceil(total / PAGE_SIZE) };
}

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const { slug } = await params;
  const category = await getCategory(slug);
  if (!category) return { title: "Not Found" };

  const title = category.name_en
    ? `${category.name} | ${category.name_en}`
    : `${category.name}`;
  const description =
    category.description ||
    `${category.name} सम्बन्धी ताजा समाचार, लेख र अपडेटहरू - Gorkhali Khabar`;
  const url = canonicalUrl(`/categories/${category.slug}`);

  return {
    title,
    description,
    alternates: { canonical: url },
    openGraph: {
      title: category.name,
      description,
      url,
      images: [defaultOpenGraphImage()],
    },
    twitter: {
      card: "summary_large_image",
      title: category.name,
      description,
      images: [defaultOpenGraphImage()],
    },
  };
}

export default async function CategoryArchivePage({
  params,
  searchParams,
}: Props) {
  const { slug } = await params;
  const { page: pageParam } = await searchParams;
  const page = Math.max(1, parseInt(pageParam || "1"));

  const category = await getCategory(slug);
  if (!category) notFound();

  const { articles, total, totalPages } = await getCategoryArticles(
    category.id,
    page
  );

  return (
    <>
      <Header />
      <main className="public-page-shell mx-auto max-w-7xl px-4 py-8 pb-safe">
        <PublicPageHeader
          title={category.name}
          eyebrow={category.name_en || undefined}
          description={category.description}
          countLabel={`${total} ${total === 1 ? "article" : "articles"}`}
          accentColor={category.color}
          breadcrumbs={[{ label: "गृहपृष्ठ", href: "/" }, { label: category.name }]}
        />

        <div className="mt-8">
          {articles.length > 0 ? (
            articles.length >= 4 ? (
              <div className="space-y-8">
                <div className="grid gap-5 lg:grid-cols-[minmax(0,1.35fr)_minmax(18rem,0.8fr)]">
                  <ArticleCard {...articles[0]} variant="hero" />
                  <div className="grid gap-3">{articles.slice(1, 3).map((article) => <ArticleCard key={article.id} {...article} variant="horizontal" />)}</div>
                </div>
                {articles.length > 3 && <div className="grid gap-5 sm:grid-cols-2">{articles.slice(3).map((article) => <ArticleCard key={article.id} {...article} variant="default" />)}</div>}
              </div>
            ) : (
              <div className="divide-y divide-border">{articles.map((article) => <div key={article.id} className="py-3 first:pt-0 last:pb-0"><ArticleCard {...article} variant="horizontal" /></div>)}</div>
            )
          ) : (
            <EditorialEmptyState title="कुनै समाचार भेटिएन" description="अर्को विषय वा पछिल्लो समाचार हेर्नुहोस्।" action={{ label: "गृहपृष्ठमा फर्कनुहोस्", href: "/" }} />
          )}
        </div>

        {/* Pagination */}
        {totalPages > 1 && (
          <div className="flex justify-center items-center gap-2 mt-10">
            {page > 1 && (
              <Link
                href={`/categories/${slug}?page=${page - 1}`}
                className="btn-secondary text-sm"
              >
                <ArrowLeft className="inline h-3.5 w-3.5" /> अघिल्लो
              </Link>
            )}
            <span className="text-sm text-muted px-4">
              {page} / {totalPages}
            </span>
            {page < totalPages && (
              <Link
                href={`/categories/${slug}?page=${page + 1}`}
                className="btn-secondary text-sm"
              >
                अर्को <ArrowRight className="inline h-3.5 w-3.5" />
              </Link>
            )}
          </div>
        )}
      </main>
      <Footer />
    </>
  );
}

"use client";

import { SectionHeader } from "./SectionHeader";
import { ArticleCard } from "@/components/articles/ArticleCard";

interface CategoryArticle {
  id: string;
  slug: string;
  title: string;
  title_en?: string | null;
  excerpt?: string | null;
  excerpt_en?: string | null;
  featured_image?: string | null;
  published_at?: string | Date | null;
  view_count: number;
  reading_time?: number | null;
  comment_count?: number;
  author: { name?: string | null };
  category: { name: string; name_en?: string | null; slug: string; color: string };
}

interface CategorySectionProps {
  sectionKey: string;
  articles: CategoryArticle[];
  color: string;
  slug: string;
  layout?: "grid" | "featured" | "list";
}

export function CategorySection({ sectionKey, articles, color, slug, layout = "featured" }: CategorySectionProps) {
  if (!articles.length) return null;

  const articleCard = (article: CategoryArticle, variant: "default" | "hero" | "horizontal") => (
    <ArticleCard
      key={article.id}
      slug={article.slug}
      title={article.title}
      title_en={article.title_en}
      excerpt={article.excerpt}
      excerpt_en={article.excerpt_en}
      featured_image={article.featured_image}
      category={article.category}
      author={article.author}
      reading_time={article.reading_time}
      published_at={article.published_at}
      view_count={article.view_count}
      comment_count={article.comment_count}
      variant={variant}
    />
  );

  /* ── List layout ── */
  if (layout === "list") {
    return (
      <section>
        <SectionHeader titleKey={sectionKey} color={color} href={`/categories/${slug}`} />
        <div className="divide-y divide-border border-b border-border">
          {articles.map((article) => (
            <div key={article.id} className="py-3 first:pt-0 last:pb-0">
              {articleCard(article, "horizontal")}
            </div>
          ))}
        </div>
      </section>
    );
  }

  /* ── Grid layout ── */
  if (layout === "grid") {
    return (
      <section>
        <SectionHeader titleKey={sectionKey} color={color} href={`/categories/${slug}`} />
        <div className="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
          {articles.map((article, index) => (
            <div key={article.id} className={index === 0 ? "sm:col-span-2" : ""}>
              {articleCard(article, "default")}
            </div>
          ))}
        </div>
      </section>
    );
  }

  const [main, ...rest] = articles;
  return (
    <section>
      <SectionHeader titleKey={sectionKey} color={color} href={`/categories/${slug}`} />
      <div className="grid grid-cols-1 gap-7 lg:grid-cols-[minmax(0,1.45fr)_minmax(20rem,0.75fr)]">
        {articleCard(main, "hero")}
        <div className="grid content-start gap-3">
          {rest.map((article) => articleCard(article, "horizontal"))}
        </div>
      </div>
    </section>
  );
}

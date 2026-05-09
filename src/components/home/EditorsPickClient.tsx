"use client";

import { useLanguage } from "@/contexts/LanguageContext";
import { ArticleCard } from "@/components/articles/ArticleCard";

type Article = {
  id: string;
  slug: string;
  title: string;
  title_en: string | null;
  excerpt: string | null;
  excerpt_en: string | null;
  featured_image: string | null;
  reading_time: number | null;
  published_at: Date | null;
  view_count: number;
  comment_count: number;
  category: { name: string; name_en: string | null; slug: string; color: string } | null;
  author: { name: string | null } | null;
};

export function EditorsPickClient({ articles }: { articles: Article[] }) {
  const { t } = useLanguage();

  return (
    <section className="space-y-4">
      <div className="flex items-center gap-3">
        <div className="h-8 w-1 rounded-full bg-accent" />
        <h2 className="text-xl font-black text-foreground sm:text-2xl">{t("sections.editorsPick")}</h2>
      </div>
      <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        {articles.map((article) => (
          <ArticleCard
            key={article.id}
            slug={article.slug}
            title={article.title}
            title_en={article.title_en}
            excerpt={article.excerpt}
            excerpt_en={article.excerpt_en}
            featured_image={article.featured_image}
            category={article.category ?? { name: "", slug: "", color: "" }}
            author={article.author ?? {}}
            reading_time={article.reading_time}
            published_at={article.published_at}
            view_count={article.view_count}
            comment_count={article.comment_count}
          />
        ))}
      </div>
    </section>
  );
}

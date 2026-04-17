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
    <section className="bg-surface rounded-xl p-8 border border-border">
      <div className="flex items-center gap-3 mb-6">
        <div className="w-1 h-8 bg-accent rounded-full" />
        <h2 className="text-2xl font-bold text-foreground">{t("sections.editorsPick")}</h2>
      </div>
      <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
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

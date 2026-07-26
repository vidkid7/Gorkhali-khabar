"use client";

import type { HomeArticle } from "@/lib/home-api";
import { ArticleCard } from "@/components/articles/ArticleCard";
import { SectionHeader } from "./SectionHeader";

function ArticleGrid({ titleKey, color, articles }: { titleKey: string; color: string; articles: HomeArticle[] }) {
  if (!articles.length) return null;
  return (
    <section className="space-y-4">
      <SectionHeader titleKey={titleKey} color={color} />
      <div className="grid gap-4 sm:grid-cols-2">
        {articles.slice(0, 4).map((article) => (
          <ArticleCard key={article.id} {...article} category={article.category ?? { name: "समाचार", slug: "samachar", color }} author={article.author ?? {}} />
        ))}
      </div>
    </section>
  );
}

export function EditorialFeatureStrip({ editorPicks, opinion }: { editorPicks: HomeArticle[]; opinion: HomeArticle[] }) {
  if (!editorPicks.length && !opinion.length) return null;
  return (
    <div className="grid gap-8 lg:grid-cols-2">
      <ArticleGrid titleKey="sections.editorsPick" color="#c62828" articles={editorPicks} />
      <ArticleGrid titleKey="sections.opinion" color="#4e342e" articles={opinion} />
    </div>
  );
}

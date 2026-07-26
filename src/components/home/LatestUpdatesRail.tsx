"use client";

import { ArrowRight } from "lucide-react";
import Link from "next/link";
import { useLanguage } from "@/contexts/LanguageContext";
import { ArticleCard } from "@/components/articles/ArticleCard";
import { SectionHeader } from "./SectionHeader";
import type { HomeArticle } from "@/lib/home-api";

export function LatestUpdatesRail({ articles }: { articles: HomeArticle[] }) {
  const { language } = useLanguage();
  if (!articles.length) return null;
  return (
    <section className="space-y-4" aria-labelledby="latest-updates-heading">
      <div className="flex items-end justify-between gap-3">
        <SectionHeader titleKey="sections.latestNews" color="#07579B" />
        <Link href="/categories/samachar" className="inline-flex items-center gap-1 text-xs font-bold text-primary hover:underline">
          {language === "ne" ? "सबै अपडेट" : "All updates"} <ArrowRight className="h-3.5 w-3.5" />
        </Link>
      </div>
      <div className="grid gap-3 md:grid-cols-2 xl:grid-cols-4">
        {articles.slice(0, 8).map((article) => (
          <ArticleCard key={article.id} {...article} category={article.category ?? { name: "समाचार", slug: "samachar", color: "#07579B" }} author={article.author ?? {}} variant="horizontal" />
        ))}
      </div>
    </section>
  );
}

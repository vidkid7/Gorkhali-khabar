"use client";

import { useLanguage } from "@/contexts/LanguageContext";
import Link from "next/link";

interface BreakingNewsItem {
  id: string;
  title: string;
  title_en?: string | null;
  article?: { slug: string } | null;
}

export function BreakingNewsTicker({ items, label = "ब्रेकिङ" }: { items: BreakingNewsItem[]; label?: string }) {
  const { language, t } = useLanguage();

  if (!items.length) return null;

  return (
    <div
      className="relative flex items-center overflow-hidden bg-accent text-white"
      role="marquee"
      aria-live="polite"
      aria-label={t("article.breakingNews")}
    >
      <span className="shrink-0 z-10 px-3 py-2 font-bold text-sm bg-accent-hover whitespace-nowrap">
        {label}
      </span>
      <div className="overflow-hidden flex-1">
        <div className="flex animate-marquee whitespace-nowrap py-2 hover:[animation-play-state:paused]">
          {items.concat(items).map((item, idx) => {
            const title =
              language === "en" && item.title_en ? item.title_en : item.title;
            const inner = (
              <span className="mx-8 text-sm font-medium">{title}</span>
            );
            return item.article ? (
              <Link
                key={`${item.id}-${idx}`}
                href={`/articles/${item.article.slug}`}
                className="hover:underline"
              >
                {inner}
              </Link>
            ) : (
              <span key={`${item.id}-${idx}`}>{inner}</span>
            );
          })}
        </div>
      </div>
    </div>
  );
}

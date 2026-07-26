"use client";

import { useLanguage } from "@/contexts/LanguageContext";
import Link from "next/link";
import { publicArticlePath } from "@/lib/public-articles";

interface BreakingNewsItem {
  id: string;
  title: string;
  title_en?: string | null;
  article?: { slug: string } | null;
}

export function BreakingNewsTicker({ items, label = "ब्रेकिङ" }: { items: BreakingNewsItem[]; label?: string }) {
  const { language, t } = useLanguage();

  if (!items.length) return null;

  const renderItems = (sequence: number) =>
    items.map((item, index) => {
      const title =
        language === "en" && item.title_en ? item.title_en : item.title;
      const inner = (
        <span className="flex items-center gap-2 text-sm font-medium">
          <span className="h-1 w-1 rounded-full bg-white/50" />
          {title}
        </span>
      );

      return item.article ? (
        <Link
          key={`${sequence}-${item.id}-${index}`}
          href={publicArticlePath(item.article.slug)}
          className="whitespace-nowrap hover:underline focus-visible:underline focus-visible:outline-none"
        >
          {inner}
        </Link>
      ) : (
        <span key={`${sequence}-${item.id}-${index}`} className="whitespace-nowrap">
          {inner}
        </span>
      );
    });

  return (
    <div
      className="relative flex items-center overflow-hidden bg-accent text-white"
      role="marquee"
      aria-live="polite"
      aria-label={t("article.breakingNews")}
    >
      <span
        data-testid="breaking-label"
        className="z-10 flex shrink-0 items-center gap-1.5 whitespace-nowrap bg-accent-hover px-4 py-2.5 text-sm font-bold"
      >
        <span className="relative flex h-2 w-2">
          <span className="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-70" />
          <span className="relative inline-flex rounded-full h-2 w-2 bg-white" />
        </span>
        {label}
      </span>
      <div className="relative flex-1 min-w-0 overflow-hidden">
        <div
          data-testid="breaking-track"
          className="breaking-news-track py-2.5"
        >
          <div className="breaking-news-sequence">{renderItems(0)}</div>
          <div className="breaking-news-sequence" aria-hidden="true">{renderItems(1)}</div>
        </div>
      </div>
    </div>
  );
}

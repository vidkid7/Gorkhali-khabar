"use client";

import Link from "next/link";
import { ArrowRight } from "lucide-react";
import { useLanguage, toNepaliDigits } from "@/contexts/LanguageContext";
import { publicArticlePath } from "@/lib/public-articles";
import { timeAgo } from "@/lib/utils";
import { getCategoryColorWithFallback } from "@/lib/utils";

interface BriefArticle {
  id: string;
  slug: string;
  title: string;
  title_en?: string | null;
  published_at?: string | Date | null;
  category: { name: string; name_en?: string | null; slug: string; color: string };
}

export function DailyBrief({ articles }: { articles: BriefArticle[] }) {
  const { language } = useLanguage();
  if (!articles.length) return null;

  const title = (article: BriefArticle) =>
    language === "en" && article.title_en ? article.title_en : article.title;
  const category = (article: BriefArticle) =>
    language === "en" && article.category.name_en
      ? article.category.name_en
      : article.category.name;

  return (
    <section
      id="daily-brief"
      className="scroll-mt-28"
    >
      <div className="flex items-center justify-between gap-4 border-b border-border pb-2.5">
        <div className="flex items-center gap-2">
          {/* Animated live dot */}
          <span className="relative flex h-2.5 w-2.5">
            <span className="animate-ping absolute inline-flex h-full w-full rounded-full bg-accent opacity-60" />
            <span className="relative inline-flex rounded-full h-2.5 w-2.5 bg-accent" />
          </span>
          <h2
            className="text-base font-bold text-foreground"
            style={{ fontFamily: "var(--font-nepali-serif)" }}
          >
            {language === "ne" ? "ताजा समाचार" : "Latest News"}
          </h2>
        </div>
        <Link
          href="/categories/samachar"
          className="text-[11px] font-bold text-accent hover:underline shrink-0 inline-flex items-center gap-1"
        >
          {language === "ne" ? "सबै" : "See all"}
          <ArrowRight className="inline h-3.5 w-3.5" />
        </Link>
      </div>

      <ol className="divide-y divide-border">
        {articles.slice(0, 5).map((article, index) => (
          <li key={article.id}>
            <Link
              href={publicArticlePath(article.slug)}
              className="group grid grid-cols-[2rem_minmax(0,1fr)_auto] items-center gap-3 py-3 transition-colors hover:text-accent"
            >
              <span
                className="flex h-6 w-6 flex-shrink-0 items-center justify-center text-xs font-bold"
                style={{
                  color: index === 0 ? "var(--accent)" : "var(--muted)",
                }}
              >
                {language === "ne" ? toNepaliDigits(index + 1) : index + 1}
              </span>

              <span className="min-w-0 flex-1">
                <span className="flex items-center gap-1.5 mb-0.5">
                  <span
                    className="inline-block h-1.5 w-1.5 rounded-full shrink-0"
                    style={{ backgroundColor: getCategoryColorWithFallback(article.category.slug, article.category.color) }}
                  />
                  <span className="text-[10px] font-semibold" style={{ color: getCategoryColorWithFallback(article.category.slug, article.category.color) }}>
                    {category(article)}
                  </span>
                  {article.published_at && (
                    <>
                      <span className="text-muted-foreground text-[10px]">·</span>
                      <span className="text-[10px] text-muted">
                        {timeAgo(new Date(article.published_at), language)}
                      </span>
                    </>
                  )}
                </span>
                <span
                  className="line-clamp-2 text-sm font-semibold text-foreground transition-colors group-hover:text-accent"
                  style={{ fontFamily: "var(--font-nepali-serif)", lineHeight: "1.6", paddingTop: "0.05em" }}
                >
                  {title(article)}
                </span>
              </span>
              <ArrowRight className="h-4 w-4 text-muted transition-colors group-hover:text-accent" aria-hidden="true" />
            </Link>
          </li>
        ))}
      </ol>
    </section>
  );
}

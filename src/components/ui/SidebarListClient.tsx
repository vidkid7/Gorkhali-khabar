"use client";

import Link from "next/link";
import { MessageSquare, TrendingUp } from "lucide-react";
import { useLanguage, toNepaliDigits } from "@/contexts/LanguageContext";
import { publicArticlePath } from "@/lib/public-articles";

interface SidebarArticle {
  id: string;
  slug: string;
  title: string;
  title_en?: string | null;
  view_count: number;
  comment_count: number;
  category: {
    name: string;
    name_en?: string | null;
    slug: string;
    color: string;
  };
}

function formatNumber(value: number, language: "ne" | "en"): string {
  const safe = value || 0;
  if (language === "ne") return toNepaliDigits(safe);
  if (safe >= 1000) return `${(safe / 1000).toFixed(1)}k`;
  return safe.toLocaleString("en-US");
}

export function SidebarListClient({
  titleKey,
  articles,
  icon,
}: {
  titleKey: string;
  articles: SidebarArticle[];
  icon?: "trending" | "comments";
}) {
  const { language, t } = useLanguage();
  const isNe = language === "ne";

  if (!articles.length) return null;

  const Icon = icon === "comments" ? MessageSquare : TrendingUp;
  const rankColorFor = (idx: number, catColor: string): string => {
    if (idx === 0) return "#d60000";
    if (idx === 1) return "#07579b";
    if (idx === 2) return "#2e7d32";
    return catColor;
  };

  const metaLabel =
    icon === "comments"
      ? isNe ? "टिप्पणीहरू" : "Comments"
      : isNe ? "हेराइ" : "Views";

  return (
    <section
      className="rounded-xl border border-border bg-surface shadow-sm overflow-hidden"
      aria-label={t(titleKey)}
    >
      {/* Header */}
      <header className="flex items-center gap-2.5 border-b border-border bg-surface-alt/40 px-4 py-3">
        <span
          className="grid h-7 w-7 shrink-0 place-items-center rounded-md"
          style={{ background: "var(--accent-light)", color: "var(--accent)" }}
          aria-hidden="true"
        >
          <Icon className="h-3.5 w-3.5" strokeWidth={2.5} />
        </span>
        <h3
          className="flex-1 text-[15px] font-bold leading-none text-foreground"
          style={{ fontFamily: "var(--font-nepali-serif)" }}
        >
          {t(titleKey)}
        </h3>
        <span className="text-[10px] font-bold uppercase tracking-[0.16em] text-muted-foreground">
          #{isNe ? toNepaliDigits(articles.length) : articles.length}
        </span>
      </header>

      {/* Article list */}
      <ol className="divide-y divide-border">
        {articles.map((a, idx) => {
          const title = isNe && a.title_en ? a.title_en : a.title;
          const num = isNe ? toNepaliDigits(idx + 1) : idx + 1;
          const catName = isNe && a.category.name_en ? a.category.name_en : a.category.name;
          const metric =
            icon === "comments"
              ? a.comment_count || 0
              : a.view_count || 0;
          const rankColor = rankColorFor(idx, a.category.color);

          return (
            <li key={a.id}>
              <Link
                href={publicArticlePath(a.slug)}
                className="group relative flex gap-3 px-4 py-3 transition-colors hover:bg-surface-alt focus-visible:outline-2 focus-visible:outline-offset-[-2px] focus-visible:outline-accent"
              >
                {/* Rank number */}
                <span
                  aria-hidden="true"
                  className="flex h-7 w-7 shrink-0 items-center justify-center self-start rounded-md text-[12px] font-black tabular-nums"
                  style={{
                    background: `${rankColor}14`,
                    color: rankColor,
                  }}
                >
                  {num}
                </span>

                {/* Text */}
                <div className="min-w-0 flex-1">
                  <div className="mb-1 flex items-center gap-1.5">
                    <span
                      className="inline-block h-0.5 w-3 shrink-0 rounded-full"
                      style={{ background: a.category.color }}
                      aria-hidden="true"
                    />
                    <span className="truncate text-[10px] font-bold uppercase tracking-[0.14em] text-muted-foreground">
                      {catName}
                    </span>
                  </div>
                  <h4
                    className="line-clamp-2 text-sm font-bold leading-[1.45] transition-colors group-hover:text-accent"
                    style={{
                      fontFamily: "var(--font-nepali-serif)",
                      paddingTop: "0.05em",
                    }}
                  >
                    {title}
                  </h4>
                  <div className="mt-1 flex items-center gap-1 text-[10px] font-semibold uppercase tracking-wider text-muted-foreground">
                    <span>{metaLabel}</span>
                    <span className="text-foreground/60">·</span>
                    <span
                      className="font-bold tabular-nums"
                      style={{ color: rankColor }}
                    >
                      {formatNumber(metric, language)}
                    </span>
                  </div>
                </div>
              </Link>
            </li>
          );
        })}
      </ol>
    </section>
  );
}

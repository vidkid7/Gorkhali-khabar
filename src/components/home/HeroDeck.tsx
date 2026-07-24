"use client";

import Link from "next/link";
import { useMemo, useState } from "react";
import { ArrowRight, Clock3, Eye, ImageIcon, MessageSquare, UserRound } from "lucide-react";
import { useLanguage, toNepaliDigits } from "@/contexts/LanguageContext";
import { timeAgo } from "@/lib/utils";
import { getCategoryColorWithFallback } from "@/lib/utils";
import { publicArticlePath } from "@/lib/public-articles";
import { ImageWithFallback } from "@/components/ui/ImageWithFallback";

interface HeroArticle {
  id: string;
  slug: string;
  title: string;
  title_en?: string | null;
  excerpt?: string | null;
  excerpt_en?: string | null;
  featured_image?: string | null;
  published_at?: string | Date | null;
  view_count?: number;
  comment_count?: number;
  category: { name: string; name_en?: string | null; slug: string; color: string };
  author: { name?: string | null };
}

function numberFor(value: number | undefined, language: "ne" | "en") {
  const safeValue = value ?? 0;
  return language === "ne" ? toNepaliDigits(safeValue) : safeValue.toLocaleString("en-US");
}

export function HeroDeck({ articles }: { articles: HeroArticle[] }) {
  const { language } = useLanguage();
  const [activeIndex, setActiveIndex] = useState(0);
  const active = articles[activeIndex] ?? articles[0];
  const remaining = useMemo(
    () => articles.filter((_, index) => index !== activeIndex),
    [articles, activeIndex],
  );

  if (!active) return null;

  const title = (article: HeroArticle) =>
    language === "en" && article.title_en ? article.title_en : article.title;
  const excerpt =
    language === "en" && active.excerpt_en ? active.excerpt_en : active.excerpt;
  const category =
    language === "en" && active.category.name_en
      ? active.category.name_en
      : active.category.name;
  const supportStories = remaining.slice(0, 2);
  const rankedStories = remaining.slice(2, 7);

  const leadKicker = language === "ne" ? "मुख्य कथा" : "Lead Story";
  const moreHeadlines = language === "ne" ? "थप समाचार" : "More Headlines";

  return (
    <section className="grid w-full min-w-0 gap-7 lg:grid-cols-[minmax(0,1.7fr)_minmax(18rem,0.7fr)]">
      <article className="group relative aspect-[4/3] min-w-0 overflow-hidden bg-surface-alt sm:aspect-[3/2]">
        {active.featured_image ? (
          <ImageWithFallback
            src={active.featured_image}
            alt={title(active)}
            fill
            priority
            sizes="(max-width: 1024px) 100vw, 65vw"
            className="object-cover transition-transform duration-700 group-hover:scale-[1.03]"
          />
        ) : (
          <div className="flex h-full items-center justify-center bg-surface-alt">
            <ImageIcon className="h-16 w-16 text-muted" aria-hidden="true" />
          </div>
        )}
        {/* Soft, multi-stop editorial overlay. Top gradient gives breathing room for any future overlay UI;
            the dominant lower gradient keeps white headline type fully legible. */}
        <div className="pointer-events-none absolute inset-0 bg-gradient-to-t from-black/85 via-black/55 to-black/0" />
        <div className="pointer-events-none absolute inset-x-0 bottom-0 h-2/3 bg-gradient-to-t from-black/70 to-transparent" />
        <div className="absolute inset-x-0 bottom-0 p-5 text-white sm:p-8 lg:p-10">
          <div className="flex items-center gap-3">
            <span className="editorial-kicker !bg-white !text-foreground dark:!bg-white">
              {leadKicker}
            </span>
            <span className="inline-flex items-center gap-2 text-[11px] font-bold uppercase tracking-wider text-white/85">
              <span
                className="h-0.5 w-5"
                style={{ backgroundColor: getCategoryColorWithFallback(active.category.slug, active.category.color) }}
              />
              {category}
            </span>
          </div>
          <h1
            className="mt-3 max-w-3xl text-[1.75rem] font-black leading-[1.5] sm:text-[2.5rem] lg:text-[3rem]"
            style={{ fontFamily: "var(--font-nepali-serif)" }}
          >
            <Link
              href={publicArticlePath(active.slug)}
              className="underline-offset-4 hover:underline"
            >
              {title(active)}
            </Link>
          </h1>
          {excerpt && (
            <p className="mt-3 max-w-2xl line-clamp-2 text-[0.95rem] leading-relaxed text-white/80 sm:text-base">
              {excerpt}
            </p>
          )}
          <div className="mt-5 flex min-h-6 flex-wrap items-center gap-x-4 gap-y-2 border-t border-white/15 pt-4 text-xs font-medium text-white/70">
            {active.author.name && (
              <span className="flex items-center gap-1.5">
                <UserRound className="h-3.5 w-3.5" aria-hidden="true" />
                {active.author.name}
              </span>
            )}
            {active.published_at && (
              <span className="flex items-center gap-1.5">
                <Clock3 className="h-3.5 w-3.5" aria-hidden="true" />
                {timeAgo(new Date(active.published_at), language)}
              </span>
            )}
            {active.view_count ? (
              <span className="flex items-center gap-1.5">
                <Eye className="h-3.5 w-3.5" aria-hidden="true" />
                {numberFor(active.view_count, language)}
              </span>
            ) : null}
            {active.comment_count ? (
              <span className="flex items-center gap-1.5">
                <MessageSquare className="h-3.5 w-3.5" aria-hidden="true" />
                {numberFor(active.comment_count, language)}
              </span>
            ) : null}
          </div>
        </div>
      </article>

      <div className="grid min-w-0 content-start gap-6">
        <div className="grid min-w-0 grid-cols-2 gap-5 border-b border-border pb-5">
          {supportStories.map((article) => {
            const articleIndex = articles.findIndex(
              (candidate) => candidate.id === article.id,
            );
            const articleCategory =
              language === "en" && article.category.name_en
                ? article.category.name_en
                : article.category.name;

            return (
              <article
                key={article.id}
                className="group min-w-0 overflow-hidden"
              >
                <div className="relative aspect-[16/10] overflow-hidden bg-surface-alt">
                  {article.featured_image ? (
                    <ImageWithFallback
                      src={article.featured_image}
                      alt={title(article)}
                      fill
                      sizes="(max-width: 640px) 50vw, 18vw"
                      className="object-cover transition-transform duration-500 group-hover:scale-[1.03]"
                    />
                  ) : (
                    <div className="flex h-full items-center justify-center">
                      <ImageIcon className="h-8 w-8 text-muted" aria-hidden="true" />
                    </div>
                  )}
                  <button
                    type="button"
                    onClick={() => setActiveIndex(articleIndex)}
                    aria-pressed={articleIndex === activeIndex}
                    aria-label={`${language === "ne" ? "मुख्य समाचारमा देखाउनुहोस्" : "Show as lead story"}: ${title(article)}`}
                    className="absolute right-2 top-2 flex h-7 w-7 items-center justify-center rounded-full bg-black/55 text-[11px] font-bold text-white ring-1 ring-white/30 backdrop-blur-sm transition-colors hover:bg-primary focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white"
                  >
                    {language === "ne"
                      ? toNepaliDigits(articleIndex + 1)
                      : articleIndex + 1}
                  </button>
                </div>
                <div className="px-1 pt-3">
                  <span className="flex items-center gap-2 text-[10px] font-bold uppercase tracking-wider text-muted-foreground">
                    <span
                      className="h-0.5 w-5"
                      style={{ backgroundColor: getCategoryColorWithFallback(article.category.slug, article.category.color) }}
                    />
                    {articleCategory}
                  </span>
                  <h2
                    className="mt-1.5 line-clamp-3 text-sm font-bold leading-[1.55] transition-colors group-hover:text-accent"
                    style={{ fontFamily: "var(--font-nepali-serif)" }}
                  >
                    <Link href={publicArticlePath(article.slug)}>
                      {title(article)}
                    </Link>
                  </h2>
                </div>
              </article>
            );
          })}
        </div>

        {rankedStories.length > 0 && (
          <div>
            <h3 className="mb-2 flex items-center gap-2 text-[11px] font-bold uppercase tracking-[0.18em] text-muted-foreground">
              <span className="h-px flex-1 bg-border" />
              <span>{moreHeadlines}</span>
              <span className="h-px flex-1 bg-border" />
            </h3>
            <ol className="divide-y divide-border border-y border-border">
              {rankedStories.map((article, index) => (
                <li key={article.id}>
                  <Link
                    href={publicArticlePath(article.slug)}
                    className="group/row grid grid-cols-[2rem_minmax(0,1fr)_auto] items-center gap-3 py-3"
                  >
                    <span
                      className="text-[0.95rem] font-black tabular-nums leading-none"
                      style={{ color: index < 3 ? "var(--accent)" : "var(--primary)" }}
                    >
                      {language === "ne" ? toNepaliDigits(index + 1) : index + 1}
                    </span>
                    <span className="grid min-w-0 gap-1">
                      <span className="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider text-muted-foreground">
                        <span
                          className="h-1.5 w-1.5 rounded-full"
                          style={{ backgroundColor: getCategoryColorWithFallback(article.category.slug, article.category.color) }}
                        />
                        <span className="truncate">
                          {language === "en" && article.category.name_en
                            ? article.category.name_en
                            : article.category.name}
                        </span>
                      </span>
                      <span
                        className="line-clamp-2 text-sm font-semibold leading-[1.5] transition-colors group-hover/row:text-accent"
                        style={{ fontFamily: "var(--font-nepali-serif)" }}
                      >
                        {title(article)}
                      </span>
                    </span>
                    <ArrowRight
                      className="h-4 w-4 text-muted transition-all group-hover/row:translate-x-0.5 group-hover/row:text-accent"
                      aria-hidden="true"
                    />
                  </Link>
                </li>
              ))}
            </ol>
          </div>
        )}

        {articles.length > 1 && (
          <div
            className="flex items-center gap-2"
            aria-label={language === "ne" ? "मुख्य समाचार चयन" : "Lead story selection"}
          >
            <span className="text-[10px] font-bold uppercase tracking-[0.18em] text-muted-foreground">
              {language === "ne" ? "चयन" : "Pick"}
            </span>
            <div className="flex items-center gap-1.5">
              {articles.slice(0, 7).map((article, index) => (
                <button
                  key={article.id}
                  type="button"
                  onClick={() => setActiveIndex(index)}
                  aria-pressed={index === activeIndex}
                  aria-label={`${language === "ne" ? "समाचार" : "Story"} ${index + 1}`}
                  className={`h-1.5 rounded-full bg-primary transition-[width,opacity] ${
                    index === activeIndex
                      ? "w-10 opacity-100"
                      : "w-2 opacity-30 hover:opacity-70"
                  }`}
                />
              ))}
            </div>
          </div>
        )}
      </div>
    </section>
  );
}

"use client";

import Link from "next/link";
import Image from "next/image";
import { useMemo, useState } from "react";
import { useLanguage, toNepaliDigits } from "@/contexts/LanguageContext";
import { timeAgo } from "@/lib/utils";

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

function displayNumber(value: number | undefined, language: "ne" | "en") {
  const safe = value ?? 0;
  return language === "ne" ? toNepaliDigits(safe) : safe.toLocaleString("en-US");
}

export function HeroDeck({ articles }: { articles: HeroArticle[] }) {
  const { language } = useLanguage();
  const [activeIndex, setActiveIndex] = useState(0);
  const active = articles[activeIndex] ?? articles[0];
  const rest = useMemo(() => articles.filter((_, index) => index !== activeIndex).slice(0, 4), [articles, activeIndex]);

  if (!active) return null;

  const title = (article: HeroArticle) => language === "en" && article.title_en ? article.title_en : article.title;
  const excerpt = (article: HeroArticle) => language === "en" && article.excerpt_en ? article.excerpt_en : article.excerpt;
  const category = (article: HeroArticle) => language === "en" && article.category.name_en ? article.category.name_en : article.category.name;

  return (
    <section className="w-full min-w-0">
      <div className="grid w-full min-w-0 gap-4 lg:grid-cols-[minmax(0,1.35fr)_minmax(320px,0.65fr)] lg:gap-6">
        <article className="relative min-h-[25rem] overflow-hidden rounded-3xl border border-black/10 bg-slate-900 shadow-xl sm:min-h-[31rem]">
          {active.featured_image ? (
            <Image
              src={active.featured_image}
              alt={title(active)}
              fill
              className="object-cover"
              sizes="(max-width: 1024px) 100vw, 60vw"
              priority
            />
          ) : (
            <div className="absolute inset-0 bg-gradient-to-br from-blue-900 via-slate-900 to-red-900" />
          )}
          <div className="absolute inset-0 bg-gradient-to-t from-black/90 via-black/35 to-black/5" />

          <div className="absolute left-0 right-0 top-0 flex items-center justify-between gap-3 p-4">
            <span className="rounded-full bg-white/90 px-3 py-1 text-[11px] font-extrabold text-red-700 shadow-sm">
              {language === "ne" ? "मुख्य समाचार" : "Top story"}
            </span>
            <span className="rounded-full bg-black/35 px-3 py-1 text-[11px] font-bold text-white backdrop-blur">
              {active.published_at ? timeAgo(new Date(active.published_at), language) : ""}
            </span>
          </div>

          <Link href={`/articles/${active.slug}`} className="absolute inset-x-0 bottom-0 block p-5 text-white sm:p-7">
            <span
              className="inline-flex rounded-md px-2.5 py-1 text-[11px] font-extrabold"
              style={{ backgroundColor: active.category.color }}
            >
              {category(active)}
            </span>
            <h1 className="mt-3 max-w-3xl text-2xl font-black leading-tight sm:text-4xl">
              {title(active)}
            </h1>
            {excerpt(active) && (
              <p className="mt-3 max-w-2xl text-sm font-medium leading-relaxed text-white/82 sm:text-base">
                {excerpt(active)}
              </p>
            )}
            <div className="mt-4 flex flex-wrap items-center gap-3 text-xs font-semibold text-white/75">
              {active.author.name && <span>{active.author.name}</span>}
              <span>{displayNumber(active.comment_count, language)} {language === "ne" ? "प्रतिक्रिया" : "comments"}</span>
              <span>{displayNumber(active.view_count, language)} {language === "ne" ? "पढाइ" : "reads"}</span>
            </div>
          </Link>

          <div className="absolute bottom-4 right-4 hidden gap-1.5 sm:flex">
            {articles.slice(0, 5).map((article, index) => (
              <button
                key={article.id}
                onClick={() => setActiveIndex(index)}
                className={`h-2 rounded-full transition-all ${index === activeIndex ? "w-8 bg-white" : "w-2 bg-white/45"}`}
                aria-label={`${language === "ne" ? "समाचार" : "Story"} ${index + 1}`}
              />
            ))}
          </div>
        </article>

        <div className="grid min-w-0 gap-3 sm:grid-cols-2 lg:grid-cols-1">
          {rest.map((article, index) => (
            <button
              key={article.id}
              type="button"
              onClick={() => setActiveIndex(articles.findIndex((candidate) => candidate.id === article.id))}
              className="group grid min-w-0 grid-cols-[7.5rem_minmax(0,1fr)] overflow-hidden rounded-2xl border border-border bg-surface text-left shadow-sm transition hover:-translate-y-0.5 hover:shadow-md"
            >
              <div className="relative min-h-[7.25rem] bg-surface-alt">
                {article.featured_image ? (
                  <Image src={article.featured_image} alt={title(article)} fill className="object-cover" sizes="120px" />
                ) : null}
              </div>
              <div className="min-w-0 p-3">
                <span
                  className="inline-flex rounded px-2 py-0.5 text-[10px] font-extrabold text-white"
                  style={{ backgroundColor: article.category.color }}
                >
                  {category(article)}
                </span>
                <h2 className="mt-2 line-clamp-3 text-sm font-extrabold leading-snug text-foreground group-hover:text-primary">
                  {title(article)}
                </h2>
                <p className="mt-2 text-[11px] font-medium text-muted">
                  {article.published_at ? timeAgo(new Date(article.published_at), language) : ""}
                </p>
              </div>
              <span className="sr-only">
                {language === "ne" ? "मुख्य समाचारमा देखाउनुहोस्" : "Show as top story"} {index + 1}
              </span>
            </button>
          ))}
        </div>
      </div>
    </section>
  );
}

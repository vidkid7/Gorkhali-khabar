"use client";

import Link from "next/link";
import { useLanguage } from "@/contexts/LanguageContext";
import { toNepaliDigits } from "@/contexts/LanguageContext";

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

export function SidebarListClient({
  titleKey,
  articles,
}: {
  titleKey: string;
  articles: SidebarArticle[];
}) {
  const { language, t } = useLanguage();

  if (!articles.length) return null;

  return (
    <div className="card p-4">
      <h3 className="font-bold text-base mb-3 border-b border-border pb-2">
        {t(titleKey)}
      </h3>
      <ul className="space-y-3">
        {articles.map((a, idx) => {
          const title = language === "en" && a.title_en ? a.title_en : a.title;
          const num = language === "ne" ? toNepaliDigits(idx + 1) : idx + 1;
          return (
            <li key={a.id}>
              <Link
                href={`/articles/${a.slug}`}
                className="flex gap-3 items-start group"
              >
                <span className="text-lg font-bold text-accent leading-none mt-0.5">
                  {num}
                </span>
                <div className="min-w-0">
                  <p className="text-sm font-medium line-clamp-2 group-hover:text-accent transition-colors">
                    {title}
                  </p>
                  <span
                    className="category-badge mt-1 text-[10px]"
                    style={{ "--category-color": a.category.color } as React.CSSProperties}
                  >
                    {language === "en" && a.category.name_en
                      ? a.category.name_en
                      : a.category.name}
                  </span>
                </div>
              </Link>
            </li>
          );
        })}
      </ul>
    </div>
  );
}

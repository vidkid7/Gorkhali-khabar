"use client";

import Link from "next/link";
import { useLanguage, toNepaliDigits } from "@/contexts/LanguageContext";
import { timeAgo } from "@/lib/utils";

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

  const title = (article: BriefArticle) => language === "en" && article.title_en ? article.title_en : article.title;
  const category = (article: BriefArticle) => language === "en" && article.category.name_en ? article.category.name_en : article.category.name;

  return (
    <section id="daily-brief" className="scroll-mt-28 rounded-3xl border border-border bg-surface p-4 shadow-sm sm:p-5">
      <div className="mb-4 flex items-center justify-between gap-4">
        <div>
          <h2 className="text-xl font-black text-foreground">
            {language === "ne" ? "५ मिनेट ब्रिफ" : "5-minute brief"}
          </h2>
          <p className="mt-1 text-xs font-medium text-muted">
            {language === "ne" ? "आज छुटाउन नहुने मुख्य खबरहरू" : "The stories worth catching up on today"}
          </p>
        </div>
        <Link href="/categories/samachar" className="shrink-0 rounded-full bg-foreground px-3 py-1.5 text-xs font-bold text-background">
          {language === "ne" ? "सबै" : "All"}
        </Link>
      </div>

      <ol className="space-y-3">
        {articles.slice(0, 5).map((article, index) => (
          <li key={article.id}>
            <Link href={`/articles/${article.slug}`} className="grid grid-cols-[2.25rem_minmax(0,1fr)] gap-3 rounded-2xl bg-surface-alt p-3 transition-colors hover:bg-border">
              <span className="grid h-9 w-9 place-items-center rounded-full bg-background text-sm font-black text-primary shadow-sm">
                {language === "ne" ? toNepaliDigits(index + 1) : index + 1}
              </span>
              <span className="min-w-0">
                <span className="flex min-w-0 items-center gap-2">
                  <span
                    className="h-2 w-2 shrink-0 rounded-full"
                    style={{ backgroundColor: article.category.color }}
                  />
                  <span className="truncate text-[11px] font-extrabold text-muted">{category(article)}</span>
                  {article.published_at && (
                    <span className="shrink-0 text-[11px] font-medium text-muted">
                      {timeAgo(new Date(article.published_at), language)}
                    </span>
                  )}
                </span>
                <span className="mt-1 line-clamp-2 text-sm font-extrabold leading-snug text-foreground">
                  {title(article)}
                </span>
              </span>
            </Link>
          </li>
        ))}
      </ol>
    </section>
  );
}

"use client";

import Image from "next/image";
import Link from "next/link";
import { useLanguage } from "@/contexts/LanguageContext";
import { toNepaliDigits } from "@/contexts/LanguageContext";
import { useEffect } from "react";
import { laravelApi } from "@/lib/api/laravel";
import { publicArticlePath } from "@/lib/public-articles";
import { ImageWithFallback } from "@/components/ui/ImageWithFallback";
import { ArticleActions } from "@/components/articles/ArticleActions";
import { AdSlot } from "@/components/ads/AdSlot";
import {
  shouldShowInArticleAd,
  splitHtmlAtParagraph,
} from "@/components/ads/ad-placement";

interface ArticleContentProps {
  articleId: string;
  title: string;
  title_en?: string | null;
  content: string;
  content_en?: string | null;
  excerpt?: string | null;
  excerpt_en?: string | null;
  featured_image?: string | null;
  ai_summary?: string | null;
  category: { name: string; name_en?: string | null; slug: string; color: string };
  author: { name?: string | null; image?: string | null };
  tags: { name: string; name_en?: string | null; slug: string }[];
  reading_time?: number | null;
  word_count?: number | null;
  view_count: number;
  published_at?: Date | string | null;
  slug: string;
}

export function ArticleContent({
  articleId,
  title,
  title_en,
  content,
  content_en,
  featured_image,
  ai_summary,
  category,
  author,
  tags,
  reading_time,
  word_count,
  view_count,
  published_at,
  slug,
}: ArticleContentProps) {
  const { language, t } = useLanguage();

  useEffect(() => {
    void laravelApi.post(`/api/v1/articles/${encodeURIComponent(articleId)}/view`, undefined, { csrf: false }).catch(() => {});
  }, [articleId]);

  const displayTitle = language === "en" && title_en ? title_en : title;
  const displayContent = language === "en" && content_en ? content_en : content;
  const showArticleAd = shouldShowInArticleAd(word_count);
  const { beforeAd, afterAd } = showArticleAd
    ? splitHtmlAtParagraph(displayContent)
    : { beforeAd: displayContent, afterAd: "" };
  const catName = language === "en" && category.name_en ? category.name_en : category.name;
  const views = language === "ne" ? toNepaliDigits(view_count) : view_count;

  const formattedDate = published_at
    ? new Date(published_at).toLocaleDateString(
        language === "ne" ? "ne-NP" : "en-US",
        { year: "numeric", month: "long", day: "numeric" }
      )
    : null;

  const readingTimeText = reading_time
    ? `${language === "ne" ? toNepaliDigits(reading_time) : reading_time} ${t("common.minutes")} ${t("common.readingTime")}`
    : null;

  const shareUrl =
    typeof window !== "undefined"
      ? window.location.href
      : `${process.env.NEXT_PUBLIC_SITE_URL || ""}${publicArticlePath(slug)}`;

  return (
    <article className="article-detail mx-auto max-w-4xl">
      {/* Breadcrumbs */}
      <nav className="mb-5 flex flex-wrap items-center gap-1.5 text-xs font-semibold uppercase tracking-wide text-muted">
        <Link href="/" className="hover:text-accent">
          {t("common.home")}
        </Link>
        <span>/</span>
        <Link
          href={`/categories/${category.slug}`}
          className="hover:text-accent"
          style={{ fontFamily: "var(--font-nepali-serif)" }}
        >
          {catName}
        </Link>
      </nav>

      {/* Title */}
      <h1 className="mb-5 max-w-4xl text-3xl font-black leading-[1.12] tracking-tight text-foreground sm:text-4xl lg:text-5xl" style={{ fontFamily: "var(--font-nepali-serif)" }}>
        {displayTitle}
      </h1>

      {/* Meta */}
      <div className="mb-7 flex flex-wrap items-center gap-2.5 rounded-xl border border-border bg-surface-alt/50 px-3 py-3 text-xs text-muted sm:gap-4 sm:px-4">
        <span
          className="category-badge"
          style={{ "--category-color": category.color } as React.CSSProperties}
        >
          {catName}
        </span>
        {author.name && (
          <span className="flex items-center gap-1">
            {author.image && (
              <Image
                src={author.image}
                alt={author.name}
                width={20}
                height={20}
                className="rounded-full"
              />
            )}
            {author.name}
          </span>
        )}
        {formattedDate && <span suppressHydrationWarning>{formattedDate}</span>}
        {readingTimeText && <span>{readingTimeText}</span>}
        <span>
          {views} {t("article.views")}
        </span>
        {word_count && (
          <span>
            {language === "ne" ? toNepaliDigits(word_count) : word_count}{" "}
            {t("article.wordCount")}
          </span>
        )}
      </div>

      <div className="mb-6 border-y border-border py-3">
        <ArticleActions articleId={articleId} title={displayTitle} url={shareUrl} />
      </div>

      {/* Featured image */}
      {featured_image && (
        <div className="relative mb-8 h-64 w-full overflow-hidden rounded-2xl border border-border bg-surface-alt shadow-sm md:h-[30rem]">
          <ImageWithFallback
            src={featured_image}
            alt={displayTitle}
            fill
            className="object-cover"
            priority
            sizes="(max-width: 768px) 100vw, 800px"
          />
        </div>
      )}

      {/* AI Summary */}
      {ai_summary && (
        <div className="card p-4 mb-6 border-l-4 border-accent">
          <h2 className="text-sm font-bold text-accent mb-2" style={{ fontFamily: "var(--font-nepali-serif)" }}>
            {t("article.aiSummary")}
          </h2>
          <p className="text-sm text-muted leading-relaxed">{ai_summary}</p>
        </div>
      )}

      {/* Content */}
      <div className="mx-auto mb-10 max-w-3xl">
        <div
          className="prose-news"
          dangerouslySetInnerHTML={{ __html: beforeAd }}
        />
        {showArticleAd && afterAd && (
          <AdSlot position="IN_ARTICLE" className="my-8" compactLabel />
        )}
        {afterAd && (
          <div
            className="prose-news"
            dangerouslySetInnerHTML={{ __html: afterAd }}
          />
        )}
      </div>

      {/* Tags */}
      {tags.length > 0 && (
        <div className="flex flex-wrap gap-2 mb-8">
          {tags.map((tag) => (
            <Link
              key={tag.slug}
              href={`/tag/${tag.slug}`}
              className="text-xs px-3 py-1 rounded-full border border-border hover:bg-surface transition-colors"
            >
              #{language === "en" && tag.name_en ? tag.name_en : tag.name}
            </Link>
          ))}
        </div>
      )}

      {/* Comments placeholder */}
      <section>
        <h2 className="text-xl font-bold mb-4" style={{ fontFamily: "var(--font-nepali-serif)" }}>{t("article.comments")}</h2>
        <div className="card p-6 text-center text-muted text-sm">
          {t("article.writeComment")}
        </div>
      </section>
    </article>
  );
}

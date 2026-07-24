"use client";

import Link from "next/link";
import { useLanguage } from "@/contexts/LanguageContext";
import { toNepaliDigits } from "@/contexts/LanguageContext";
import { publicArticlePath } from "@/lib/public-articles";
import { timeAgo as sharedTimeAgo, getInitials } from "@/lib/utils";
import { ImageWithFallback } from "@/components/ui/ImageWithFallback";
import { Clock3, Eye, ImageIcon, MessageSquare } from "lucide-react";
import { getCategoryColorWithFallback } from "@/lib/utils";

interface ArticleCardProps {
  slug: string;
  title: string;
  title_en?: string | null;
  excerpt?: string | null;
  excerpt_en?: string | null;
  featured_image?: string | null;
  category: {
    name: string;
    name_en?: string | null;
    slug: string;
    color: string;
  };
  author: { name?: string | null };
  reading_time?: number | null;
  published_at?: string | Date | null;
  view_count?: number;
  comment_count?: number;
  variant?: "default" | "hero" | "horizontal";
}

function AuthorAvatar({ name }: { name: string }) {
  const initials = getInitials(name);
  return (
    <span className="flex h-6 w-6 shrink-0 items-center justify-center rounded-full text-[10px] font-bold text-white"
      style={{ background: "var(--primary)" }}>
      {initials}
    </span>
  );
}

function CategoryLabel({ color, name, inverse = false, slug }: { color: string; name: string; inverse?: boolean; slug?: string }) {
  const designColor = slug ? getCategoryColorWithFallback(slug, color) : color;
  const barColor = inverse ? "var(--color-white)" : designColor;
  
  return (
    <span className={`inline-flex items-center gap-2 text-[11px] font-bold ${inverse ? "text-white" : "text-foreground"}`}>
      <span className="h-0.5 w-5 shrink-0" style={{ backgroundColor: barColor }} />
      {name}
    </span>
  );
}

export function ArticleCard({
  slug, title, title_en, excerpt, excerpt_en, featured_image,
  category, author, reading_time, published_at, view_count = 0,
  comment_count = 0, variant = "default",
}: ArticleCardProps) {
  const { language } = useLanguage();

  const displayTitle = language === "en" && title_en ? title_en : title;
  const displayExcerpt = language === "en" && excerpt_en ? excerpt_en : excerpt;
  const catName = language === "en" && category.name_en ? category.name_en : category.name;
  const timeStr = published_at ? sharedTimeAgo(new Date(published_at), language) : null;

  if (variant === "horizontal") {
    return (
      <Link
        href={publicArticlePath(slug)}
        className="group grid w-full min-w-0 grid-cols-[9rem_minmax(0,1fr)] overflow-hidden border-border transition-colors hover:border-border-strong"
      >
        <div className="relative aspect-[4/3] w-full overflow-hidden bg-surface-alt">
          {featured_image ? (
            <ImageWithFallback src={featured_image} alt={displayTitle} fill
              className="object-cover transition-transform duration-300 group-hover:scale-[1.03]" sizes="144px" />
          ) : (
            <div className="flex h-full w-full items-center justify-center bg-surface-alt">
              <ImageIcon className="h-8 w-8 text-muted" aria-hidden="true" />
            </div>
          )}
        </div>
        <div className="flex min-w-0 flex-col p-3">
          <CategoryLabel color={category.color} name={catName} slug={category.slug} />
          <h3 className="mt-1.5 line-clamp-3 text-sm font-semibold leading-[1.55] transition-colors group-hover:text-accent" style={{ fontFamily: "var(--font-nepali-serif)" }}>
            {displayTitle}
          </h3>
          <div className="mt-auto flex min-h-5 items-center gap-3 pt-2 text-[11px] text-muted">
            {timeStr && (
              <span className="flex items-center gap-1">
                <Clock3 className="h-3 w-3" aria-hidden="true" />
                {timeStr}
              </span>
            )}
            {comment_count > 0 && (
              <span className="flex items-center gap-1">
                <MessageSquare className="h-3 w-3" aria-hidden="true" />
                {language === "ne" ? toNepaliDigits(comment_count) : comment_count}
              </span>
            )}
          </div>
        </div>
      </Link>
    );
  }

  if (variant === "hero") {
    return (
      <Link href={publicArticlePath(slug)} className="group relative block w-full min-w-0 overflow-hidden bg-surface">
        <div className="relative aspect-[16/9] w-full">
          {featured_image ? (
            <ImageWithFallback src={featured_image} alt={displayTitle} fill
              className="object-cover transition-transform duration-700 group-hover:scale-[1.03]" sizes="(max-width: 768px) 100vw, 50vw" priority />
          ) : (
            <div className="flex h-full w-full items-center justify-center bg-surface-alt">
              <ImageIcon className="h-14 w-14 text-muted" aria-hidden="true" />
            </div>
          )}
          <div className="absolute inset-0 bg-gradient-to-t from-black/90 via-black/30 to-transparent" />
          <div className="absolute bottom-0 left-0 right-0 p-6 text-white">
            <CategoryLabel color={category.color} name={catName} inverse slug={category.slug} />
            <h2 className="mt-3 line-clamp-3 text-xl font-bold leading-[1.5] decoration-2 group-hover:underline lg:text-2xl" style={{ fontFamily: "var(--font-nepali-serif)" }}>
              {displayTitle}
            </h2>
            {displayExcerpt && (
              <p className="mt-2 text-sm text-white/80 line-clamp-2">{displayExcerpt}</p>
            )}
            <div className="mt-3 flex min-h-6 flex-wrap items-center gap-3 text-xs text-white/70">
              {author.name && (
                <span className="flex items-center gap-1.5">
                  <AuthorAvatar name={author.name} />
                  {author.name}
                </span>
              )}
              {timeStr && (
                <span className="flex items-center gap-1">
                  <Clock3 className="h-3 w-3" aria-hidden="true" />
                  {timeStr}
                </span>
              )}
              {view_count > 0 && (
                <span className="flex items-center gap-1">
                  <Eye className="h-3 w-3" aria-hidden="true" />
                  {language === "ne" ? toNepaliDigits(view_count) : view_count}
                </span>
              )}
              {comment_count > 0 && (
                <span className="flex items-center gap-1">
                  <MessageSquare className="h-3 w-3" aria-hidden="true" />
                  {language === "ne" ? toNepaliDigits(comment_count) : comment_count}
                </span>
              )}
            </div>
          </div>
        </div>
      </Link>
    );
  }

  return (
    <Link href={publicArticlePath(slug)} className="group flex h-full flex-col overflow-hidden border-t border-border bg-surface transition-colors hover:border-border-strong">
      <div className="relative aspect-[16/10] w-full overflow-hidden bg-surface-alt">
        {featured_image ? (
          <ImageWithFallback src={featured_image} alt={displayTitle} fill
            className="object-cover transition-transform duration-500 group-hover:scale-[1.03]"
            sizes="(max-width: 768px) 100vw, (max-width: 1200px) 50vw, 33vw" />
        ) : (
          <div className="flex h-full w-full items-center justify-center bg-surface-alt">
            <ImageIcon className="h-12 w-12 text-muted" aria-hidden="true" />
          </div>
        )}
      </div>
      <div className="flex flex-1 flex-col p-4">
        <CategoryLabel color={category.color} name={catName} slug={category.slug} />
        <h3 className="mt-2 line-clamp-3 text-base font-bold leading-[1.55] transition-colors group-hover:text-accent" style={{ fontFamily: "var(--font-nepali-serif)" }}>
          {displayTitle}
        </h3>
        {displayExcerpt && (
          <p className="mt-1.5 text-sm text-muted line-clamp-3 leading-relaxed">{displayExcerpt}</p>
        )}
        <div className="mt-auto flex min-h-7 flex-wrap items-center gap-3 pt-3 text-xs text-muted">
          {author.name && (
            <span className="flex items-center gap-1.5">
              <AuthorAvatar name={author.name} />
              <span className="font-medium">{author.name}</span>
            </span>
          )}
          <span className="ml-auto flex items-center gap-3">
            {timeStr && (
              <span className="flex items-center gap-1">
                <Clock3 className="h-3 w-3" aria-hidden="true" />
                {timeStr}
              </span>
            )}
            {view_count > 0 && (
              <span className="flex items-center gap-1">
                <Eye className="h-3 w-3" aria-hidden="true" />
                {language === "ne" ? toNepaliDigits(view_count) : view_count}
              </span>
            )}
            {reading_time && reading_time > 0 && (
              <span className="flex items-center gap-1">
                <svg className="w-3 h-3" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/></svg>
                {language === "ne" ? `${toNepaliDigits(reading_time)} मिन` : `${reading_time} min`}
              </span>
            )}
            {comment_count > 0 && (
              <span className="flex items-center gap-1">
                <MessageSquare className="h-3 w-3" aria-hidden="true" />
                {language === "ne" ? toNepaliDigits(comment_count) : comment_count}
              </span>
            )}
          </span>
        </div>
      </div>
    </Link>
  );
}

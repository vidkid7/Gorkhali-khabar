import Link from "next/link";

interface QuickNewsBannerProps {
  title: string;
  title_en?: string | null;
  slug: string;
  categoryName: string;
  categoryColor: string;
  publishedAt?: Date | string | null;
  lang?: "ne" | "en";
}

export function QuickNewsBanner({
  title,
  title_en,
  slug,
  categoryName,
  categoryColor,
  lang = "ne",
}: QuickNewsBannerProps) {
  const displayTitle = lang === "en" && title_en ? title_en : title;

  return (
    <Link
      href={`/articles/${slug}`}
      className="group flex items-start gap-4 rounded-lg px-4 py-3 transition-colors hover:opacity-90"
      style={{ background: categoryColor + "18", borderLeft: `4px solid ${categoryColor}` }}
    >
      <span
        className="shrink-0 text-[10px] font-bold uppercase px-2 py-0.5 rounded text-white mt-0.5"
        style={{ background: categoryColor }}
      >
        {lang === "ne" ? "ताजा" : "LATEST"}
      </span>
      <h3 className="text-sm font-bold leading-snug text-foreground group-hover:text-primary transition-colors line-clamp-2">
        {displayTitle}
      </h3>
      <svg
        className="h-4 w-4 text-muted shrink-0 mt-0.5 group-hover:translate-x-1 transition-transform"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
        strokeWidth={2}
      >
        <path d="M9 18l6-6-6-6" />
      </svg>
    </Link>
  );
}

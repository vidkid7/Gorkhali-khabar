"use client";

import Link from "next/link";
import Image from "next/image";
import { useLanguage } from "@/contexts/LanguageContext";
import { timeAgo } from "@/lib/utils";
import { SectionHeader } from "./SectionHeader";

interface CategoryArticle {
  id: string;
  slug: string;
  title: string;
  title_en?: string | null;
  excerpt?: string | null;
  excerpt_en?: string | null;
  featured_image?: string | null;
  published_at?: string | Date | null;
  view_count: number;
  author: { name?: string | null };
  category: { name: string; name_en?: string | null; slug: string; color: string };
}

interface CategorySectionProps {
  sectionKey: string;
  articles: CategoryArticle[];
  color: string;
  slug: string;
  layout?: "grid" | "featured" | "list";
}

export function CategorySection({ sectionKey, articles, color, slug, layout = "featured" }: CategorySectionProps) {
  const { language } = useLanguage();
  if (!articles.length) return null;

  const title = (a: CategoryArticle) => (language === "en" && a.title_en ? a.title_en : a.title);
  const excerpt = (a: CategoryArticle) => (language === "en" && a.excerpt_en ? a.excerpt_en : a.excerpt);

  if (layout === "list") {
    return (
      <section>
        <SectionHeader titleKey={sectionKey} color={color} href={`/categories/${slug}`} />
        <div className="space-y-4">
          {articles.map((a) => (
            <Link key={a.id} href={`/articles/${a.slug}`} className="flex gap-4 group">
              <div className="relative w-28 h-20 shrink-0 rounded-lg overflow-hidden bg-surface">
                {a.featured_image ? (
                  <Image src={a.featured_image} alt={title(a)} fill className="object-cover" sizes="112px" />
                ) : (
                  <div className="w-full h-full flex items-center justify-center"><span className="text-2xl">📰</span></div>
                )}
              </div>
              <div className="flex-1 min-w-0">
                <h3 className="text-sm font-semibold line-clamp-2 group-hover:text-accent transition-colors">{title(a)}</h3>
                <p className="text-xs text-muted mt-1">{a.published_at ? timeAgo(new Date(a.published_at), language) : ""}</p>
              </div>
            </Link>
          ))}
        </div>
      </section>
    );
  }

  if (layout === "grid") {
    return (
      <section>
        <SectionHeader titleKey={sectionKey} color={color} href={`/categories/${slug}`} />
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
          {articles.map((a) => (
            <Link key={a.id} href={`/articles/${a.slug}`} className="card group block">
              <div className="relative w-full h-44 rounded-t-lg overflow-hidden bg-surface">
                {a.featured_image ? (
                  <Image src={a.featured_image} alt={title(a)} fill className="object-cover" sizes="(max-width: 768px) 100vw, 33vw" />
                ) : (
                  <div className="w-full h-full flex items-center justify-center"><span className="text-4xl">📰</span></div>
                )}
              </div>
              <div className="p-4">
                <span className="text-[10px] font-bold uppercase tracking-wider" style={{ color }}>{language === "en" && a.category.name_en ? a.category.name_en : a.category.name}</span>
                <h3 className="mt-1 text-sm font-semibold line-clamp-2 group-hover:text-accent transition-colors">{title(a)}</h3>
                <p className="text-xs text-muted mt-1">{a.published_at ? timeAgo(new Date(a.published_at), language) : ""}</p>
              </div>
            </Link>
          ))}
        </div>
      </section>
    );
  }

  // "featured" layout: 1 big + smaller list
  const [main, ...rest] = articles;
  return (
    <section>
      <SectionHeader titleKey={sectionKey} color={color} href={`/categories/${slug}`} />
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-5">
        {/* Main featured */}
        <Link href={`/articles/${main.slug}`} className="lg:col-span-3 card group block">
          <div className="relative w-full h-64 lg:h-80 rounded-t-lg overflow-hidden bg-surface">
            {main.featured_image ? (
              <Image src={main.featured_image} alt={title(main)} fill className="object-cover" sizes="(max-width: 768px) 100vw, 60vw" priority />
            ) : (
              <div className="w-full h-full flex items-center justify-center" style={{ background: "var(--surface-alt, var(--surface))" }}>
                <span className="text-6xl">📰</span>
              </div>
            )}
            <div className="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent" />
            <div className="absolute bottom-0 p-5 text-white">
              <span className="text-xs font-bold uppercase tracking-wider px-2 py-0.5 rounded" style={{ backgroundColor: color }}>
                {language === "en" && main.category.name_en ? main.category.name_en : main.category.name}
              </span>
              <h3 className="mt-2 text-lg font-bold line-clamp-3 group-hover:underline">{title(main)}</h3>
              {excerpt(main) && <p className="mt-1 text-sm text-white/80 line-clamp-2">{excerpt(main)}</p>}
              <div className="mt-2 flex items-center gap-3 text-xs text-white/70">
                {main.author.name && <span>{main.author.name}</span>}
                {main.published_at && <span>{timeAgo(new Date(main.published_at), language)}</span>}
              </div>
            </div>
          </div>
        </Link>

        {/* Side list */}
        <div className="lg:col-span-2 space-y-3">
          {rest.map((a) => (
            <Link key={a.id} href={`/articles/${a.slug}`} className="flex gap-3 group">
              <div className="relative w-24 h-18 shrink-0 rounded-lg overflow-hidden bg-surface">
                {a.featured_image ? (
                  <Image src={a.featured_image} alt={title(a)} fill className="object-cover" sizes="96px" />
                ) : (
                  <div className="w-full h-full flex items-center justify-center"><span className="text-xl">📰</span></div>
                )}
              </div>
              <div className="flex-1 min-w-0 py-0.5">
                <h4 className="text-sm font-semibold line-clamp-2 group-hover:text-accent transition-colors leading-tight">{title(a)}</h4>
                <p className="text-xs text-muted mt-1">{a.published_at ? timeAgo(new Date(a.published_at), language) : ""}</p>
              </div>
            </Link>
          ))}
        </div>
      </div>
    </section>
  );
}

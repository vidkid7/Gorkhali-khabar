"use client";

import Link from "next/link";
import { useRef } from "react";
import { useLanguage } from "@/contexts/LanguageContext";
import { SectionHeader } from "./SectionHeader";

interface Reel {
  id: string;
  title: string;
  title_en?: string | null;
  slug: string;
  thumbnail?: string | null;
  view_count: number;
}

export function ReelsCarousel({ reels }: { reels: Reel[] }) {
  const { language } = useLanguage();
  const scrollRef = useRef<HTMLDivElement>(null);

  if (!reels.length) return null;

  const scroll = (dir: "left" | "right") => {
    if (!scrollRef.current) return;
    const amount = dir === "left" ? -300 : 300;
    scrollRef.current.scrollBy({ left: amount, behavior: "smooth" });
  };

  return (
    <section>
      <SectionHeader titleKey="sections.okReels" color="#d50000" href="/reels" />
      <div className="relative group">
        <button
          onClick={() => scroll("left")}
          className="absolute left-0 top-1/2 -translate-y-1/2 z-10 w-10 h-10 rounded-full bg-background/90 border border-border shadow-md hidden md:flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity"
          aria-label="Scroll left"
        >
          <svg className="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth={2}><path d="M15 18l-6-6 6-6"/></svg>
        </button>
        <div ref={scrollRef} className="flex gap-4 overflow-x-auto scrollbar-hide snap-x snap-mandatory pb-2">
          {reels.map((reel) => {
            const title = language === "en" && reel.title_en ? reel.title_en : reel.title;
            return (
              <Link
                key={reel.id}
                href={`/reels/${reel.slug}`}
                className="snap-start shrink-0 w-44 group/reel"
              >
                <div className="relative w-44 h-72 rounded-xl overflow-hidden bg-surface border border-border">
                  {reel.thumbnail ? (
                    <img src={reel.thumbnail} alt={title} className="w-full h-full object-cover" />
                  ) : (
                    <div className="w-full h-full flex items-center justify-center bg-gradient-to-b from-red-600 to-red-900">
                      <svg className="h-12 w-12 text-white/80" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>
                    </div>
                  )}
                  <div className="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent" />
                  <div className="absolute bottom-0 p-3 text-white">
                    <p className="text-xs font-semibold line-clamp-2 leading-tight">{title}</p>
                    <p className="text-[10px] mt-1 opacity-70">▶ {reel.view_count.toLocaleString()}</p>
                  </div>
                </div>
              </Link>
            );
          })}
        </div>
        <button
          onClick={() => scroll("right")}
          className="absolute right-0 top-1/2 -translate-y-1/2 z-10 w-10 h-10 rounded-full bg-background/90 border border-border shadow-md hidden md:flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity"
          aria-label="Scroll right"
        >
          <svg className="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth={2}><path d="M9 18l6-6-6-6"/></svg>
        </button>
      </div>
    </section>
  );
}

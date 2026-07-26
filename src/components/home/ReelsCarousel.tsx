"use client";

import Image from "next/image";
import Link from "next/link";
import { useEffect, useRef, useState } from "react";
import { ArrowRight, ChevronLeft, ChevronRight, Eye, Play } from "lucide-react";
import { useLanguage, toNepaliDigits } from "@/contexts/LanguageContext";
import { publicContentPath } from "@/lib/public-articles";

export interface Reel {
  id: string;
  title: string;
  title_en?: string | null;
  slug: string;
  thumbnail?: string | null;
  view_count?: number;
}

function formatViewCount(value: number, language: "ne" | "en"): string {
  if (language === "ne") return toNepaliDigits(value);
  if (value >= 1_000_000) return `${(value / 1_000_000).toFixed(1)}M`;
  if (value >= 1_000) return `${(value / 1_000).toFixed(1)}K`;
  return value.toLocaleString("en-US");
}

export function ReelsCarousel({ reels }: { reels: Reel[] }) {
  const { language } = useLanguage();
  const scrollRef = useRef<HTMLDivElement>(null);
  const [canScrollLeft, setCanScrollLeft] = useState(false);
  const [canScrollRight, setCanScrollRight] = useState(false);

  const isNe = language === "ne";

  useEffect(() => {
    const node = scrollRef.current;
    if (!node) return;
    const update = () => {
      setCanScrollLeft(node.scrollLeft > 4);
      setCanScrollRight(node.scrollLeft + node.clientWidth < node.scrollWidth - 4);
    };
    update();
    node.addEventListener("scroll", update, { passive: true });
    const ro = new ResizeObserver(update);
    ro.observe(node);
    return () => {
      node.removeEventListener("scroll", update);
      ro.disconnect();
    };
  }, [reels.length]);

  if (!reels.length) return null;

  const scroll = (dir: "left" | "right") => {
    const node = scrollRef.current;
    if (!node) return;
    // Scroll by the visible card width plus the gap for natural feel.
    const card = node.querySelector<HTMLElement>("a[data-reel-card]");
    const amount = card ? card.getBoundingClientRect().width + 16 : 240;
    node.scrollBy({ left: dir === "left" ? -amount : amount, behavior: "smooth" });
  };

  return (
    <section
      className="overflow-hidden border border-border bg-surface text-foreground shadow-sm"
      style={{ borderRadius: "var(--radius-lg)" }}
      aria-label={isNe ? "OK रिल्स खण्ड" : "OK Reels section"}
    >
      {/* Editorial header strip */}
      <header className="flex items-center justify-between gap-3 border-b border-border px-4 py-4 sm:px-6">
        <div className="flex items-center gap-3">
          <span
            className="grid h-10 w-10 place-items-center rounded-full"
            style={{ background: "var(--accent-light)", color: "var(--accent)" }}
            aria-hidden="true"
          >
            <Play className="h-4 w-4 fill-current" />
          </span>
          <div className="grid gap-0.5">
            <span className="text-[10px] font-bold uppercase tracking-[0.18em] text-accent">
              {isNe ? "हेर्नुहोस्" : "Watch"}
            </span>
            <h2
              className="text-[1.05rem] font-black leading-tight"
              style={{ fontFamily: "var(--font-nepali-serif)" }}
            >
              {isNe ? "OK रिल्स" : "OK Reels"}
            </h2>
          </div>
        </div>
        <Link
          href="/reels"
          className="inline-flex items-center gap-1 rounded-full border border-border px-3 py-1.5 text-[11px] font-bold uppercase tracking-wider text-foreground transition-colors hover:border-primary hover:bg-primary-light hover:text-primary focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary"
        >
          {isNe ? "सबै" : "All"}
          <ArrowRight className="h-3.5 w-3.5" aria-hidden="true" />
        </Link>
      </header>

      <div className="relative bg-surface-alt/40 px-2 py-5 sm:px-4 sm:py-6">
        {canScrollLeft && (
          <button
            type="button"
            onClick={() => scroll("left")}
            className="absolute left-2 top-1/2 z-20 hidden h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full border border-border bg-surface/95 text-foreground shadow-md transition-all hover:scale-105 hover:border-primary hover:text-primary md:flex"
            aria-label={isNe ? "बायाँ स्क्रोल गर्नुहोस्" : "Scroll reels left"}
          >
            <ChevronLeft className="h-5 w-5" />
          </button>
        )}
        {canScrollRight && (
          <button
            type="button"
            onClick={() => scroll("right")}
            className="absolute right-2 top-1/2 z-20 hidden h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full border border-border bg-surface/95 text-foreground shadow-md transition-all hover:scale-105 hover:border-primary hover:text-primary md:flex"
            aria-label={isNe ? "दायाँ स्क्रोल गर्नुहोस्" : "Scroll reels right"}
          >
            <ChevronRight className="h-5 w-5" />
          </button>
        )}

        <div
          ref={scrollRef}
          className="no-scrollbar flex snap-x snap-mandatory gap-4 overflow-x-auto px-2 pb-1"
        >
          {reels.map((reel, index) => {
            const title = isNe && reel.title_en ? reel.title_en : reel.title;
            const viewCount = reel.view_count ?? 0;
            const isTopView = viewCount >= 10_000;
            const rank = isNe ? toNepaliDigits(index + 1) : index + 1;
            const watchLabel = isNe ? "हेर्नुहोस्" : "Watch now";
            return (
              <Link
                key={reel.id}
                href={publicContentPath("/reels", reel.slug)}
                data-reel-card="true"
                aria-label={`${title} — ${watchLabel}`}
                className="group/reel relative w-40 shrink-0 snap-start transition-all duration-200 sm:w-48"
              >
                {/* Card */}
                <div className="relative h-72 w-40 overflow-hidden rounded-xl border border-border bg-surface shadow-sm transition-all duration-200 group-hover/reel:-translate-y-1 group-hover/reel:border-primary group-hover/reel:shadow-lg sm:h-80 sm:w-48">
                  {reel.thumbnail ? (
                    <Image
                      src={reel.thumbnail}
                      alt={title}
                      fill
                      className="object-cover transition-transform duration-500 group-hover/reel:scale-105"
                      sizes="(max-width: 640px) 160px, 192px"
                    />
                  ) : (
                    <div className="flex h-full w-full items-center justify-center bg-gradient-to-b from-red-500 to-red-800">
                      <Play className="h-12 w-12 fill-white/80 text-white/80" />
                    </div>
                  )}

                  {/* Layered gradient for legibility + focus vignette */}
                  <div
                    className="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent"
                    aria-hidden="true"
                  />
                  <div
                    className="absolute inset-0 bg-gradient-to-br from-black/40 via-transparent to-transparent opacity-0 transition-opacity duration-300 group-hover/reel:opacity-100"
                    aria-hidden="true"
                  />

                  {/* Rank badge */}
                  <span
                    className="absolute right-2 top-2 inline-flex h-7 min-w-7 items-center justify-center rounded-full bg-black/55 px-1.5 text-[11px] font-bold text-white ring-1 ring-white/25 backdrop-blur-sm tabular-nums"
                    aria-hidden="true"
                  >
                    {rank}
                  </span>

                  {/* Watch badge — outlined so it stays visible on any thumbnail */}
                  <span
                    className="absolute left-2 top-2 inline-flex h-7 w-7 items-center justify-center rounded-full bg-white/95 text-red-600 shadow-md ring-1 ring-white/40 transition-transform duration-200 group-hover/reel:scale-110"
                    aria-hidden="true"
                  >
                    <Play className="h-3.5 w-3.5 fill-current" />
                  </span>

                  {/* Top-view highlight bar */}
                  {isTopView && (
                    <span
                      className="absolute inset-x-0 top-0 h-1 bg-accent"
                      aria-hidden="true"
                    />
                  )}

                  {/* Lower content stack */}
                  <div className="absolute inset-x-0 bottom-0 space-y-1.5 p-3 text-white">
                    <h3
                      className="line-clamp-3 text-[13px] font-bold leading-[1.45]"
                      style={{ fontFamily: "var(--font-nepali-serif)" }}
                    >
                      {title}
                    </h3>
                    <div className="flex items-center justify-between gap-2 pt-1">
                      <span className="inline-flex items-center gap-1 text-[10px] font-semibold text-white/85">
                        <Eye className="h-3 w-3" aria-hidden="true" />
                        {formatViewCount(viewCount, language)}
                      </span>
                      <span className="text-[10px] uppercase tracking-wider text-white/70">
                        {isNe ? "रेल" : "Reel"}
                      </span>
                    </div>
                  </div>
                </div>

                {/* Caption underneath card — secondary visual weight */}
                <div className="mt-2 flex items-start gap-1.5 px-0.5">
                  <span
                    className="mt-1 inline-block h-0.5 w-3 shrink-0"
                    style={{ backgroundColor: "var(--accent)" }}
                    aria-hidden="true"
                  />
                  <p className="line-clamp-2 text-[11px] font-semibold leading-[1.5] text-muted-foreground">
                    {isNe ? "अडियो र भिडियो" : "Watch the full clip"} — {title}
                  </p>
                </div>
              </Link>
            );
          })}
        </div>
      </div>
    </section>
  );
}

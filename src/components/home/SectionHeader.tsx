"use client";

import Link from "next/link";
import { ArrowRight } from "lucide-react";
import { useLanguage } from "@/contexts/LanguageContext";

interface SectionHeaderProps {
  titleKey: string;
  title?: string;
  titleEn?: string | null;
  color: string;
  href?: string;
}

export function SectionHeader({ titleKey, title, titleEn, color, href }: SectionHeaderProps) {
  const { t, language } = useLanguage();
  const displayTitle = title ? (language === "en" && titleEn ? titleEn : title) : t(titleKey);
  const viewAll = t("common.viewAll");
  const isNe = language === "ne";

  return (
    <header
      className="relative mb-6 border-b-2 border-border pb-3 sm:mb-8"
      style={{ borderBottomColor: "var(--border)" }}
    >
      {/* Category accent rule on top */}
      <span
        aria-hidden="true"
        className="absolute inset-x-0 -top-px h-0.5"
        style={{ background: color }}
      />

      <div className="flex items-end justify-between gap-3">
        <div className="grid gap-1">
          <span
            className="text-[10px] font-bold uppercase tracking-[0.2em]"
            style={{ color: color }}
          >
            <span className="inline-flex items-center gap-1.5">
              <span
                aria-hidden="true"
                className="inline-block h-1.5 w-1.5 rounded-full"
                style={{ background: color }}
              />
              {isNe ? "खण्ड" : "Section"}
            </span>
          </span>
          <h2
            className="text-[1.25rem] font-black leading-tight text-foreground sm:text-[1.5rem]"
            style={{
              fontFamily: "var(--font-nepali-serif)",
              paddingTop: "0.1em",
            }}
          >
            <span className="relative inline-block">
              {displayTitle}
              <span
                aria-hidden="true"
                className="absolute inset-x-0 -bottom-1.5 h-0.5"
                style={{ background: "var(--border)" }}
              />
            </span>
          </h2>
        </div>

        {href && (
          <Link
            href={href}
            className="group inline-flex shrink-0 items-center gap-1 text-[11px] font-bold uppercase tracking-[0.16em] text-foreground transition-colors hover:text-accent focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent"
          >
            {viewAll}
            <ArrowRight
              className="h-3.5 w-3.5 transition-transform duration-200 group-hover:translate-x-0.5"
              aria-hidden="true"
            />
          </Link>
        )}
      </div>
    </header>
  );
}

"use client";

import Link from "next/link";
import { useLanguage } from "@/contexts/LanguageContext";

interface SectionHeaderProps {
  titleKey: string;
  color: string;
  href?: string;
}

export function SectionHeader({ titleKey, color, href }: SectionHeaderProps) {
  const { t } = useLanguage();
  const title = t(titleKey);
  const viewAll = t("common.viewAll");

  return (
    <div className="flex items-center justify-between mb-6 pb-4" style={{ borderBottom: `2px solid var(--border)` }}>
      <div className="flex items-center gap-3">
        <div className="w-1 h-7 rounded-full" style={{ backgroundColor: color }} />
        <h2 className="text-xl sm:text-2xl font-bold" style={{ fontFamily: "var(--font-nepali-serif)", color: "var(--foreground)" }}>{title}</h2>
      </div>
      {href && (
        <Link href={href}
          className="text-xs font-semibold px-3 py-1.5 rounded-md transition-all hover:shadow-sm"
          style={{ background: color, color: "#fff" }}>
          {viewAll} →
        </Link>
      )}
    </div>
  );
}

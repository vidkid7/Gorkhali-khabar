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
    <div
      className="section-header-premium"
      style={{ "--section-color": color } as React.CSSProperties}
    >
      <div className="sh-title">
        <div className="sh-bar" />
        <h2 className="sh-text">{title}</h2>
      </div>
      {href && (
        <Link href={href} className="sh-link">
          {viewAll}
          <svg className="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2.5}>
            <path d="M9 18l6-6-6-6" />
          </svg>
        </Link>
      )}
    </div>
  );
}

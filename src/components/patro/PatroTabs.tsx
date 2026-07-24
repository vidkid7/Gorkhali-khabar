"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";
import { useLanguage } from "@/contexts/LanguageContext";
import { Calendar, Umbrella, Star, Coins, ArrowLeftRight, RefreshCw, type LucideIcon } from "lucide-react";

const TABS: { href: string; key: string; ne: string; en: string; Icon: LucideIcon }[] = [
  { href: "/patro", key: "calendar", ne: "पात्रो", en: "Calendar", Icon: Calendar },
  { href: "/patro/holidays", key: "holidays", ne: "बिदाहरू", en: "Holidays", Icon: Umbrella },
  { href: "/patro/rashifal", key: "rashifal", ne: "राशिफल", en: "Rashifal", Icon: Star },
  { href: "/patro/gold-silver", key: "gold-silver", ne: "सुन-चाँदी", en: "Gold-Silver", Icon: Coins },
  { href: "/patro/forex", key: "forex", ne: "विनिमय दर", en: "Forex", Icon: ArrowLeftRight },
  { href: "/patro/date-converter", key: "date-converter", ne: "मिति परिवर्तन", en: "Date Converter", Icon: RefreshCw },
];

export function PatroTabs() {
  const pathname = usePathname();
  const { language } = useLanguage();

  return (
    <nav className="no-scrollbar mb-6 flex gap-1 overflow-x-auto border-b border-border" aria-label={language === "ne" ? "पात्रो सेवाहरू" : "Patro services"}>
      {TABS.map((tab) => {
        const active = pathname === tab.href;
        return (
          <Link
            key={tab.key}
            href={tab.href}
            aria-current={active ? "page" : undefined}
            className="flex items-center gap-1.5 whitespace-nowrap border-b-2 px-3 py-2 text-xs font-semibold transition-colors"
            style={{
              borderColor: active ? "var(--primary)" : "transparent",
              color: active ? "var(--primary)" : "var(--muted)",
            }}
          >
            <span className="flex items-center"><tab.Icon className="h-4 w-4" /></span>
            {language === "ne" ? tab.ne : tab.en}
          </Link>
        );
      })}
    </nav>
  );
}

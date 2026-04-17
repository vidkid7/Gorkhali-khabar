"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";
import { useLanguage } from "@/contexts/LanguageContext";

const TABS = [
  { href: "/patro", key: "calendar", ne: "पात्रो", en: "Calendar", icon: "📅" },
  { href: "/patro/holidays", key: "holidays", ne: "बिदाहरू", en: "Holidays", icon: "🏖️" },
  { href: "/patro/rashifal", key: "rashifal", ne: "राशिफल", en: "Rashifal", icon: "♈" },
  { href: "/patro/gold-silver", key: "gold-silver", ne: "सुन-चाँदी", en: "Gold-Silver", icon: "🪙" },
  { href: "/patro/forex", key: "forex", ne: "विनिमय दर", en: "Forex", icon: "💱" },
  { href: "/patro/date-converter", key: "date-converter", ne: "मिति परिवर्तन", en: "Date Converter", icon: "🔄" },
];

export function PatroTabs() {
  const pathname = usePathname();
  const { language } = useLanguage();

  return (
    <nav className="flex flex-wrap gap-1.5 mb-6 p-1.5 rounded-xl"
      style={{ background: "var(--surface)", border: "1px solid var(--border)" }}>
      {TABS.map((tab) => {
        const active = pathname === tab.href;
        return (
          <Link
            key={tab.key}
            href={tab.href}
            className="flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-semibold transition-all whitespace-nowrap"
            style={{
              background: active ? "linear-gradient(135deg, #c62828, #b71c1c)" : "transparent",
              color: active ? "#fff" : "var(--muted)",
            }}
          >
            <span>{tab.icon}</span>
            {language === "ne" ? tab.ne : tab.en}
          </Link>
        );
      })}
    </nav>
  );
}

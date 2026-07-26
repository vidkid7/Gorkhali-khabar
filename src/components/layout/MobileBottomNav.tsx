"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";
import { useLanguage } from "@/contexts/LanguageContext";
import { useState, useEffect, useRef } from "react";
import { CalendarDays, House, ListStart, Search, UserRound, X } from "lucide-react";
import { MOBILE_NAV_ITEMS } from "@/components/layout/navigation-data";
import { useLaravelAuth } from "@/contexts/LaravelAuthContext";

export function MobileBottomNav() {
  const pathname = usePathname();
  const { language, t } = useLanguage();
  const { data: session } = useLaravelAuth();
  const [searchOpen, setSearchOpen] = useState(false);
  const [query, setQuery] = useState("");
  const inputRef = useRef<HTMLInputElement>(null);

  useEffect(() => {
    if (searchOpen && inputRef.current) {
      setTimeout(() => inputRef.current?.focus(), 100);
    }
  }, [searchOpen]);

  if (pathname?.startsWith("/admin")) return null;

  const isActive = (href: string) =>
    href === "/" ? pathname === "/" : pathname?.startsWith(href);

  const mn = (ne: string, en: string) => language === "ne" ? ne : en;

  const navItems = MOBILE_NAV_ITEMS.map((item) => {
    if (item.key === "home") {
      return { ...item, icon: <House className="h-6 w-6" />, label: t("nav.home") };
    }
    if (item.key === "latest") {
      return { ...item, icon: <ListStart className="h-6 w-6" />, label: t("nav.latest") };
    }
    if (item.key === "search") {
      return { ...item, icon: <Search className="h-6 w-6" />, label: t("nav.search"), action: () => setSearchOpen(true) };
    }
    if (item.key === "patro") {
      return { ...item, icon: <CalendarDays className="h-6 w-6" />, label: t("nav.patro") };
    }
    return {
      ...item,
      href: session?.user ? item.href : "/auth/login?callbackUrl=%2Fprofile",
      icon: <UserRound className="h-6 w-6" />,
      label: t("nav.profile"),
    };
  });

  return (
    <>
      {/* Search overlay */}
      {searchOpen && (
        <div
          className="fixed inset-0 z-[200] flex flex-col"
          style={{ background: "rgba(0,0,0,0.75)", backdropFilter: "blur(6px)" }}
        >
          <div className="flex-1" onClick={() => setSearchOpen(false)} />
          <div
            className="mx-3 mb-[calc(4.5rem+env(safe-area-inset-bottom))] rounded-2xl shadow-2xl overflow-hidden"
            style={{ background: "var(--surface)", border: "1px solid var(--border)" }}
          >
            <div
              className="flex items-center gap-3 px-4 py-3"
              style={{ borderBottom: "1px solid var(--border)" }}
            >
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth={2}
                className="w-5 h-5 shrink-0" style={{ color: "var(--muted)" }}>
                <circle cx="11" cy="11" r="8" />
                <line x1="21" y1="21" x2="16.65" y2="16.65" strokeLinecap="round" />
              </svg>
              <input
                ref={inputRef}
                value={query}
                onChange={(e) => setQuery(e.target.value)}
                onKeyDown={(e) => {
                  if (e.key === "Enter" && query.trim()) {
                    window.location.href = `/search?q=${encodeURIComponent(query.trim())}`;
                  }
                  if (e.key === "Escape") setSearchOpen(false);
                }}
                placeholder={t("common.searchNews")}
                className="flex-1 bg-transparent text-base outline-none"
                style={{ color: "var(--foreground)" }}
              />
              <button onClick={() => setSearchOpen(false)}
                className="shrink-0 p-1 rounded-lg"
                style={{ color: "var(--muted)" }}
                aria-label={t("common.close")}>
                <X className="h-5 w-5" />
              </button>
            </div>
            <div className="px-4 py-3">
              <p className="text-xs mb-2" style={{ color: "var(--muted)" }}>
                {mn("लोकप्रिय खोजहरू", "Popular searches")}
              </p>
              {["राजनीति", "खेलकुद", "अर्थ", "प्रविधि"].map((tag) => (
                <button
                  key={tag}
                  onClick={() => { window.location.href = `/search?q=${encodeURIComponent(tag)}`; }}
                  className="inline-block mr-2 mb-2 px-3 py-1 rounded-full text-sm"
                  style={{ background: "var(--surface-alt)", color: "var(--foreground)", border: "1px solid var(--border)" }}
                >
                  {tag}
                </button>
              ))}
            </div>
          </div>
        </div>
      )}

      {/* Bottom nav bar */}
      <nav className="mobile-bottom-nav">
        {navItems.map((item) => {
          const active = item.key === "latest" ? pathname === "/" : item.href ? isActive(item.href) : false;
          const content = (
            <span
              className="flex flex-col items-center gap-0.5 py-2 px-1 min-w-0 transition-all"
              style={{ color: active ? "var(--accent)" : "var(--muted)" }}
            >
              <span className={`transition-transform ${active ? "scale-110" : ""}`}>{item.icon}</span>
              <span className="text-[10px] font-medium leading-none truncate">{item.label}</span>
            </span>
          );
          if ("action" in item) {
            return (
              <button key={item.key} onClick={item.action} className="flex-1 flex justify-center">
                {content}
              </button>
            );
          }
          return (
            <Link key={item.key} href={item.href!} className="flex-1 flex justify-center">
              {content}
            </Link>
          );
        })}
      </nav>

      {/* Spacer so content isn't hidden behind the nav */}
      <div className="mobile-bottom-nav-spacer" />
    </>
  );
}

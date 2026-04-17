"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";
import { useState, useEffect } from "react";
import { FontSizeProvider, useFontSize } from "@/contexts/FontSizeContext";

const navItems = [
  { href: "/admin",             labelNe: "ड्यासबोर्ड",     labelEn: "Dashboard",      icon: "📊" },
  { href: "/admin/articles",    labelNe: "लेखहरू",         labelEn: "Articles",       icon: "📝" },
  { href: "/admin/categories",  labelNe: "वर्गहरू",        labelEn: "Categories",     icon: "📁" },
  { href: "/admin/tags",        labelNe: "ट्यागहरू",       labelEn: "Tags",           icon: "🏷️" },
  { href: "/admin/comments",    labelNe: "टिप्पणीहरू",     labelEn: "Comments",       icon: "💬" },
  { href: "/admin/media",       labelNe: "मिडिया",         labelEn: "Media",          icon: "🖼️" },
  { href: "/admin/ads",         labelNe: "विज्ञापन",       labelEn: "Ads",            icon: "📢" },
  { href: "/admin/sports",      labelNe: "खेलकुद",         labelEn: "Sports",         icon: "⚽" },
  { href: "/admin/reels",       labelNe: "रिल्स",          labelEn: "Reels",          icon: "🎬" },
  { href: "/admin/galleries",   labelNe: "ग्यालेरीहरू",    labelEn: "Galleries",      icon: "🖼️" },
  { href: "/admin/breaking-news", labelNe: "ब्रेकिङ न्युज", labelEn: "Breaking News", icon: "🔴" },
  { href: "/admin/analytics",   labelNe: "विश्लेषण",       labelEn: "Analytics",      icon: "📈" },
  { href: "/admin/holidays",    labelNe: "बिदाहरू",        labelEn: "Holidays",       icon: "🏖️" },
  { href: "/admin/gold-silver", labelNe: "सुन-चाँदी",      labelEn: "Gold-Silver",    icon: "🪙" },
  { href: "/admin/forex",       labelNe: "विनिमय दर",      labelEn: "Forex Rates",    icon: "💱" },
  { href: "/admin/rashifal",    labelNe: "राशिफल",         labelEn: "Rashifal",       icon: "♈" },
  { href: "/admin/users",       labelNe: "प्रयोगकर्ता",    labelEn: "Users",          icon: "👥" },
  { href: "/admin/settings",    labelNe: "सेटिङ",          labelEn: "Settings",       icon: "⚙️" },
  { href: "/admin/audit-log",   labelNe: "अडिट लग",        labelEn: "Audit Log",      icon: "📋" },
];

function FontSizerInline() {
  const { increase, decrease, reset, canIncrease, canDecrease } = useFontSize();
  const btnCls =
    "inline-flex items-center justify-center w-7 h-7 rounded text-xs font-bold border transition-all select-none";
  const activeCls = "border-border text-foreground hover:bg-surface-alt";
  const disabledCls = "opacity-30 cursor-not-allowed border-transparent";

  return (
    <div className="flex items-center gap-1 justify-center py-2">
      <button onClick={decrease} disabled={!canDecrease} title="Smaller text"
        className={`${btnCls} ${activeCls} ${!canDecrease ? disabledCls : ""}`}
        aria-label="Decrease font size">
        <span style={{ fontSize: "10px" }}>A-</span>
      </button>
      <button onClick={reset} title="Reset text size"
        className={`${btnCls} ${activeCls}`}
        aria-label="Reset font size">
        <span style={{ fontSize: "11px" }}>A</span>
      </button>
      <button onClick={increase} disabled={!canIncrease} title="Larger text"
        className={`${btnCls} ${activeCls} ${!canIncrease ? disabledCls : ""}`}
        aria-label="Increase font size">
        <span style={{ fontSize: "12px" }}>A+</span>
      </button>
    </div>
  );
}

function SidebarContent() {
  const pathname = usePathname();
  const [open, setOpen] = useState(false);
  const [theme, setTheme] = useState<"light" | "dark">("light");
  const [lang, setLang] = useState<"ne" | "en">("ne");

  useEffect(() => {
    const t = localStorage.getItem("adminTheme") as "light" | "dark" | null;
    if (t) setTheme(t);
    const l = localStorage.getItem("adminLang") as "ne" | "en" | null;
    if (l) setLang(l);
  }, []);

  function toggleTheme() {
    const next = theme === "light" ? "dark" : "light";
    setTheme(next);
    localStorage.setItem("adminTheme", next);
    document.documentElement.setAttribute("data-admin-theme", next);
    const wrapper = document.querySelector("[data-admin-theme]");
    if (wrapper) wrapper.setAttribute("data-admin-theme", next);
  }

  function toggleLang() {
    const next = lang === "ne" ? "en" : "ne";
    setLang(next);
    localStorage.setItem("adminLang", next);
  }

  function isActive(href: string) {
    if (href === "/admin") return pathname === "/admin";
    return pathname.startsWith(href);
  }

  return (
    <>
      {/* Mobile toggle */}
      <button
        onClick={() => setOpen(!open)}
        className="fixed top-4 left-4 z-50 md:hidden p-2 rounded-lg shadow-md"
        style={{ background: "var(--surface)", color: "var(--foreground)", border: "1px solid var(--border)" }}
        aria-label="Toggle sidebar"
      >
        {open ? "✕" : "☰"}
      </button>

      <aside
        className={`fixed md:static inset-y-0 left-0 z-40 flex flex-col transition-transform duration-200 ${
          open ? "translate-x-0" : "-translate-x-full md:translate-x-0"
        }`}
        style={{
          width: "15rem",
          background: "var(--sidebar-bg)",
          borderRight: "1px solid var(--border)",
          boxShadow: "var(--shadow-md)",
        }}
      >
        {/* Brand Header */}
        <div
          className="px-4 py-3 border-b flex items-center justify-between"
          style={{ borderColor: "var(--border)" }}
        >
          <Link href="/admin" className="flex items-center gap-2.5" style={{ color: "var(--foreground)" }}>
            <div
              className="w-8 h-8 rounded-lg flex items-center justify-center text-white font-bold text-sm"
              style={{ background: "var(--accent)" }}
            >
              स
            </div>
            <div className="leading-tight">
              <p className="text-sm font-bold">Admin Panel</p>
              <p className="text-[10px]" style={{ color: "var(--muted)" }}>प्रशासन</p>
            </div>
          </Link>
          {/* Language toggle in header */}
          <button
            onClick={toggleLang}
            className="text-[10px] font-bold px-1.5 py-1 rounded border transition-colors"
            style={{ borderColor: "var(--border)", color: "var(--muted)" }}
            title="Toggle language labels"
          >
            {lang === "ne" ? "EN" : "ने"}
          </button>
        </div>

        {/* Navigation */}
        <nav className="flex-1 overflow-y-auto py-2" style={{ scrollbarWidth: "thin" }}>
          {navItems.map((item) => {
            const active = isActive(item.href);
            return (
              <Link
                key={item.href}
                href={item.href}
                onClick={() => setOpen(false)}
                className="flex items-center gap-3 px-3 py-2 mx-2 rounded-lg text-sm font-medium transition-all"
                style={{
                  background: active ? "var(--accent)" : "transparent",
                  color: active ? "#fff" : "var(--muted)",
                  marginBottom: "1px",
                }}
              >
                <span className="text-base shrink-0">{item.icon}</span>
                <div className="min-w-0 flex-1">
                  <span className="block truncate">
                    {lang === "ne" ? item.labelNe : item.labelEn}
                  </span>
                  {lang === "ne" && (
                    <span className="block text-[10px] truncate opacity-60">{item.labelEn}</span>
                  )}
                </div>
              </Link>
            );
          })}
        </nav>

        {/* Footer Controls */}
        <div className="px-3 pb-3 pt-2 border-t space-y-1.5" style={{ borderColor: "var(--border)" }}>
          {/* Font size controls */}
          <div
            className="rounded-lg px-2"
            style={{ background: "var(--surface-alt)", border: "1px solid var(--border)" }}
          >
            <p className="text-[10px] text-center pt-1.5 font-medium" style={{ color: "var(--muted)" }}>
              अक्षर आकार / Font Size
            </p>
            <FontSizerInline />
          </div>

          {/* Theme toggle */}
          <button
            onClick={toggleTheme}
            className="flex items-center gap-2 w-full px-3 py-2 rounded-lg text-sm transition-colors"
            style={{ background: "var(--surface)", color: "var(--foreground)", border: "1px solid var(--border)" }}
          >
            {theme === "light" ? "🌙" : "☀️"}
            <span>{theme === "light" ? "Dark Mode" : "Light Mode"}</span>
          </button>

          {/* Back to site */}
          <Link
            href="/"
            className="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-colors"
            style={{ color: "var(--muted)" }}
          >
            ← साइटमा फर्कनुहोस्
          </Link>
        </div>
      </aside>

      {/* Mobile overlay */}
      {open && (
        <div
          className="fixed inset-0 z-30 bg-black/50 md:hidden"
          onClick={() => setOpen(false)}
        />
      )}
    </>
  );
}

export function AdminSidebar() {
  return (
    <FontSizeProvider>
      <SidebarContent />
    </FontSizeProvider>
  );
}


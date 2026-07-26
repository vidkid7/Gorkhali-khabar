"use client";

import { FormEvent, ReactNode, useEffect, useState } from "react";
import Link from "next/link";
import { ArrowUpRight, Mail, MapPin, Phone, Rss, Send } from "lucide-react";
import { useLanguage } from "@/contexts/LanguageContext";
import { useLaravelAuth } from "@/contexts/LaravelAuthContext";
import { useSiteConfig } from "@/contexts/SiteConfigContext";
import { adminPath } from "@/lib/admin-path";
import { BrandWordmark } from "@/components/layout/BrandWordmark";
import {
  getPublicNavItems,
  type PublicNavItem,
} from "@/components/layout/navigation-data";
import { AdSlot } from "@/components/ads/AdSlot";

const FOOTER_SECTIONS = {
  news: [
    { key: "samachar", href: "/categories/samachar" },
    { key: "rajniti", href: "/categories/rajniti" },
    { key: "bichar", href: "/categories/bichar" },
    { key: "antarvaarta", href: "/categories/antarvaarta" },
    { key: "antarrashtriya", href: "/categories/antarrashtriya" },
  ],
  business: [
    { key: "arthatantra", href: "/categories/arthatantra" },
    { key: "shareMarket", href: "/share-market" },
    { key: "prabidhi", href: "/categories/prabidhi" },
    { key: "finance", href: "/finance" },
  ],
  lifestyle: [
    { key: "feature", href: "/categories/feature" },
    { key: "entertainment", href: "/categories/bichitra" },
    { key: "sahitya", href: "/categories/sahitya" },
    { key: "khelkud", href: "/categories/khelkud" },
  ],
  special: [
    { key: "coverStory", href: "/categories/cover-story" },
    { key: "video", href: "/categories/video" },
    { key: "photoGallery", href: "/categories/photo-gallery" },
    { key: "patro", href: "/patro" },
    { key: "horoscope", href: "/rashifal" },
  ],
} as const;

const FACEBOOK_ICON = (
  <svg className="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
  </svg>
);
const X_ICON = (
  <svg className="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
    <path d="M18.901 1.153h3.68l-8.04 9.19L24 22.846h-7.406l-5.8-7.584-6.638 7.584H.474l8.6-9.83L0 1.154h7.594l5.243 6.932zM17.61 20.644h2.039L6.486 3.24H4.298z" />
  </svg>
);
const YOUTUBE_ICON = (
  <svg className="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
    <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12z" />
  </svg>
);
const INSTAGRAM_ICON = (
  <svg className="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 1 0 0 12.324 6.162 6.162 0 0 0 0-12.324zM12 16a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm6.406-11.845a1.44 1.44 0 1 0 0 2.881 1.44 1.44 0 0 0 0-2.881z" />
  </svg>
);
const TIKTOK_ICON = (
  <svg className="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
    <path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z" />
  </svg>
);

const SOCIAL_LINKS: Array<{
  key: "social_facebook" | "social_twitter" | "social_youtube" | "social_instagram" | "social_tiktok";
  label: string;
  icon: ReactNode;
}> = [
  { key: "social_facebook", label: "Facebook", icon: FACEBOOK_ICON },
  { key: "social_twitter", label: "X (Twitter)", icon: X_ICON },
  { key: "social_youtube", label: "YouTube", icon: YOUTUBE_ICON },
  { key: "social_instagram", label: "Instagram", icon: INSTAGRAM_ICON },
  { key: "social_tiktok", label: "TikTok", icon: TIKTOK_ICON },
];

function FooterHeading({
  eyebrow,
  title,
  description,
}: {
  eyebrow: string;
  title: string;
  description?: string;
}) {
  return (
    <div className="grid gap-1.5">
      <span className="text-[10px] font-bold uppercase tracking-[0.2em] text-accent">
        {eyebrow}
      </span>
      <h2
        className="text-[1.4rem] font-black leading-tight text-gray-900 sm:text-2xl"
        style={{ fontFamily: "var(--font-nepali-serif)" }}
      >
        {title}
      </h2>
      {description && (
        <p className="max-w-2xl text-sm leading-6 text-gray-600">
          {description}
        </p>
      )}
    </div>
  );
}

function FooterColumn({
  title,
  items,
  translate,
  isNe,
}: {
  title: string;
  items: ReadonlyArray<Pick<PublicNavItem, "key" | "href" | "label" | "label_en">>;
  translate: (key: string) => string;
  isNe: boolean;
}) {
  return (
    <div className="min-w-0">
      <h3 className="mb-4 flex items-center gap-2 text-[11px] font-bold uppercase tracking-[0.16em] text-gray-900">
        <span
          className="inline-block h-0.5 w-4"
          style={{ background: "var(--accent)" }}
          aria-hidden="true"
        />
        {title}
      </h3>
      <ul className="grid gap-2.5">
        {items.map((item) => (
          <li key={item.key}>
            <Link
              href={item.href}
              className="group inline-flex max-w-full items-center gap-2 text-sm text-gray-600 transition-colors hover:text-gray-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent"
            >
              <span className="truncate">
                {item.label
                  ? isNe
                    ? item.label
                    : item.label_en || item.label
                  : translate(`nav.${item.key}`)}
              </span>
              <ArrowUpRight
                className="h-3.5 w-3.5 shrink-0 opacity-0 transition-all duration-200 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 group-hover:opacity-100"
                aria-hidden="true"
              />
            </Link>
          </li>
        ))}
      </ul>
    </div>
  );
}

function ContactCard({
  icon,
  label,
  value,
}: {
  icon: ReactNode;
  label: string;
  value: string;
}) {
  if (!value) return null;

  return (
    <div className="flex min-w-0 items-start gap-3 rounded-lg border border-gray-200 bg-gray-50 p-3">
      <span className="grid h-9 w-9 shrink-0 place-items-center rounded-md bg-red-50 text-accent">
        {icon}
      </span>
      <div className="min-w-0">
        <span className="block text-[10px] font-bold uppercase tracking-[0.16em] text-gray-500">
          {label}
        </span>
        <span className="block truncate text-sm font-semibold text-gray-900">
          {value}
        </span>
      </div>
    </div>
  );
}

function SocialButton({
  href,
  label,
  children,
}: {
  href?: string;
  label: string;
  children: ReactNode;
}) {
  if (!href) return null;
  return (
    <a
      href={href}
      target="_blank"
      rel="noopener noreferrer"
      aria-label={label}
      className="inline-flex h-10 w-10 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-700 shadow-sm transition-all hover:-translate-y-0.5 hover:border-accent hover:bg-accent hover:text-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent"
    >
      {children}
    </a>
  );
}

export function Footer() {
  const { language, t } = useLanguage();
  const { data: session } = useLaravelAuth();
  const { config } = useSiteConfig();
  const [email, setEmail] = useState("");
  const [status, setStatus] = useState<"idle" | "submitting" | "success" | "error">("idle");
  const [message, setMessage] = useState("");
  const [footerMenus, setFooterMenus] = useState<PublicNavItem[]>([]);

  const isNe = language === "ne";
  const canManage = Boolean(session?.user?.role && ["ADMIN", "EDITOR", "AUTHOR"].includes(session.user.role));
  const siteName = isNe ? config.site_name.ne : config.site_name.en;
  const tagline = isNe ? config.site_tagline.ne : config.site_tagline.en;
  const address = isNe ? config.contact_address.ne : config.contact_address.en;
  const copyrightRaw = isNe ? config.copyright_text.ne : config.copyright_text.en;
  const copyright = copyrightRaw.replace(
    "{year}",
    new Date().getFullYear().toString(),
  );
  const sectionTitle = (ne: string, en: string) => (isNe ? ne : en);

  const hasContact =
    Boolean(config.contact_phone) ||
    Boolean(config.contact_email) ||
    Boolean(address);
  const hasSocial = SOCIAL_LINKS.some((link) => Boolean(config[link.key]));
  const managedFooterGroups = footerMenus.length
    ? Array.from({ length: 4 }, (_, groupIndex) =>
        footerMenus.filter((_, itemIndex) => itemIndex % 4 === groupIndex),
      )
    : null;

  useEffect(() => {
    let active = true;

    getPublicNavItems(undefined, "footer")
      .then((items) => {
        if (active && items.length) setFooterMenus(items);
      })
      .catch(() => {
        // Keep the bundled links available if the API is temporarily unavailable.
      });

    return () => {
      active = false;
    };
  }, []);

  async function handleSubscribe(event: FormEvent<HTMLFormElement>) {
    event.preventDefault();
    const trimmed = email.trim();
    if (!trimmed) return;

    setStatus("submitting");
    setMessage("");

    try {
      const res = await fetch("/api/v1/newsletter", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ email: trimmed, language }),
      });
      const data = await res.json();

      if (!res.ok || !data.success) {
        throw new Error(data.error || "Subscribe failed");
      }

      setStatus("success");
      setEmail("");
      setMessage(isNe ? "सदस्यता सुरक्षित भयो।" : "Subscription saved.");
    } catch {
      setStatus("error");
      setMessage(
        isNe
          ? "अहिले सदस्यता लिन सकिएन।"
          : "Could not subscribe right now.",
      );
    }
  }

  return (
    <footer
      className="site-footer mt-2 bg-white text-gray-700"
      aria-labelledby="footer-masthead"
    >
      {/* Brand accent rule */}
      <div style={{ height: 3, background: "var(--accent)" }} />

      {/* ─── Masthead bar with brand ─── */}
      <div className="border-b border-gray-200 bg-gray-50">
        <div className="mx-auto max-w-7xl px-4 py-5 sm:px-6">
          <div className="flex flex-col items-start gap-3 sm:flex-row sm:items-center sm:justify-between">
            <Link
              href="/"
              aria-label={siteName}
              className="inline-flex items-center gap-2"
              id="footer-masthead"
            >
              <span
                className="inline-block h-0.5 w-6"
                style={{ background: "var(--accent)" }}
                aria-hidden="true"
              />
              <span className="text-[11px] font-bold uppercase tracking-[0.2em] text-accent">
                {isNe ? "समाचार पोर्टल" : "News Portal"}
              </span>
            </Link>
            <div className="flex items-center gap-3">
              <Link
                href="/rss.xml"
                aria-label={isNe ? "RSS फिड" : "RSS feed"}
                className="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-gray-600 transition-colors hover:text-gray-900"
              >
                <Rss className="h-3.5 w-3.5" aria-hidden="true" />
                RSS
              </Link>
              <span className="h-3 w-px bg-gray-300" aria-hidden="true" />
              {canManage && (
                <Link
                  href={adminPath()}
                  className="text-xs font-bold uppercase tracking-wider text-gray-600 transition-colors hover:text-gray-900"
                >
                  {isNe ? "एडमिन" : "Admin"}
                </Link>
              )}
            </div>
          </div>
        </div>
      </div>

      <div className="mx-auto max-w-7xl px-4 py-7 sm:px-6">
        <AdSlot position="FOOTER" className="mx-auto max-w-[728px]" compactLabel />
      </div>

      {/* ─── Newsletter ─── */}
      <section
        className="site-footer__newsletter border-b border-gray-200 bg-white"
        aria-label={isNe ? "समाचार अलर्ट" : "News alerts"}
      >
        <div className="mx-auto max-w-7xl px-4 py-9 sm:px-6 sm:py-12">
          <div className="grid gap-6 lg:grid-cols-[minmax(0,1.05fr)_minmax(320px,0.95fr)] lg:items-center lg:gap-10">
            <FooterHeading
              eyebrow={isNe ? "समाचार अलर्ट" : "News alerts"}
              title={
                isNe
                  ? "ताजा समाचार इमेलमा पाउनुहोस्"
                  : "Get the latest news by email"
              }
              description={
                isNe
                  ? "मुख्य खबर, अपडेट र विशेष सामग्री सिधै आफ्नो इमेलमा। बिहानै दैनिक ब्रीफिङ इमेलमा।"
                  : "Top stories, updates, and selected features delivered to your inbox each morning."
              }
            />

            <form
              onSubmit={handleSubscribe}
              className="grid gap-2 sm:grid-cols-[minmax(0,1fr)_auto]"
              aria-label={isNe ? "न्यूजलेटर सदस्यता" : "Newsletter subscription"}
            >
              <label className="sr-only" htmlFor="footer-newsletter-email">
                {isNe ? "इमेल" : "Email"}
              </label>
              <div className="relative">
                <Mail
                  className="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400"
                  aria-hidden="true"
                />
                <input
                  id="footer-newsletter-email"
                  type="email"
                  value={email}
                  onChange={(event) => setEmail(event.target.value)}
                  placeholder={
                    isNe
                      ? "आफ्नो इमेल प्रविष्ट गर्नुहोस्"
                      : "Enter your email"
                  }
                  className="min-w-0 w-full rounded-md border border-gray-300 bg-white py-3 pl-9 pr-4 text-sm text-gray-900 placeholder:text-gray-400 focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/40"
                  required
                  aria-describedby="footer-newsletter-hint"
                />
              </div>
              <button
                type="submit"
                disabled={status === "submitting"}
                className="inline-flex items-center justify-center gap-1.5 rounded-md bg-accent px-5 py-3 text-sm font-semibold text-white transition-colors hover:bg-accent-hover disabled:cursor-wait disabled:opacity-70"
              >
                {status === "submitting" ? (
                  <>
                    <span
                      className="inline-block h-3 w-3 animate-spin rounded-full border-2 border-white/40 border-t-white"
                      aria-hidden="true"
                    />
                    {isNe ? "पठाउँदै..." : "Saving..."}
                  </>
                ) : (
                  <>
                    {isNe ? "सदस्यता लिनुहोस्" : "Subscribe"}
                    <Send className="h-4 w-4" aria-hidden="true" />
                  </>
                )}
              </button>
              {message && (
                <p
                  role="status"
                  id="footer-newsletter-hint"
                  className={`text-xs font-medium sm:col-span-2 ${
                    status === "success"
                      ? "text-success"
                      : status === "error"
                        ? "text-error"
                        : "text-gray-600"
                  }`}
                >
                  {message}
                </p>
              )}
            </form>
          </div>
        </div>
      </section>

      {/* ─── Brand + Contact + Navigation ─── */}
      <div className="site-footer__main bg-white">
        <div className="mx-auto max-w-7xl px-4 py-12 sm:px-6 sm:py-14">
          <div className="grid gap-12 lg:grid-cols-[minmax(0,1.05fr)_minmax(0,1.95fr)] lg:gap-14">
            {/* Brand & contact column */}
            <div className="site-footer__brand min-w-0 space-y-7">
              <div className="space-y-4">
                <Link
                  href="/"
                  aria-label={siteName}
                  className="inline-flex max-w-full items-center rounded-md border border-gray-200 bg-white px-3 py-2 shadow-sm"
                >
                  <BrandWordmark className="h-9 w-auto sm:h-10" />
                </Link>
                {tagline && (
                  <p className="max-w-md text-sm leading-6 text-gray-600">
                    {tagline}
                  </p>
                )}
                {config.registration_number && (
                  <p className="inline-flex items-center gap-2 text-[11px] font-semibold uppercase tracking-[0.12em] text-gray-500">
                    <span
                      className="inline-block h-0.5 w-3"
                      style={{ background: "var(--accent)" }}
                      aria-hidden="true"
                    />
                    {isNe ? "दर्ता नं" : "Reg"} {config.registration_number}
                  </p>
                )}
              </div>

              {hasContact && (
                <div className="grid gap-3 sm:grid-cols-2">
                  <ContactCard
                    icon={<Phone className="h-4 w-4" />}
                    label={isNe ? "फोन" : "Phone"}
                    value={config.contact_phone ?? ""}
                  />
                  <ContactCard
                    icon={<Mail className="h-4 w-4" />}
                    label={isNe ? "इमेल" : "Email"}
                    value={config.contact_email ?? ""}
                  />
                  {address && (
                    <div className="flex min-w-0 items-start gap-3 rounded-lg border border-gray-200 bg-gray-50 p-3 sm:col-span-2">
                      <span className="grid h-9 w-9 shrink-0 place-items-center rounded-md bg-red-50 text-accent">
                        <MapPin className="h-4 w-4" />
                      </span>
                      <div className="min-w-0">
                        <span className="block text-[10px] font-bold uppercase tracking-[0.16em] text-gray-500">
                          {isNe ? "ठेगाना" : "Address"}
                        </span>
                        <span className="block text-sm font-semibold text-gray-900">
                          {address}
                        </span>
                      </div>
                    </div>
                  )}
                </div>
              )}

              {hasSocial && (
                <div className="flex items-center gap-2 pt-1">
                  <span className="text-[10px] font-bold uppercase tracking-[0.18em] text-gray-500">
                    {isNe ? "हामीलाई फलो गर्नुहोस्" : "Follow us"}
                  </span>
                  <div className="flex flex-wrap gap-2">
                    {SOCIAL_LINKS.map((link) => (
                      <SocialButton
                        key={link.key}
                        href={config[link.key] ?? undefined}
                        label={link.label}
                      >
                        {link.icon}
                      </SocialButton>
                    ))}
                  </div>
                </div>
              )}
            </div>

            {/* Navigation columns */}
            <nav
              className="site-footer__links grid grid-cols-2 gap-x-6 gap-y-8 sm:grid-cols-3 xl:grid-cols-5"
              aria-label={sectionTitle(
                "फुटर नेभिगेसन",
                "Footer navigation",
              )}
            >
              <FooterColumn
                title={sectionTitle("समाचार", "News")}
                items={managedFooterGroups?.[0] ?? FOOTER_SECTIONS.news}
                translate={t}
                isNe={isNe}
              />
              <FooterColumn
                title={sectionTitle("बिजनेस", "Business")}
                items={managedFooterGroups?.[1] ?? FOOTER_SECTIONS.business}
                translate={t}
                isNe={isNe}
              />
              <FooterColumn
                title={sectionTitle("जीवनशैली", "Lifestyle")}
                items={managedFooterGroups?.[2] ?? FOOTER_SECTIONS.lifestyle}
                translate={t}
                isNe={isNe}
              />
              <FooterColumn
                title={sectionTitle("विशेष", "Special")}
                items={managedFooterGroups?.[3] ?? FOOTER_SECTIONS.special}
                translate={t}
                isNe={isNe}
              />
              <div className="min-w-0">
                <h3 className="mb-4 flex items-center gap-2 text-[11px] font-bold uppercase tracking-[0.16em] text-gray-900">
                  <span
                    className="inline-block h-0.5 w-4"
                    style={{ background: "var(--accent)" }}
                    aria-hidden="true"
                  />
                  {sectionTitle("पाठक सेवा", "Reader services")}
                </h3>
                <ul className="grid gap-2.5">
                  <li>
                    <Link
                      href="/about"
                      className="group inline-flex max-w-full items-center gap-2 text-sm text-gray-600 transition-colors hover:text-gray-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent"
                    >
                      <span className="truncate">{t("footer.about")}</span>
                      <ArrowUpRight
                        className="h-3.5 w-3.5 shrink-0 opacity-0 transition-all duration-200 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 group-hover:opacity-100"
                        aria-hidden="true"
                      />
                    </Link>
                  </li>
                  <li>
                    <Link
                      href="/privacy-policy"
                      className="group inline-flex max-w-full items-center gap-2 text-sm text-gray-600 transition-colors hover:text-gray-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent"
                    >
                      <span className="truncate">
                        {t("footer.privacyPolicy")}
                      </span>
                      <ArrowUpRight
                        className="h-3.5 w-3.5 shrink-0 opacity-0 transition-all duration-200 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 group-hover:opacity-100"
                        aria-hidden="true"
                      />
                    </Link>
                  </li>
                  <li>
                    <Link
                      href="/terms-of-service"
                      className="group inline-flex max-w-full items-center gap-2 text-sm text-gray-600 transition-colors hover:text-gray-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent"
                    >
                      <span className="truncate">
                        {t("footer.termsOfService")}
                      </span>
                      <ArrowUpRight
                        className="h-3.5 w-3.5 shrink-0 opacity-0 transition-all duration-200 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 group-hover:opacity-100"
                        aria-hidden="true"
                      />
                    </Link>
                  </li>
                  <li>
                    <Link
                      href="/cookie-policy"
                      className="group inline-flex max-w-full items-center gap-2 text-sm text-gray-600 transition-colors hover:text-gray-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent"
                    >
                      <span className="truncate">
                        {t("footer.cookiePolicy")}
                      </span>
                      <ArrowUpRight
                        className="h-3.5 w-3.5 shrink-0 opacity-0 transition-all duration-200 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 group-hover:opacity-100"
                        aria-hidden="true"
                      />
                    </Link>
                  </li>
                  <li>
                    <Link
                      href="/rss.xml"
                      className="group inline-flex max-w-full items-center gap-2 text-sm text-gray-600 transition-colors hover:text-gray-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent"
                    >
                      <span className="truncate">RSS Feed</span>
                      <ArrowUpRight
                        className="h-3.5 w-3.5 shrink-0 opacity-0 transition-all duration-200 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 group-hover:opacity-100"
                        aria-hidden="true"
                      />
                    </Link>
                  </li>
                </ul>
              </div>
            </nav>
          </div>
        </div>
      </div>

      {/* ─── Bottom bar ─── */}
      <div className="site-footer__legal border-t border-gray-200 bg-gray-50">
        <div className="mx-auto max-w-7xl px-4 py-6 text-center text-xs font-medium text-gray-600 sm:px-6 md:flex md:items-center md:justify-between md:text-left">
          <p className="tracking-wide">{copyright}</p>
          <div className="mt-3 flex flex-wrap items-center justify-center gap-x-5 gap-y-2 md:mt-0">
            <Link
              href="/about"
              className="text-xs font-bold uppercase tracking-wider text-gray-600 transition-colors hover:text-gray-900"
            >
              {t("footer.about")}
            </Link>
            <span className="h-3 w-px bg-gray-300" aria-hidden="true" />
            <Link
              href="/privacy-policy"
              className="text-xs font-bold uppercase tracking-wider text-gray-600 transition-colors hover:text-gray-900"
            >
              {t("footer.privacyPolicy")}
            </Link>
            {canManage && (
              <>
                <span className="h-3 w-px bg-gray-300" aria-hidden="true" />
                <Link
                  href={adminPath()}
                  className="text-xs font-bold uppercase tracking-wider text-gray-600 transition-colors hover:text-gray-900"
                >
                  {isNe ? "एडमिन प्यानल" : "Admin Panel"}
                </Link>
              </>
            )}
            <span className="text-xs text-gray-500">
              {isNe ? "AashaTech द्वारा व्यवस्थापन" : "Managed by AashaTech"}
            </span>
          </div>
        </div>
      </div>
    </footer>
  );
}

"use client";

import Link from "next/link";
import Image from "next/image";
import { useLanguage } from "@/contexts/LanguageContext";
import { useSiteConfig } from "@/contexts/SiteConfigContext";

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
  ],
  lifestyle: [
    { key: "feature", href: "/categories/feature" },
    { key: "entertainment", href: "/categories/bichitra" },
    { key: "sahitya", href: "/categories/sahitya" },
    { key: "khelkud", href: "/categories/khelkud" },
    { key: "saptaahanta", href: "/categories/saptaahanta" },
  ],
  special: [
    { key: "coverStory", href: "/categories/cover-story" },
    { key: "video", href: "/categories/video" },
    { key: "photoGallery", href: "/categories/photo-gallery" },
    { key: "patro", href: "/patro" },
    { key: "horoscope", href: "/rashifal" },
    { key: "finance", href: "/finance" },
  ],
};

export function Footer() {
  const { language, t } = useLanguage();
  const { config } = useSiteConfig();

  const siteName = language === "en" ? config.site_name.en : config.site_name.ne;
  const tagline = language === "en" ? config.site_tagline.en : config.site_tagline.ne;
  const address = language === "en" ? config.contact_address.en : config.contact_address.ne;
  const copyrightRaw = language === "en" ? config.copyright_text.en : config.copyright_text.ne;
  const copyright = copyrightRaw.replace("{year}", new Date().getFullYear().toString());

  const sectionTitle = (ne: string, en: string) => language === "ne" ? ne : en;

  return (
    <footer className="bg-footer-bg text-footer-text">
      {/* Newsletter Section */}
      <div className="border-b border-footer-border">
        <div className="mx-auto max-w-7xl px-4 py-10">
          <div className="flex flex-col md:flex-row items-center justify-between gap-6">
            <div className="text-center md:text-left">
              <h3 className="text-2xl font-bold text-footer-heading mb-2">
                {language === "ne" ? "समाचार सदस्यता लिनुहोस्" : "Subscribe to Newsletter"}
              </h3>
              <p className="text-footer-text text-sm">
                {language === "ne" 
                  ? "ताजा समाचार र अपडेट सिधै आफ्नो इमेलमा प्राप्त गर्नुहोस्" 
                  : "Get latest news and updates directly to your inbox"}
              </p>
            </div>
            <form className="flex gap-2 w-full md:w-auto">
              <input
                type="email"
                placeholder={language === "ne" ? "आफ्नो इमेल प्रविष्ट गर्नुहोस्" : "Enter your email"}
                className="flex-1 md:w-80 px-4 py-3 rounded-lg bg-footer-input-bg border border-footer-border text-footer-heading placeholder:text-footer-text focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent"
              />
              <button
                type="submit"
                className="px-6 py-3 bg-accent hover:bg-accent-hover text-white font-semibold rounded-lg transition-colors whitespace-nowrap"
              >
                {language === "ne" ? "सदस्यता लिनुहोस्" : "Subscribe"}
              </button>
            </form>
          </div>
        </div>
      </div>

      {/* Main Footer Content */}
      <div className="mx-auto max-w-7xl px-4 py-12">
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-8 lg:gap-6">
          {/* Brand Column - Spans 2 columns */}
          <div className="sm:col-span-2 lg:col-span-2 space-y-5">
            <div className="flex items-center gap-3">
              {config.site_logo ? (
                <Image 
                  src={config.site_logo} 
                  alt={siteName} 
                  width={50} 
                  height={50} 
                  className="rounded-lg shadow-md"
                  onError={(e) => { (e.target as HTMLImageElement).style.display = 'none'; }} 
                />
              ) : (
                <div className="w-12 h-12 rounded-lg bg-accent flex items-center justify-center text-white font-bold text-xl shadow-lg">
                  स
                </div>
              )}
              <div>
                <h2 className="text-2xl font-bold text-footer-heading">{siteName}</h2>
                {config.registration_number && (
                  <p className="text-xs text-footer-text">
                    {language === "ne" ? "दर्ता नं:" : "Reg:"} {config.registration_number}
                  </p>
                )}
              </div>
            </div>
            
            <p className="text-sm leading-relaxed text-footer-text">
              {tagline}
            </p>

            {/* Contact Info with Icons */}
            <div className="space-y-3">
              {config.contact_phone && (
                <div className="flex items-start gap-3 text-sm text-footer-text group">
                  <div className="w-10 h-10 rounded-lg bg-footer-icon-bg flex items-center justify-center shrink-0 group-hover:bg-accent transition-colors">
                    <svg className="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                      <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/>
                    </svg>
                  </div>
                  <div className="pt-2">
                    <p className="font-medium text-footer-heading">{language === "ne" ? "फोन" : "Phone"}</p>
                    <p>{config.contact_phone}</p>
                  </div>
                </div>
              )}
              
              {config.contact_email && (
                <div className="flex items-start gap-3 text-sm text-footer-text group">
                  <div className="w-10 h-10 rounded-lg bg-footer-icon-bg flex items-center justify-center shrink-0 group-hover:bg-accent transition-colors">
                    <svg className="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                      <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                    </svg>
                  </div>
                  <div className="pt-2">
                    <p className="font-medium text-footer-heading">{language === "ne" ? "इमेल" : "Email"}</p>
                    <p>{config.contact_email}</p>
                  </div>
                </div>
              )}
              
              {address && (
                <div className="flex items-start gap-3 text-sm text-footer-text group">
                  <div className="w-10 h-10 rounded-lg bg-footer-icon-bg flex items-center justify-center shrink-0 group-hover:bg-accent transition-colors">
                    <svg className="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                      <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                    </svg>
                  </div>
                  <div className="pt-2">
                    <p className="font-medium text-footer-heading">{language === "ne" ? "ठेगाना" : "Address"}</p>
                    <p>{address}</p>
                  </div>
                </div>
              )}
            </div>

            {/* Social Media Links */}
            <div>
              <p className="text-sm font-semibold text-footer-heading mb-3">
                {language === "ne" ? "हामीलाई फलो गर्नुहोस्" : "Follow Us"}
              </p>
              <div className="flex gap-2">
                {config.social_facebook && (
                  <a 
                    href={config.social_facebook} 
                    target="_blank" 
                    rel="noopener noreferrer"
                    className="w-10 h-10 rounded-lg bg-footer-icon-bg hover:bg-accent flex items-center justify-center transition-colors hover:scale-110" 
                    aria-label="Facebook"
                  >
                    <svg className="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                      <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                  </a>
                )}
                {config.social_twitter && (
                  <a 
                    href={config.social_twitter} 
                    target="_blank" 
                    rel="noopener noreferrer"
                    className="w-10 h-10 rounded-lg bg-footer-icon-bg hover:bg-accent flex items-center justify-center transition-colors hover:scale-110" 
                    aria-label="X/Twitter"
                  >
                    <svg className="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                      <path d="M18.901 1.153h3.68l-8.04 9.19L24 22.846h-7.406l-5.8-7.584-6.638 7.584H.474l8.6-9.83L0 1.154h7.594l5.243 6.932zM17.61 20.644h2.039L6.486 3.24H4.298z"/>
                    </svg>
                  </a>
                )}
                {config.social_youtube && (
                  <a 
                    href={config.social_youtube} 
                    target="_blank" 
                    rel="noopener noreferrer"
                    className="w-10 h-10 rounded-lg bg-footer-icon-bg hover:bg-accent flex items-center justify-center transition-colors hover:scale-110" 
                    aria-label="YouTube"
                  >
                    <svg className="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                      <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12z"/>
                    </svg>
                  </a>
                )}
                {config.social_instagram && (
                  <a 
                    href={config.social_instagram} 
                    target="_blank" 
                    rel="noopener noreferrer"
                    className="w-10 h-10 rounded-lg bg-footer-icon-bg hover:bg-accent flex items-center justify-center transition-colors hover:scale-110" 
                    aria-label="Instagram"
                  >
                    <svg className="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                      <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 1 0 0 12.324 6.162 6.162 0 0 0 0-12.324zM12 16a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm6.406-11.845a1.44 1.44 0 1 0 0 2.881 1.44 1.44 0 0 0 0-2.881z"/>
                    </svg>
                  </a>
                )}
                {config.social_tiktok && (
                  <a 
                    href={config.social_tiktok} 
                    target="_blank" 
                    rel="noopener noreferrer"
                    className="w-10 h-10 rounded-lg bg-footer-icon-bg hover:bg-accent flex items-center justify-center transition-colors hover:scale-110" 
                    aria-label="TikTok"
                  >
                    <svg className="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                      <path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/>
                    </svg>
                  </a>
                )}
              </div>
            </div>
          </div>

          {/* News Column */}
          <div>
            <h3 className="text-sm font-bold text-footer-heading uppercase tracking-wider mb-4 pb-2 border-b-2 border-accent">
              {sectionTitle("समाचार", "News")}
            </h3>
            <ul className="space-y-2.5">
              {FOOTER_SECTIONS.news.map((item) => (
                <li key={item.key}>
                  <Link 
                    href={item.href} 
                    className="text-sm text-footer-text hover:text-footer-heading hover:translate-x-1 transition-all inline-flex items-center gap-2 group"
                  >
                    <span className="w-1.5 h-1.5 rounded-full bg-footer-border group-hover:bg-accent transition-colors"></span>
                    {t(`nav.${item.key}`)}
                  </Link>
                </li>
              ))}
            </ul>
          </div>

          {/* Business Column */}
          <div>
            <h3 className="text-sm font-bold text-footer-heading uppercase tracking-wider mb-4 pb-2 border-b-2 border-accent">
              {sectionTitle("बिजनेस", "Business")}
            </h3>
            <ul className="space-y-2.5">
              {FOOTER_SECTIONS.business.map((item) => (
                <li key={item.key}>
                  <Link 
                    href={item.href} 
                    className="text-sm text-footer-text hover:text-footer-heading hover:translate-x-1 transition-all inline-flex items-center gap-2 group"
                  >
                    <span className="w-1.5 h-1.5 rounded-full bg-footer-border group-hover:bg-accent transition-colors"></span>
                    {t(`nav.${item.key}`)}
                  </Link>
                </li>
              ))}
            </ul>
          </div>

          {/* Lifestyle Column */}
          <div>
            <h3 className="text-sm font-bold text-footer-heading uppercase tracking-wider mb-4 pb-2 border-b-2 border-accent">
              {sectionTitle("जीवनशैली", "Lifestyle")}
            </h3>
            <ul className="space-y-2.5">
              {FOOTER_SECTIONS.lifestyle.map((item) => (
                <li key={item.key}>
                  <Link 
                    href={item.href} 
                    className="text-sm text-footer-text hover:text-footer-heading hover:translate-x-1 transition-all inline-flex items-center gap-2 group"
                  >
                    <span className="w-1.5 h-1.5 rounded-full bg-footer-border group-hover:bg-accent transition-colors"></span>
                    {t(`nav.${item.key}`)}
                  </Link>
                </li>
              ))}
            </ul>
          </div>

          {/* Special & Info Column */}
          <div>
            <h3 className="text-sm font-bold text-footer-heading uppercase tracking-wider mb-4 pb-2 border-b-2 border-accent">
              {sectionTitle("विशेष", "Special")}
            </h3>
            <ul className="space-y-2.5 mb-6">
              {FOOTER_SECTIONS.special.map((item) => (
                <li key={item.key}>
                  <Link 
                    href={item.href} 
                    className="text-sm text-footer-text hover:text-footer-heading hover:translate-x-1 transition-all inline-flex items-center gap-2 group"
                  >
                    <span className="w-1.5 h-1.5 rounded-full bg-footer-border group-hover:bg-accent transition-colors"></span>
                    {t(`nav.${item.key}`)}
                  </Link>
                </li>
              ))}
            </ul>
            
            <h3 className="text-sm font-bold text-footer-heading uppercase tracking-wider mb-4 pb-2 border-b border-footer-border">
              {sectionTitle("सूचना", "Info")}
            </h3>
            <ul className="space-y-2.5">
              <li>
                <Link 
                  href="/about" 
                  className="text-sm text-footer-text hover:text-footer-heading hover:translate-x-1 transition-all inline-flex items-center gap-2 group"
                >
                  <span className="w-1.5 h-1.5 rounded-full bg-footer-border group-hover:bg-accent transition-colors"></span>
                  {t("footer.about")}
                </Link>
              </li>
              <li>
                <Link 
                  href="/privacy-policy" 
                  className="text-sm text-footer-text hover:text-footer-heading hover:translate-x-1 transition-all inline-flex items-center gap-2 group"
                >
                  <span className="w-1.5 h-1.5 rounded-full bg-footer-border group-hover:bg-accent transition-colors"></span>
                  {t("footer.privacyPolicy")}
                </Link>
              </li>
              <li>
                <Link 
                  href="/terms-of-service" 
                  className="text-sm text-footer-text hover:text-footer-heading hover:translate-x-1 transition-all inline-flex items-center gap-2 group"
                >
                  <span className="w-1.5 h-1.5 rounded-full bg-footer-border group-hover:bg-accent transition-colors"></span>
                  {t("footer.termsOfService")}
                </Link>
              </li>
              <li>
                <a 
                  href="/rss.xml" 
                  className="text-sm text-footer-text hover:text-footer-heading hover:translate-x-1 transition-all inline-flex items-center gap-2 group"
                >
                  <svg className="w-3.5 h-3.5 group-hover:text-accent transition-colors" viewBox="0 0 24 24" fill="currentColor">
                    <circle cx="6.18" cy="17.82" r="2.18"/>
                    <path d="M4 4.44v2.83c7.03 0 12.73 5.7 12.73 12.73h2.83c0-8.59-6.97-15.56-15.56-15.56zm0 5.66v2.83c3.9 0 7.07 3.17 7.07 7.07h2.83c0-5.47-4.43-9.9-9.9-9.9z"/>
                  </svg>
                  RSS Feed
                </a>
              </li>
            </ul>
          </div>
        </div>
      </div>

      {/* Bottom Bar */}
      <div className="border-t border-footer-border">
        <div className="mx-auto max-w-7xl px-4 py-6">
          <div className="flex flex-col md:flex-row items-center justify-between gap-4">
            <p className="text-sm text-footer-text text-center md:text-left">
              {copyright}
            </p>
            <div className="flex items-center gap-6 text-xs text-footer-text">
              <span className="flex items-center gap-2">
                <svg className="w-4 h-4 text-primary" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                </svg>
                {language === "ne" ? "Next.js द्वारा संचालित" : "Powered by Next.js"}
              </span>
              <span className="flex items-center gap-2">
                <svg className="w-4 h-4 text-success" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M21 16v-2l-8-5V3.5c0-.83-.67-1.5-1.5-1.5S10 2.67 10 3.5V9l-8 5v2l8-2.5V19l-2 1.5V22l3.5-1 3.5 1v-1.5L13 19v-5.5l8 2.5z"/>
                </svg>
                {language === "ne" ? "Prisma द्वारा संचालित" : "Powered by Prisma"}
              </span>
            </div>
          </div>
        </div>
      </div>
    </footer>
  );
}

import type { SiteConfig } from "@/types";

export const defaultSiteConfig: SiteConfig = {
  site_name: { ne: "गोर्खाली खबर", en: "Gorkhali Khabar" },
  site_tagline: { ne: "विश्वसनीय समाचार सेवा", en: "Trusted News Service" },
  site_logo: "/icons/logo.png",
  site_favicon: "/favicon.ico",
  primary_color: "#c62828",
  contact_phone: "",
  contact_email: "",
  contact_address: { ne: "", en: "" },
  registration_number: "",
  social_facebook: "",
  social_twitter: "",
  social_youtube: "",
  social_instagram: "",
  social_tiktok: "",
  homepage_section_order: [],
  features_comments: true,
  features_bookmarks: true,
  features_reels: true,
  features_galleries: true,
  copyright_text: { ne: "© {year} समाचार पोर्टल।", en: "© {year} News Portal." },
};

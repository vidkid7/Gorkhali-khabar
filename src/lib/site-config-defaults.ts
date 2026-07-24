import type { SiteConfig } from "@/types";

export const defaultSiteConfig: SiteConfig = {
  site_name: { ne: "गोर्खाली खबर", en: "Gorkhali Khabar" },
  site_tagline: { ne: "सत्य, सन्तुलित र समयमै", en: "Truthful, balanced, and timely" },
  site_logo: "/icons/logo.png",
  site_favicon: "/icons/logo.png",
  primary_color: "#07579B",
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
  copyright_text: { ne: "© {year} गोर्खाली खबर।", en: "© {year} Gorkhali Khabar." },
};

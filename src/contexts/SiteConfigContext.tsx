"use client";

import React, { createContext, useContext, useEffect, useState } from "react";
import type { SiteConfig } from "@/types";

const defaultConfig: SiteConfig = {
  site_name: { ne: "गोर्खाली खबर", en: "Gorkhali Khabar" },
  site_tagline: { ne: "विश्वसनीय समाचार सेवा", en: "Trusted News Service" },
  site_logo: "/icons/logo.png",
  site_favicon: "/favicon.ico",
  primary_color: "#c62828",
  contact_phone: "+977-1-4XXXXXX",
  contact_email: "info@newsportal.com",
  contact_address: { ne: "काठमाडौं, नेपाल", en: "Kathmandu, Nepal" },
  registration_number: "XXX-XXX-XXXX",
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

interface SiteConfigContextType {
  config: SiteConfig;
  loading: boolean;
  refresh: () => Promise<void>;
}

const SiteConfigContext = createContext<SiteConfigContextType>({
  config: defaultConfig,
  loading: true,
  refresh: async () => {},
});

export function SiteConfigProvider({ children }: { children: React.ReactNode }) {
  const [config, setConfig] = useState<SiteConfig>(defaultConfig);
  const [loading, setLoading] = useState(true);

  const refresh = async () => {
    try {
      const res = await fetch("/api/v1/settings");
      if (res.ok) {
        const data = await res.json();
        if (data.success && data.data) {
          setConfig({ ...defaultConfig, ...data.data });
        }
      }
    } catch {
      // Use defaults on error
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    refresh();
  }, []);

  return (
    <SiteConfigContext.Provider value={{ config, loading, refresh }}>
      {children}
    </SiteConfigContext.Provider>
  );
}

export function useSiteConfig() {
  return useContext(SiteConfigContext);
}

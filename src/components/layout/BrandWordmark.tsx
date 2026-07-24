"use client";

import Image from "next/image";
import { useSiteConfig } from "@/contexts/SiteConfigContext";

type BrandWordmarkProps = {
  compact?: boolean;
  priority?: boolean;
  className?: string;
};

export function BrandWordmark({ compact = false, priority = false, className = "" }: BrandWordmarkProps) {
  const { config } = useSiteConfig();
  const siteName = config.site_name.ne || "गोर्खाली खबर";
  const logoSrc = config.site_logo || "/icons/logo.png";

  return (
    <span
      className={`brand-wordmark ${compact ? "brand-wordmark--compact" : ""} ${className}`.trim()}
      style={{ display: "inline-flex", alignItems: "center" }}
    >
      <Image
        src={logoSrc}
        alt={siteName}
        width={2506}
        height={369}
        sizes={compact ? "(max-width: 767px) 150px, 190px" : "(max-width: 767px) 190px, 420px"}
        className="h-auto w-full object-contain"
        style={{ width: "100%", height: "auto" }}
        priority={priority}
        unoptimized
      />
    </span>
  );
}
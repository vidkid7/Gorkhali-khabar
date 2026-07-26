"use client";

import { useEffect, useRef, useState } from "react";
import Image from "next/image";

export type AdPositionType =
  | "HEADER"
  | "SIDEBAR"
  | "IN_ARTICLE"
  | "FOOTER"
  | "BETWEEN_SECTIONS";

interface AdSlotProps {
  position: AdPositionType;
  className?: string;
  compactLabel?: boolean;
}

interface Ad {
  id: string;
  title: string;
  image_url: string | null;
  target_url: string;
  position: { type: string; width?: number | null; height?: number | null };
}

const PRESENTATION = {
  HEADER: { layout: "leaderboard", maxWidth: "max-w-[728px]" },
  FOOTER: { layout: "leaderboard", maxWidth: "max-w-[728px]" },
  BETWEEN_SECTIONS: { layout: "section-banner", maxWidth: "max-w-[970px]" },
  IN_ARTICLE: { layout: "article-banner", maxWidth: "max-w-[728px]" },
  SIDEBAR: { layout: "sidebar", maxWidth: "max-w-[300px]" },
} as const;

export function AdSlot({
  position,
  className = "",
  compactLabel = false,
}: AdSlotProps) {
  const [ad, setAd] = useState<Ad | null>(null);
  const [imgError, setImgError] = useState(false);
  const impressionTracked = useRef(false);

  useEffect(() => {
    async function loadAd() {
      try {
        const res = await fetch(`/api/v1/ads?position=${position}`);
        const json = await res.json();
        if (json.success && json.data?.length > 0) {
          const ads: Ad[] = json.data;
          const selected = ads[Math.floor(Math.random() * ads.length)];
          setAd(selected);
          if (!impressionTracked.current) {
            impressionTracked.current = true;
            fetch(`/api/v1/ads/${selected.id}/impression`, {
              method: "POST",
              headers: { "Content-Type": "application/json" },
              body: "{}",
            }).catch(() => {});
          }
        }
      } catch {
        // Silently fail for ads
      }
    }
    loadAd();
  }, [position]);

  if (!ad || !ad.image_url || imgError) {
    return null;
  }

  const handleClick = () => {
    fetch(`/api/v1/ads/${ad.id}/click`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: "{}",
    }).catch(() => {});
  };

  const width = ad.position.width || 728;
  const height = ad.position.height || 90;
  const presentation = PRESENTATION[position];

  return (
    <aside
      className={`ad-slot w-full flex-col ${className}`}
      data-position={position}
      data-layout={presentation.layout}
      data-testid={`ad-${position}`}
      aria-label="Advertisement"
    >
      <span
        className={`mb-1 block text-center uppercase tracking-[0.16em] text-muted ${
          compactLabel ? "text-[9px]" : "text-[10px]"
        }`}
      >
        विज्ञापन / Advertisement
      </span>
      <a
        href={ad.target_url}
        target="_blank"
        rel="noopener noreferrer sponsored"
        onClick={handleClick}
        className={`mx-auto block w-full overflow-hidden rounded-lg border border-border bg-surface shadow-sm ${presentation.maxWidth}`}
      >
        <Image
          src={ad.image_url}
          alt={ad.title}
          width={width}
          height={height}
          className="h-auto w-full object-contain"
          unoptimized
          onError={() => setImgError(true)}
        />
      </a>
    </aside>
  );
}

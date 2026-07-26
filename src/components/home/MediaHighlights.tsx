"use client";

import { SectionHeader } from "./SectionHeader";
import { ReelsCarousel } from "./ReelsCarousel";
import { GalleryCard } from "@/components/gallery/GalleryCard";
import type { HomeGallery, HomeReel } from "@/lib/home-api";

export function MediaHighlights({ reels, galleries }: { reels: HomeReel[]; galleries: HomeGallery[] }) {
  if (!reels.length && !galleries.length) return null;
  return (
    <section className="space-y-5">
      <SectionHeader titleKey="sections.mediaHighlights" color="#ad1457" />
      {reels.length ? <ReelsCarousel reels={reels} /> : null}
      {galleries.length ? (
        <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
          {galleries.slice(0, 3).map((gallery) => (
            <GalleryCard key={gallery.id} slug={gallery.slug} title={gallery.title} coverImage={gallery.cover_image} imageCount={gallery.images?.length ?? 0} />
          ))}
        </div>
      ) : null}
    </section>
  );
}

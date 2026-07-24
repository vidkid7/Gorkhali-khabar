import { Header } from "@/components/layout/Header";
import { Footer } from "@/components/layout/Footer";
import { GalleryCard } from "@/components/gallery/GalleryCard";
import { PublicPageHeader } from "@/components/ui/PublicPageHeader";
import { EditorialEmptyState } from "@/components/ui/EditorialEmptyState";
import { ArrowLeft, ArrowRight } from "lucide-react";
import { prisma } from "@/lib/prisma";
import type { Metadata } from "next";

export const dynamic = "force-dynamic";

export const metadata: Metadata = {
  title: "फोटो ग्यालेरी",
};

interface PageProps {
  searchParams: Promise<{ [key: string]: string | string[] | undefined }>;
}

export default async function GalleriesPage({ searchParams }: PageProps) {
  const params = await searchParams;
  const page = Math.max(1, parseInt(typeof params.page === "string" ? params.page : "1"));
  const pageSize = 20;

  const [galleries, total] = await Promise.all([
    prisma.gallery.findMany({
      where: { is_active: true },
      skip: (page - 1) * pageSize,
      take: pageSize,
      orderBy: { created_at: "desc" },
      include: {
        _count: { select: { images: true } },
      },
    }),
    prisma.gallery.count({ where: { is_active: true } }),
  ]);

  const totalPages = Math.ceil(total / pageSize);

  return (
    <>
      <Header />
      <main className="public-page-shell mx-auto max-w-6xl px-4 py-8">
        <PublicPageHeader title="फोटो ग्यालेरी" eyebrow="Photo Gallery" description="तस्बिरमार्फत पछिल्ला घटनाक्रम र विशेष क्षणहरू हेर्नुहोस्।" breadcrumbs={[{ label: "गृहपृष्ठ", href: "/" }, { label: "फोटो ग्यालेरी" }]} />

        {galleries.length > 0 ? (
          <div className="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
            {galleries.map((gallery) => (
              <GalleryCard
                key={gallery.id}
                slug={gallery.slug}
                title={gallery.title}
                coverImage={gallery.cover_image}
                imageCount={gallery._count.images}
              />
            ))}
          </div>
        ) : (
          <div className="mt-8"><EditorialEmptyState title="कुनै ग्यालेरी भेटिएन" description="नयाँ फोटो ग्यालेरीहरू यहाँ देखिनेछन्।" action={{ label: "गृहपृष्ठमा फर्कनुहोस्", href: "/" }} /></div>
        )}

        {totalPages > 1 && (
          <div className="flex items-center justify-center gap-2">
            {page > 1 && (
              <a href={`/galleries?page=${page - 1}`} className="btn-secondary text-sm">
                <ArrowLeft className="inline h-3.5 w-3.5" /> अघिल्लो
              </a>
            )}
            <span className="text-sm px-3" style={{ color: "var(--muted)" }}>
              पृष्ठ {page} / {totalPages}
            </span>
            {page < totalPages && (
              <a href={`/galleries?page=${page + 1}`} className="btn-secondary text-sm">
                अर्को <ArrowRight className="inline h-3.5 w-3.5" />
              </a>
            )}
          </div>
        )}
      </main>
      <Footer />
    </>
  );
}

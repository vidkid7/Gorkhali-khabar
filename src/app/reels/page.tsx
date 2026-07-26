import { Header } from "@/components/layout/Header";
import { Footer } from "@/components/layout/Footer";
import { ReelCard } from "@/components/reels/ReelCard";
import { PublicPageHeader } from "@/components/ui/PublicPageHeader";
import { EditorialEmptyState } from "@/components/ui/EditorialEmptyState";
import { ArrowLeft, ArrowRight } from "lucide-react";
import { laravelApi } from "@/lib/api/laravel";
import type { Metadata } from "next";

export const dynamic = "force-dynamic";

export const metadata: Metadata = {
  title: "OK Reels - भिडियो",
};

interface PageProps {
  searchParams: Promise<{ [key: string]: string | string[] | undefined }>;
}

export default async function ReelsPage({ searchParams }: PageProps) {
  const params = await searchParams;
  const page = Math.max(1, parseInt(typeof params.page === "string" ? params.page : "1"));
  const pageSize = 20;

  const response = await laravelApi.get<{
    data: Array<{
      id: string;
      title: string;
      slug: string;
      thumbnail?: string | null;
      view_count?: number;
    }>;
    total: number;
    totalPages: number;
  }>(`/api/v1/reels?page=${page}&pageSize=${pageSize}`);
  const reels = response.data;
  const total = response.total;
  const totalPages = response.totalPages;

  return (
    <>
      <Header />
      <main className="public-page-shell mx-auto max-w-6xl px-4 py-8">
        <PublicPageHeader title="OK Reels" eyebrow="Short Video" description="छोटो भिडियो, रिपोर्ट र विशेष सामग्रीको संग्रह।" breadcrumbs={[{ label: "गृहपृष्ठ", href: "/" }, { label: "OK Reels" }]} />

        {reels.length > 0 ? (
          <div className="mt-8 grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5">
            {reels.map((reel) => (
              <ReelCard
                key={reel.id}
                slug={reel.slug}
                title={reel.title}
                thumbnail={reel.thumbnail}
                viewCount={reel.view_count ?? 0}
              />
            ))}
          </div>
        ) : (
          <div className="mt-8"><EditorialEmptyState title="कुनै रिल भेटिएन" description="नयाँ छोटो भिडियोहरू यहाँ देखिनेछन्।" action={{ label: "गृहपृष्ठमा फर्कनुहोस्", href: "/" }} /></div>
        )}

        {totalPages > 1 && (
          <div className="flex items-center justify-center gap-2">
            {page > 1 && (
              <a href={`/reels?page=${page - 1}`} className="btn-secondary text-sm">
                <ArrowLeft className="inline h-3.5 w-3.5" /> अघिल्लो
              </a>
            )}
            <span className="text-sm px-3" style={{ color: "var(--muted)" }}>
              पृष्ठ {page} / {totalPages}
            </span>
            {page < totalPages && (
              <a href={`/reels?page=${page + 1}`} className="btn-secondary text-sm">
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

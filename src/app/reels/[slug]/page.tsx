import { notFound } from "next/navigation";
import type { Metadata } from "next";
import { laravelApi } from "@/lib/api/laravel";
import { Header } from "@/components/layout/Header";
import { Footer } from "@/components/layout/Footer";
import { decodePublicSlugParam } from "@/lib/public-articles";
import { PublicPageHeader } from "@/components/ui/PublicPageHeader";

export const dynamic = "force-dynamic";

type Props = {
  params: Promise<{ slug: string }>;
};

async function getReel(slug: string) {
  try {
    return await laravelApi.get<{
      id: string;
      title: string;
      title_en?: string | null;
      slug: string;
      video_url: string;
      thumbnail?: string | null;
      description?: string | null;
      view_count?: number;
      created_at?: string | null;
    }>(`/api/v1/reels/slug/${encodeURIComponent(slug)}`);
  } catch {
    return null;
  }
}

function getYouTubeEmbedUrl(url: string) {
  try {
    const parsed = new URL(url);
    const host = parsed.hostname.replace(/^www\./, "");
    const id =
      host === "youtu.be"
        ? parsed.pathname.slice(1)
        : host === "youtube.com"
          ? parsed.searchParams.get("v")
          : null;
    return id ? `https://www.youtube.com/embed/${id}` : null;
  } catch {
    return null;
  }
}

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const { slug } = await params;
  const reel = await getReel(decodePublicSlugParam(slug));
  if (!reel) return { title: "Not Found" };

  return {
    title: reel.title,
    description: reel.description || undefined,
    openGraph: {
      title: reel.title,
      description: reel.description || undefined,
      images: reel.thumbnail ? [reel.thumbnail] : undefined,
    },
  };
}

export default async function ReelPage({ params }: Props) {
  const { slug } = await params;
  const reel = await getReel(decodePublicSlugParam(slug));

  if (!reel) notFound();
  const youtubeEmbedUrl = getYouTubeEmbedUrl(reel.video_url);

  return (
    <>
      <Header />
      <main className="public-page-shell mx-auto max-w-5xl px-3 py-6 sm:px-4 sm:py-10">
        <PublicPageHeader title={reel.title} eyebrow={reel.title_en || "OK Reels"} breadcrumbs={[{ label: "OK Reels", href: "/reels" }, { label: reel.title }]} />
        <div className="mt-5 flex flex-wrap items-center gap-2 text-xs font-semibold uppercase tracking-wider text-muted">
          <span className="rounded-full bg-accent/10 px-3 py-1 text-accent">OK Reels</span>
          <span>•</span>
          <span>{(reel.view_count ?? 0).toLocaleString()} views</span>
        </div>
        <div className="media-stage mt-7 aspect-video overflow-hidden rounded-2xl border border-border shadow-lg">
          {youtubeEmbedUrl ? (
            <iframe
              src={youtubeEmbedUrl}
              title={reel.title}
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
              allowFullScreen
              className="h-full w-full"
            />
          ) : (
            <video
              src={reel.video_url}
              controls
              playsInline
              poster={reel.thumbnail || undefined}
              className="w-full h-full object-contain"
            />
          )}
        </div>

        {reel.description && (
          <p className="mt-5 text-sm leading-relaxed text-foreground">
            {reel.description}
          </p>
        )}

        <div className="mt-4 flex items-center gap-4 text-sm" style={{ color: "var(--muted)" }}>
          {reel.created_at && <span suppressHydrationWarning>{new Date(reel.created_at).toLocaleDateString("ne-NP")}</span>}
        </div>
      </main>
      <Footer />
    </>
  );
}

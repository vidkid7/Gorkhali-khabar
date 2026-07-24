import { notFound } from "next/navigation";
import type { Metadata } from "next";
import { Eye } from "lucide-react";
import { prisma } from "@/lib/prisma";
import { Header } from "@/components/layout/Header";
import { Footer } from "@/components/layout/Footer";
import { decodePublicSlugParam } from "@/lib/public-articles";
import { PublicPageHeader } from "@/components/ui/PublicPageHeader";

export const dynamic = "force-dynamic";

type Props = {
  params: Promise<{ slug: string }>;
};

async function getReel(slug: string) {
  return prisma.reel.findUnique({
    where: { slug, is_active: true },
  });
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

  // Fire-and-forget view count increment
  prisma.reel.update({
    where: { id: reel.id },
    data: { view_count: { increment: 1 } },
  }).catch(() => {});

  return (
    <>
      <Header />
      <main className="public-page-shell mx-auto max-w-5xl px-4 py-8">
        <PublicPageHeader title={reel.title} eyebrow={reel.title_en || "OK Reels"} breadcrumbs={[{ label: "OK Reels", href: "/reels" }, { label: reel.title }]} />
        <div className="media-stage mt-8 aspect-video overflow-hidden">
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

        <div className="flex items-center gap-4 text-sm" style={{ color: "var(--muted)" }}>
          <span className="inline-flex items-center gap-1"><Eye className="h-4 w-4" /> {reel.view_count.toLocaleString()} views</span>
          <span suppressHydrationWarning>{reel.created_at.toLocaleDateString("ne-NP")}</span>
        </div>
      </main>
      <Footer />
    </>
  );
}

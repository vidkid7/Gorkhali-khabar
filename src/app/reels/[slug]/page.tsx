import { notFound } from "next/navigation";
import type { Metadata } from "next";
import { prisma } from "@/lib/prisma";
import { Header } from "@/components/layout/Header";
import { Footer } from "@/components/layout/Footer";

export const dynamic = "force-dynamic";

type Props = {
  params: Promise<{ slug: string }>;
};

async function getReel(slug: string) {
  return prisma.reel.findUnique({
    where: { slug, is_active: true },
  });
}

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const { slug } = await params;
  const reel = await getReel(slug);
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
  const reel = await getReel(slug);

  if (!reel) notFound();

  // Fire-and-forget view count increment
  prisma.reel.update({
    where: { id: reel.id },
    data: { view_count: { increment: 1 } },
  }).catch(() => {});

  return (
    <>
      <Header />
      <main className="mx-auto max-w-4xl px-4 py-6 space-y-4">
        <h1 className="text-2xl font-bold" style={{ color: "var(--foreground)" }}>
          {reel.title}
        </h1>

        {reel.title_en && (
          <p className="text-sm" style={{ color: "var(--muted)" }}>
            {reel.title_en}
          </p>
        )}

        <div className="aspect-video rounded-lg overflow-hidden bg-black">
          <video
            src={reel.video_url}
            controls
            autoPlay
            playsInline
            poster={reel.thumbnail || undefined}
            className="w-full h-full object-contain"
          />
        </div>

        {reel.description && (
          <p className="text-sm leading-relaxed" style={{ color: "var(--foreground)" }}>
            {reel.description}
          </p>
        )}

        <div className="flex items-center gap-4 text-sm" style={{ color: "var(--muted)" }}>
          <span>👁 {reel.view_count.toLocaleString()} views</span>
          <span suppressHydrationWarning>{reel.created_at.toLocaleDateString("ne-NP")}</span>
        </div>
      </main>
      <Footer />
    </>
  );
}

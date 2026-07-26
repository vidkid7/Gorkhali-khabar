import { notFound } from "next/navigation";
import { laravelApi } from "@/lib/api/laravel";

export const dynamic = "force-dynamic";

interface ContentPage {
  title: string;
  title_en?: string | null;
  body: string;
  body_en?: string | null;
}

export default async function ContentPageRoute({ params }: { params: Promise<{ slug: string }> }) {
  const { slug } = await params;
  let page: ContentPage;
  try {
    page = await laravelApi.get<ContentPage>(`/api/v1/pages/${encodeURIComponent(slug)}`);
  } catch {
    notFound();
  }

  return (
    <main className="mx-auto w-full max-w-4xl px-4 py-10 sm:py-16">
      <article className="prose prose-lg max-w-none dark:prose-invert">
        <h1>{page!.title}</h1>
        <div dangerouslySetInnerHTML={{ __html: page!.body }} />
      </article>
    </main>
  );
}

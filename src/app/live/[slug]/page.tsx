import { notFound } from "next/navigation";
import { laravelApi } from "@/lib/api/laravel";

export const dynamic = "force-dynamic";

interface LiveBlog {
  title: string;
  summary?: string | null;
  posts: Array<{ id: string; title?: string | null; body: string; published_at?: string | null }>;
}

export default async function LiveBlogPage({ params }: { params: Promise<{ slug: string }> }) {
  const { slug } = await params;
  let blog: LiveBlog;
  try {
    blog = await laravelApi.get<LiveBlog>(`/api/v1/live-blogs/${encodeURIComponent(slug)}`);
  } catch {
    notFound();
  }

  return (
    <main className="mx-auto w-full max-w-4xl px-4 py-10 sm:py-16">
      <h1 className="text-3xl font-black">{blog!.title}</h1>
      {blog!.summary && <p className="mt-3 text-muted">{blog!.summary}</p>}
      <div className="mt-8 space-y-5">
        {blog!.posts.map((post) => (
          <article key={post.id} className="border-l-2 border-accent pl-5">
            {post.title && <h2 className="font-bold">{post.title}</h2>}
            <div className="prose mt-2 max-w-none dark:prose-invert" dangerouslySetInnerHTML={{ __html: post.body }} />
          </article>
        ))}
      </div>
    </main>
  );
}

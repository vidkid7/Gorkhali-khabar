import Link from "next/link";
import { laravelApi } from "@/lib/api/laravel";

export const dynamic = "force-dynamic";

interface LiveBlog {
  slug: string;
  title: string;
  summary?: string | null;
  status: string;
  started_at?: string | null;
}

export default async function LivePage() {
  const blogs = await laravelApi.get<LiveBlog[]>("/api/v1/live-blogs");

  return (
    <main className="mx-auto w-full max-w-5xl px-4 py-10 sm:py-16">
      <header className="mb-8 border-b border-border pb-4">
        <p className="editorial-kicker">गोर्खाली खबर</p>
        <h1 className="mt-2 text-3xl font-black">लाइभ अपडेट</h1>
      </header>
      {blogs.length === 0 ? (
        <p className="text-muted">हाल कुनै लाइभ अपडेट उपलब्ध छैन।</p>
      ) : (
        <div className="grid gap-5 md:grid-cols-2">
          {blogs.map((blog) => (
            <Link key={blog.slug} href={`/live/${blog.slug}`} className="rounded-xl border border-border p-5 transition hover:border-accent">
              <span className="text-xs font-bold uppercase text-accent">{blog.status}</span>
              <h2 className="mt-2 text-xl font-bold">{blog.title}</h2>
              {blog.summary && <p className="mt-2 text-muted">{blog.summary}</p>}
            </Link>
          ))}
        </div>
      )}
    </main>
  );
}

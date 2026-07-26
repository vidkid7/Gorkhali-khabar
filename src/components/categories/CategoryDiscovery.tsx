import Link from "next/link";
import type { HomeArticle } from "@/lib/home-api";

const CORE_TOPICS = [
  { slug: "samachar", label: "समाचार" },
  { slug: "rajniti", label: "राजनीति" },
  { slug: "arthatantra", label: "अर्थतन्त्र" },
  { slug: "khelkud", label: "खेलकुद" },
  { slug: "bichar", label: "विचार" },
];

type CategoryDiscoveryProps = {
  articles: HomeArticle[];
  activeSlug: string;
  accentColor: string;
};

export function CategoryDiscovery({ articles, activeSlug, accentColor }: CategoryDiscoveryProps) {
  return (
    <aside className="rounded-xl border border-border bg-card p-4 shadow-sm">
      <div className="flex items-center gap-2">
        <span className="h-0.5 w-7 rounded-full" style={{ backgroundColor: accentColor }} />
        <p className="text-xs font-bold uppercase tracking-[0.16em] text-muted">विषयहरू</p>
      </div>

      <nav aria-label="Category topics" className="mt-3 flex flex-wrap gap-2" data-testid="category-topic-strip">
        {CORE_TOPICS.map((topic) => (
          <Link
            key={topic.slug}
            href={`/categories/${topic.slug}`}
            aria-current={topic.slug === activeSlug ? "page" : undefined}
            className={topic.slug === activeSlug
              ? "rounded-full px-3 py-1.5 text-sm font-bold text-white"
              : "rounded-full border border-border px-3 py-1.5 text-sm font-semibold text-foreground transition hover:border-primary hover:text-primary"}
            style={topic.slug === activeSlug ? { backgroundColor: accentColor } : undefined}
          >
            {topic.label}
          </Link>
        ))}
      </nav>

      <div className="mt-6 border-t border-border pt-4">
        <h2 className="font-serif text-xl font-bold text-foreground">धेरै पढिएको</h2>
        <ol className="mt-3 divide-y divide-border">
          {articles.slice(0, 3).map((article, index) => (
            <li key={article.id} className="flex gap-3 py-3 first:pt-0">
              <span className="font-serif text-2xl font-black text-muted/60">{index + 1}</span>
              <Link
                href={`/articles/${article.slug}`}
                className="text-sm font-bold leading-6 text-foreground transition hover:text-primary"
                data-testid="most-read-item"
              >
                {article.title}
              </Link>
            </li>
          ))}
        </ol>
      </div>
    </aside>
  );
}

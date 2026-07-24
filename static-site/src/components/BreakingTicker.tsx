import type { Article, HomePayload } from "../api/types";

export function BreakingTicker({
  items,
  fallback,
}: {
  items: HomePayload["breakingNews"];
  fallback: Article[];
}) {
  const links =
    items.length > 0
      ? items.map((item) => ({
          id: item.id,
          title: item.title,
          slug: item.article?.slug,
        }))
      : fallback.map((article) => ({
          id: article.id,
          title: article.title,
          slug: article.slug,
        }));

  if (links.length === 0) return null;
  return (
    <aside className="breaking-ticker" aria-label="ब्रेकिङ समाचार">
      <div className="container breaking-ticker__inner">
        <strong>ब्रेकिङ</strong>
        <div>
          {links.map((item) =>
            item.slug ? (
              <a key={item.id} href={`/articles/${item.slug}`}>
                {item.title}
              </a>
            ) : (
              <span key={item.id}>{item.title}</span>
            ),
          )}
        </div>
      </div>
    </aside>
  );
}

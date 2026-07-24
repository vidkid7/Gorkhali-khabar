import { apiGet, ApiRequestError } from "../api/client";
import type { Article } from "../api/types";
import { ErrorState, LoadingState, NotFoundPage } from "../components/PageState";
import { useApiResource } from "../hooks/useApiResource";

export function ArticlePage({ slug }: { slug: string }) {
  const state = useApiResource(
    () => apiGet<Article>(`/api/v1/articles/slug/${encodeURIComponent(slug)}`),
    [slug],
  );
  if (state.loading) return <LoadingState />;
  if (state.error instanceof ApiRequestError && state.error.status === 404) {
    return <NotFoundPage />;
  }
  if (state.error) return <ErrorState retry={state.retry} />;
  if (!state.data) return <NotFoundPage />;

  const article = state.data;
  const published = article.published_at
    ? new Intl.DateTimeFormat("ne-NP", { dateStyle: "long" }).format(
        new Date(article.published_at),
      )
    : "";

  return (
    <article className="article-detail container">
      <header>
        {article.category && (
          <a className="category-label" href={`/categories/${article.category.slug}`}>
            {article.category.name}
          </a>
        )}
        <h1>{article.title}</h1>
        {article.excerpt && <p className="article-deck">{article.excerpt}</p>}
        <div className="article-meta">
          {article.author?.name && <span>{article.author.name}</span>}
          {published && <time dateTime={article.published_at || undefined}>{published}</time>}
          {article.reading_time && <span>{article.reading_time} मिनेट पढाइ</span>}
        </div>
      </header>
      {article.featured_image && (
        <img className="article-hero" src={article.featured_image} alt={article.title} />
      )}
      <div
        className="article-body"
        dangerouslySetInnerHTML={{ __html: article.content || "" }}
      />
    </article>
  );
}

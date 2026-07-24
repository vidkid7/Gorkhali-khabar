import type { Article } from "../api/types";

function formatDate(value: string | null) {
  if (!value) return "";
  return new Intl.DateTimeFormat("ne-NP", {
    year: "numeric",
    month: "short",
    day: "numeric",
  }).format(new Date(value));
}

export function ArticleCard({
  article,
  featured = false,
}: {
  article: Article;
  featured?: boolean;
}) {
  return (
    <article className={`article-card ${featured ? "is-featured" : ""}`}>
      <a href={`/articles/${article.slug}`} className="article-card__image">
        {article.featured_image ? (
          <img src={article.featured_image} alt="" loading="lazy" />
        ) : (
          <span aria-hidden="true">GK</span>
        )}
      </a>
      <div className="article-card__content">
        {article.category && (
          <a
            className="category-label"
            href={`/categories/${article.category.slug}`}
          >
            {article.category.name}
          </a>
        )}
        <h3>
          <a href={`/articles/${article.slug}`}>{article.title}</a>
        </h3>
        {featured && article.excerpt && <p>{article.excerpt}</p>}
        <div className="article-meta">
          <span>{formatDate(article.published_at)}</span>
          {article.reading_time ? <span>{article.reading_time} मिनेट</span> : null}
        </div>
      </div>
    </article>
  );
}

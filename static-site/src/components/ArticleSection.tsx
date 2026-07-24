import type { Article } from "../api/types";
import { ArticleCard } from "./ArticleCard";

export function ArticleSection({
  title,
  slug,
  articles,
  layout,
}: {
  title: string;
  slug: string;
  articles: Article[];
  layout: "featured" | "grid" | "list";
}) {
  if (articles.length === 0) return null;
  return (
    <section className="content-section container">
      <header className="section-heading">
        <h2>{title}</h2>
        {slug && <a href={`/categories/${slug}`}>सबै हेर्नुहोस् →</a>}
      </header>
      <div className={`article-layout article-layout--${layout}`}>
        {articles.map((article, index) => (
          <ArticleCard
            key={article.id}
            article={article}
            featured={layout === "featured" && index === 0}
          />
        ))}
      </div>
    </section>
  );
}

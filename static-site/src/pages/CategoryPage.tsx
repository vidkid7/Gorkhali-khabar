import { apiGet, ApiRequestError } from "../api/client";
import type { Article, Category, Paginated } from "../api/types";
import { ArticleCard } from "../components/ArticleCard";
import { EmptyState, ErrorState, LoadingState, NotFoundPage } from "../components/PageState";
import { Pagination } from "../components/Pagination";
import { useApiResource } from "../hooks/useApiResource";

interface CategoryPayload extends Category {
  children?: CategoryPayload[];
}

function flattenCategories(categories: CategoryPayload[]): CategoryPayload[] {
  return categories.flatMap((category) => [
    category,
    ...flattenCategories(category.children || []),
  ]);
}

export function CategoryPage({ slug }: { slug: string }) {
  const page = Math.max(1, Number(new URLSearchParams(window.location.search).get("page")) || 1);
  const state = useApiResource(async () => {
    const categories = await apiGet<CategoryPayload[]>("/api/v1/categories");
    const category = flattenCategories(categories).find((item) => item.slug === slug);
    if (!category) throw new ApiRequestError(404, "वर्ग फेला परेन");
    const articles = await apiGet<Paginated<Article>>(
      `/api/v1/articles?category=${encodeURIComponent(category.id)}&page=${page}&pageSize=12`,
    );
    return { category, articles };
  }, [slug, page]);

  if (state.loading) return <LoadingState />;
  if (state.error instanceof ApiRequestError && state.error.status === 404) return <NotFoundPage />;
  if (state.error) return <ErrorState retry={state.retry} />;
  if (!state.data) return <NotFoundPage />;
  return (
    <main className="listing-page container">
      <header className="listing-header"><p className="eyebrow">विषय</p><h1>{state.data.category.name}</h1></header>
      {state.data.articles.data.length === 0 ? (
        <EmptyState message="यस वर्गमा समाचार उपलब्ध छैन।" />
      ) : (
        <div className="article-layout article-layout--grid">
          {state.data.articles.data.map((article) => <ArticleCard key={article.id} article={article} />)}
        </div>
      )}
      <Pagination
        page={state.data.articles.page || page}
        totalPages={state.data.articles.totalPages || 1}
        pathname={`/categories/${slug}`}
      />
    </main>
  );
}

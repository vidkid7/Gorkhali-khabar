import { apiGet } from "../api/client";
import type { Article, Paginated } from "../api/types";
import { ArticleCard } from "../components/ArticleCard";
import { EmptyState, ErrorState, LoadingState } from "../components/PageState";
import { Pagination } from "../components/Pagination";
import { useApiResource } from "../hooks/useApiResource";

export function SearchPage() {
  const params = new URLSearchParams(window.location.search);
  const query = (params.get("q") || "").trim();
  const page = Math.max(1, Number(params.get("page")) || 1);
  const state = useApiResource(
    () => apiGet<Paginated<Article>>(
      `/api/v1/search?q=${encodeURIComponent(query)}&page=${page}&pageSize=12`,
    ),
    [query, page],
  );

  if (!query) {
    return (
      <main className="listing-page container">
        <header className="listing-header"><p className="eyebrow">खोज</p><h1>समाचार खोज्नुहोस्</h1></header>
        <form className="search-form" action="/search">
          <label htmlFor="search-q">खोज शब्द</label>
          <div><input id="search-q" name="q" minLength={2} required /><button type="submit">खोज्नुहोस्</button></div>
        </form>
      </main>
    );
  }
  if (state.loading) return <LoadingState />;
  if (state.error) return <ErrorState retry={state.retry} />;
  if (!state.data) return <EmptyState />;
  return (
    <main className="listing-page container">
      <header className="listing-header"><p className="eyebrow">“{query}”</p><h1>खोज परिणाम</h1></header>
      {state.data.data.length === 0 ? <EmptyState message="मिल्दो समाचार भेटिएन।" /> : (
        <div className="article-layout article-layout--grid">
          {state.data.data.map((article) => <ArticleCard key={article.id} article={article} />)}
        </div>
      )}
      <Pagination page={state.data.page || page} totalPages={state.data.totalPages || 1} pathname="/search" params={{ q: query }} />
    </main>
  );
}

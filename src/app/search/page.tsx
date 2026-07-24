"use client";

import { Suspense, useState, useEffect, useCallback } from "react";
import { useSearchParams, useRouter } from "next/navigation";
import { Header } from "@/components/layout/Header";
import { Footer } from "@/components/layout/Footer";
import { ArticleCard } from "@/components/articles/ArticleCard";
import { ArticleCardSkeleton } from "@/components/ui/SkeletonLoader";
import { PublicPageHeader } from "@/components/ui/PublicPageHeader";
import { EditorialEmptyState } from "@/components/ui/EditorialEmptyState";
import { useLanguage } from "@/contexts/LanguageContext";
import { ArrowLeft, ArrowRight } from "lucide-react";
import Link from "next/link";

interface SearchArticle {
  id: string;
  title: string;
  title_en?: string | null;
  slug: string;
  excerpt?: string | null;
  excerpt_en?: string | null;
  featured_image?: string | null;
  published_at?: string | null;
  reading_time?: number | null;
  view_count: number;
  category: {
    id: string;
    name: string;
    name_en?: string | null;
    slug: string;
    color: string;
  };
  author: { id: string; name?: string | null; image?: string | null };
}

interface SearchResponse {
  success: boolean;
  data?: {
    data: SearchArticle[];
    total: number;
    page: number;
    pageSize: number;
    totalPages: number;
  };
  error?: string;
}

export default function SearchPage() {
  return (
    <Suspense
      fallback={
        <>
          <Header />
          <main className="mx-auto max-w-7xl px-4 py-6 pb-safe">
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
              {Array.from({ length: 6 }).map((_, i) => (
                <ArticleCardSkeleton key={i} />
              ))}
            </div>
          </main>
          <Footer />
        </>
      }
    >
      <SearchContent />
    </Suspense>
  );
}

function SearchContent() {
  const searchParams = useSearchParams();
  const router = useRouter();
  const { t } = useLanguage();

  const query = searchParams.get("q") || "";
  const page = Math.max(1, parseInt(searchParams.get("page") || "1"));

  const [inputValue, setInputValue] = useState(query);
  const [results, setResults] = useState<SearchArticle[]>([]);
  const [total, setTotal] = useState(0);
  const [totalPages, setTotalPages] = useState(0);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const fetchResults = useCallback(async (q: string, p: number) => {
    if (!q || q.length < 2) {
      setResults([]);
      setTotal(0);
      setTotalPages(0);
      return;
    }

    setLoading(true);
    setError(null);

    try {
      const res = await fetch(
        `/api/v1/search?q=${encodeURIComponent(q)}&page=${p}&pageSize=12`
      );
      const data: SearchResponse = await res.json();

      if (data.success && data.data) {
        setResults(data.data.data);
        setTotal(data.data.total);
        setTotalPages(data.data.totalPages);
      } else {
        setError(data.error || t("common.error"));
        setResults([]);
      }
    } catch {
      setError(t("common.error"));
      setResults([]);
    } finally {
      setLoading(false);
    }
  }, [t]);

  useEffect(() => {
    setInputValue(query);
    fetchResults(query, page);
  }, [query, page, fetchResults]);

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (inputValue.trim().length >= 2) {
      router.push(`/search?q=${encodeURIComponent(inputValue.trim())}`);
    }
  };

  return (
    <>
      <Header />
      <main className="public-page-shell mx-auto max-w-7xl px-4 py-8 pb-safe">
        <PublicPageHeader title={t("common.search").replace("...", "")} description={query ? `"${query}"` : undefined} breadcrumbs={[{ label: t("common.home"), href: "/" }, { label: t("common.search") }]} actions={
          <form onSubmit={handleSubmit} className="flex w-full gap-2 sm:min-w-[24rem]">
            <input
              type="search"
              value={inputValue}
              onChange={(e) => setInputValue(e.target.value)}
              placeholder={t("common.search")}
              className="min-w-0 flex-1 rounded-[4px] border border-border bg-surface px-4 py-3 text-foreground focus:outline-none focus:ring-2 focus:ring-primary"
              autoFocus
            />
            <button type="submit" className="btn-primary shrink-0 px-5">
              {t("common.search").replace("...", "")}
            </button>
          </form>
        } />

        {query && !loading && (
          <p className="mt-6 text-sm text-muted" aria-live="polite">
            {total > 0
              ? `"${query}" — ${total} results found`
              : error ? "" : t("common.noResults")}
          </p>
        )}

        {error && <div className="mt-8"><EditorialEmptyState title={t("common.error")} description={error} action={{ label: t("common.backToHome"), href: "/" }} /></div>}

        {/* Loading skeletons */}
        {loading && (
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            {Array.from({ length: 6 }).map((_, i) => (
              <ArticleCardSkeleton key={i} />
            ))}
          </div>
        )}

        {!loading && results.length > 0 && (
          <div className="mt-8 divide-y divide-border">
            {results.map((article) => (
              <div key={article.id} className="py-3 first:pt-0"><ArticleCard {...article} category={{ ...article.category, color: article.category.color || "#07579B" }} variant="horizontal" /></div>
            ))}
          </div>
        )}

        {!loading && !error && !query && <div className="mt-8"><EditorialEmptyState title={t("common.searchNews")} description={t("common.searchKeywords")} /></div>}
        {!loading && !error && query && results.length === 0 && <div className="mt-8"><EditorialEmptyState title={t("common.noResults")} description={t("common.searchKeywords")} action={{ label: t("common.backToHome"), href: "/" }} /></div>}

        {/* Pagination */}
        {!loading && totalPages > 1 && (
          <div className="flex justify-center items-center gap-2 mt-10">
            {page > 1 && (
              <Link
                href={`/search?q=${encodeURIComponent(query)}&page=${page - 1}`}
                className="btn-secondary text-sm"
              >
                <ArrowLeft className="inline h-3.5 w-3.5" /> {t("common.previous")}
              </Link>
            )}
            <span className="text-sm text-muted px-4">
              {page} / {totalPages}
            </span>
            {page < totalPages && (
              <Link
                href={`/search?q=${encodeURIComponent(query)}&page=${page + 1}`}
                className="btn-secondary text-sm"
              >
                {t("common.next")} <ArrowRight className="inline h-3.5 w-3.5" />
              </Link>
            )}
          </div>
        )}
      </main>
      <Footer />
    </>
  );
}

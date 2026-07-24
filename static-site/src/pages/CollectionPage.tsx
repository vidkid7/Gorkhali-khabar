import { apiGet } from "../api/client";
import { EmptyState, ErrorState, LoadingState } from "../components/PageState";
import { useApiResource } from "../hooks/useApiResource";

interface MediaItem {
  id?: string;
  slug?: string;
  title?: string;
  caption?: string;
  description?: string;
  thumbnail?: string;
  thumbnail_url?: string;
  image?: string;
  cover_image?: string;
}

export function CollectionPage({ kind }: { kind: "reels" | "galleries" }) {
  const state = useApiResource(
    () => apiGet<MediaItem[] | { data: MediaItem[] }>(`/api/v1/${kind}`),
    [kind],
  );
  if (state.loading) return <LoadingState />;
  if (state.error) return <ErrorState retry={state.retry} />;
  const items = Array.isArray(state.data) ? state.data : state.data?.data || [];
  const title = kind === "reels" ? "रिल्स" : "फोटो ग्यालरी";
  return (
    <main className="listing-page container">
      <header className="listing-header"><p className="eyebrow">मल्टिमिडिया</p><h1>{title}</h1></header>
      {items.length === 0 ? <EmptyState message="मल्टिमिडिया सामग्री उपलब्ध छैन।" /> : (
        <div className="collection-grid">
          {items.map((item, index) => {
            const image = item.thumbnail_url || item.thumbnail || item.cover_image || item.image;
            const label = item.title || item.caption || `${title} ${index + 1}`;
            return (
              <article className="collection-card" key={item.id || `${kind}-${index}`}>
                <div className="collection-card__media">
                  {image ? <img src={image} alt="" loading="lazy" /> : <span>GK</span>}
                </div>
                <h2>{label}</h2>
                {item.description && <p>{item.description}</p>}
              </article>
            );
          })}
        </div>
      )}
    </main>
  );
}

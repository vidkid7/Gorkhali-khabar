import { apiGet } from "../api/client";
import type { HomePayload } from "../api/types";
import { ArticleSection } from "../components/ArticleSection";
import { BreakingTicker } from "../components/BreakingTicker";
import { EmptyState, ErrorState, LoadingState } from "../components/PageState";
import { useApiResource } from "../hooks/useApiResource";

const sections = [
  ["ताजा समाचार", "samachar", "featured"],
  ["फिचर", "feature", "featured"],
  ["कभर स्टोरी", "cover-story", "grid"],
  ["राजनीति", "rajniti", "featured"],
  ["अर्थतन्त्र", "arthatantra", "grid"],
  ["प्रविधि", "prabidhi", "featured"],
  ["अन्तर्वार्ता", "antarvaarta", "list"],
  ["खेलकुद", "khelkud", "featured"],
] as const;

const provinceLabels: Record<string, string> = {
  koshi: "कोशी प्रदेश",
  madhesh: "मधेश प्रदेश",
  bagmati: "बागमती प्रदेश",
  gandaki: "गण्डकी प्रदेश",
  lumbini: "लुम्बिनी प्रदेश",
  karnali: "कर्णाली प्रदेश",
  sudurpaschim: "सुदूरपश्चिम प्रदेश",
};

export function HomePage() {
  const state = useApiResource(
    () => apiGet<HomePayload>("/api/v1/home"),
    [],
  );

  if (state.loading) return <LoadingState />;
  if (state.error) return <ErrorState retry={state.retry} />;
  if (!state.data) return <EmptyState />;
  const hasPublishedContent =
    state.data.featured.length > 0 ||
    state.data.trending.length > 0 ||
    state.data.olderArticles.length > 0 ||
    Object.values(state.data.categoryGroups).some(
      (articles) => articles.length > 0,
    );
  if (!hasPublishedContent) {
    return (
      <main>
        <div className="page-state">
          <p className="eyebrow">Gorkhali Khabar</p>
          <h1>समाचार कक्ष तयार छ</h1>
          <p>पहिलो समाचार प्रकाशनको तयारी हुँदैछ।</p>
          <a className="button-link" href="/gorkhali-admin">
            सम्पादकीय प्रवेश
          </a>
        </div>
      </main>
    );
  }

  return (
    <main>
      <BreakingTicker
        items={state.data.breakingNews}
        fallback={state.data.trending}
      />
      <ArticleSection
        title="प्रमुख समाचार"
        slug=""
        articles={state.data.featured}
        layout="featured"
      />
      {sections.map(([title, slug, layout]) => (
        <ArticleSection
          key={slug}
          title={title}
          slug={slug}
          articles={state.data.categoryGroups[slug] || []}
          layout={layout}
        />
      ))}
      <ArticleSection title="ट्रेन्डिङ" slug="" articles={state.data.trending} layout="list" />
      <ArticleSection title="धेरै प्रतिक्रिया" slug="" articles={state.data.mostCommented} layout="list" />
      <ArticleSection title="सम्पादकको रोजाइ" slug="" articles={state.data.editorPicks} layout="grid" />
      <ArticleSection title="लोकप्रिय पुराना समाचार" slug="" articles={state.data.olderArticles} layout="grid" />
      {Object.entries(state.data.provinceGroups).map(([province, articles]) => (
        <ArticleSection
          key={province}
          title={provinceLabels[province] || province}
          slug={`${province}-pradesh`}
          articles={articles}
          layout="list"
        />
      ))}
      {state.data.reels.length > 0 && (
        <section className="content-section container">
          <header className="section-heading"><h2>रिल्स</h2><a href="/reels">सबै हेर्नुहोस् →</a></header>
          <div className="media-grid">
            {state.data.reels.map((reel) => {
              const id = String(reel.id || "");
              return <a key={id} href={`/reels#${id}`} className="media-card">{String(reel.title || reel.caption || "गोर्खाली रिल")}</a>;
            })}
          </div>
        </section>
      )}
      {state.data.matches.length > 0 && (
        <section className="content-section container">
          <header className="section-heading"><h2>लाइभ स्कोर</h2><a href="/sports">सबै हेर्नुहोस् →</a></header>
          <div className="score-grid">
            {state.data.matches.map((match) => {
              const id = String(match.id || "");
              const home = String((match.home_team as { name?: string } | undefined)?.name || (match.homeTeam as { name?: string } | undefined)?.name || "Home");
              const away = String((match.away_team as { name?: string } | undefined)?.name || (match.awayTeam as { name?: string } | undefined)?.name || "Away");
              return <div key={id} className="score-card">{home}<strong>—</strong>{away}</div>;
            })}
          </div>
        </section>
      )}
    </main>
  );
}

import { apiGet } from "../api/client";
import { ErrorState, LoadingState, NotFoundPage } from "../components/PageState";
import { UtilityPanels } from "../components/UtilityPanels";
import { useApiResource } from "../hooks/useApiResource";

const utilityTitles: Record<string, string> = {
  finance: "वित्तीय बजार",
  sports: "खेलकुद",
  rashifal: "राशिफल",
  patro: "नेपाली पात्रो",
  "share-market": "शेयर बजार",
};

export function endpointForUtility(slug: string): string[] {
  const map: Record<string, string[]> = {
    finance: ["/api/v1/finance/exchange-rates", "/api/v1/finance/gold-silver"],
    sports: ["/api/v1/sports/tournaments", "/api/v1/sports/matches"],
    rashifal: ["/api/v1/rashifal"],
    patro: ["/api/v1/calendar/panchang", "/api/v1/calendar/holidays"],
    "share-market": ["/api/v1/nepse"],
  };
  return map[slug] || [];
}

export function UtilityPage({ slug }: { slug: string }) {
  const endpoints = endpointForUtility(slug);
  const state = useApiResource(
    async () => Promise.all(endpoints.map(async (endpoint) => ({
      endpoint,
      data: await apiGet<unknown>(endpoint),
    }))),
    [slug],
  );
  if (endpoints.length === 0) return <NotFoundPage />;
  if (state.loading) return <LoadingState />;
  if (state.error) return <ErrorState retry={state.retry} />;
  return (
    <main className="listing-page container">
      <header className="listing-header"><p className="eyebrow">लाइभ अपडेट</p><h1>{utilityTitles[slug]}</h1></header>
      <UtilityPanels payloads={state.data || []} />
    </main>
  );
}

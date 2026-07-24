import { NotFoundPage } from "./components/PageState";
import { SiteFooter } from "./components/SiteFooter";
import { SiteHeader } from "./components/SiteHeader";
import { resolvePublicRoute } from "./router";

function PendingPage() {
  return (
    <main className="page-state">
      <h1>गोर्खाली खबर</h1>
      <p>ताजा समाचार लोड हुँदैछ…</p>
    </main>
  );
}

export function App() {
  const route = resolvePublicRoute(window.location.pathname);
  return (
    <>
      <SiteHeader />
      <div className="site-main">
        {route.name === "not-found" ? <NotFoundPage /> : <PendingPage />}
      </div>
      <SiteFooter />
    </>
  );
}

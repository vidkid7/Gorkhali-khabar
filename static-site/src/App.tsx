import { NotFoundPage } from "./components/PageState";
import { SiteFooter } from "./components/SiteFooter";
import { SiteHeader } from "./components/SiteHeader";
import { HomePage } from "./pages/HomePage";
import { ArticlePage } from "./pages/ArticlePage";
import { CategoryPage } from "./pages/CategoryPage";
import { SearchPage } from "./pages/SearchPage";
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
  let page;
  switch (route.name) {
    case "home":
      page = <HomePage />;
      break;
    case "article":
      page = <ArticlePage slug={route.slug} />;
      break;
    case "category":
      page = <CategoryPage slug={route.slug} />;
      break;
    case "search":
      page = <SearchPage />;
      break;
    case "not-found":
      page = <NotFoundPage />;
      break;
    default:
      page = <PendingPage />;
  }
  return (
    <>
      <SiteHeader />
      <div className="site-main">
        {page}
      </div>
      <SiteFooter />
    </>
  );
}

export type PublicRoute =
  | { name: "home" }
  | { name: "article"; slug: string }
  | { name: "category"; slug: string }
  | { name: "search" }
  | { name: "reels" }
  | { name: "galleries" }
  | { name: "utility"; slug: string }
  | { name: "static"; slug: string }
  | { name: "not-found" };

const utilityRoutes = new Set([
  "finance",
  "sports",
  "rashifal",
  "patro",
  "share-market",
]);
const staticRoutes = new Set([
  "about",
  "privacy-policy",
  "terms-of-service",
  "cookie-policy",
]);

export function resolvePublicRoute(pathname: string): PublicRoute {
  const parts = pathname
    .replace(/^\/+|\/+$/g, "")
    .split("/")
    .filter(Boolean);

  if (parts.length === 0) return { name: "home" };
  if (parts[0] === "articles" && parts.length === 2) {
    return { name: "article", slug: decodeURIComponent(parts[1]) };
  }
  if (parts[0] === "categories" && parts.length === 2) {
    return { name: "category", slug: decodeURIComponent(parts[1]) };
  }
  if (parts[0] === "search" && parts.length === 1) return { name: "search" };
  if (parts[0] === "reels" && parts.length === 1) return { name: "reels" };
  if (parts[0] === "galleries" && parts.length === 1) {
    return { name: "galleries" };
  }
  if (utilityRoutes.has(parts[0]) && parts.length === 1) {
    return { name: "utility", slug: parts[0] };
  }
  if (staticRoutes.has(parts[0]) && parts.length === 1) {
    return { name: "static", slug: parts[0] };
  }
  return { name: "not-found" };
}

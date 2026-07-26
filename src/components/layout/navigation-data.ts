import { laravelApi } from "@/lib/api/laravel";

export type PublicNavItem = {
  key: string;
  href: string;
  group: "primary" | "secondary" | "service";
  label?: string;
  label_en?: string | null;
};

export const PUBLIC_NAV_ITEMS: PublicNavItem[] = [
  { key: "home", href: "/", group: "primary" },
  { key: "samachar", href: "/categories/samachar", group: "primary" },
  { key: "rajniti", href: "/categories/rajniti", group: "primary" },
  { key: "business", href: "/categories/arthatantra", group: "primary" },
  { key: "khelkud", href: "/categories/khelkud", group: "primary" },
  { key: "bichar", href: "/categories/bichar", group: "primary" },
  { key: "prabidhi", href: "/categories/prabidhi", group: "secondary" },
  { key: "samaj", href: "/categories/samaj", group: "secondary" },
  { key: "manoranjan", href: "/categories/manoranjan", group: "secondary" },
  { key: "world", href: "/categories/world", group: "secondary" },
  { key: "swasthya", href: "/categories/swasthya", group: "secondary" },
  { key: "shiksha", href: "/categories/shiksha", group: "secondary" },
  { key: "antarrashtriya", href: "/categories/antarrashtriya", group: "secondary" },
  { key: "feature", href: "/categories/feature", group: "secondary" },
  { key: "video", href: "/categories/video", group: "secondary" },
  { key: "patro", href: "/patro", group: "service" },
  { key: "shareMarket", href: "/share-market", group: "service" },
  { key: "horoscope", href: "/rashifal", group: "service" },
  { key: "forex", href: "/patro/forex", group: "service" },
  { key: "photoGallery", href: "/galleries", group: "service" },
];

export const MOBILE_NAV_ITEMS = [
  { key: "home", href: "/" },
  { key: "latest", href: "/#latest-news" },
  { key: "search", href: null },
  { key: "patro", href: "/patro" },
  { key: "profile", href: "/profile" },
] as const;

interface NavigationApi {
  get<T>(path: string): Promise<T>;
}

interface EditorialMenu {
  id: string;
  location: string;
  label: string;
  label_en?: string | null;
  href: string;
  sort_order: number;
}

const FIXED_SERVICE_ITEMS: PublicNavItem[] = [
  { key: "patro", href: "/patro", group: "service" },
  { key: "horoscope", href: "/rashifal", group: "service" },
  { key: "shareMarket", href: "/share-market", group: "service" },
  { key: "forex", href: "/patro/forex", group: "service" },
  { key: "photoGallery", href: "/galleries", group: "service" },
];

const PRIMARY_MENU_HREFS = new Set([
  "/",
  "/categories/samachar",
  "/categories/rajniti",
  "/categories/arthatantra",
  "/categories/khelkud",
  "/categories/bichar",
]);

export function mergePublicNavItems(
  managed: PublicNavItem[],
  location = "header",
): PublicNavItem[] {
  if (location !== "header") return managed;

  const managedUrls = new Set(managed.map((item) => item.href));
  return [
    ...managed,
    ...FIXED_SERVICE_ITEMS.filter((item) => !managedUrls.has(item.href)),
  ];
}

export async function getPublicNavItems(
  api: NavigationApi = laravelApi,
  location = "header",
): Promise<PublicNavItem[]> {
  const menus = await api.get<EditorialMenu[]>(`/api/v1/menus?location=${encodeURIComponent(location)}`);

  const managed: PublicNavItem[] = menus
    .filter((menu) => menu.location === location)
    .sort((a, b) => a.sort_order - b.sort_order)
    .map((menu) => ({
      key: menu.id,
      href: menu.href,
      group: PRIMARY_MENU_HREFS.has(menu.href) ? "primary" : "secondary",
      label: menu.label,
      label_en: menu.label_en,
    }));

  return mergePublicNavItems(managed, location);
}

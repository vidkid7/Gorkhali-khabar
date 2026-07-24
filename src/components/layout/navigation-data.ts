export type PublicNavItem = {
  key: string;
  href: string;
  group: "primary" | "secondary" | "service";
};

export const PUBLIC_NAV_ITEMS: PublicNavItem[] = [
  { key: "home", href: "/", group: "primary" },
  { key: "samachar", href: "/categories/samachar", group: "primary" },
  { key: "rajniti", href: "/categories/rajniti", group: "primary" },
  { key: "business", href: "/categories/arthatantra", group: "primary" },
  { key: "khelkud", href: "/categories/khelkud", group: "primary" },
  { key: "bichar", href: "/categories/bichar", group: "primary" },
  { key: "prabidhi", href: "/categories/prabidhi", group: "secondary" },
  { key: "antarrashtriya", href: "/categories/antarrashtriya", group: "secondary" },
  { key: "feature", href: "/categories/feature", group: "secondary" },
  { key: "video", href: "/categories/video", group: "secondary" },
  { key: "photoGallery", href: "/galleries", group: "secondary" },
  { key: "patro", href: "/patro", group: "service" },
  { key: "shareMarket", href: "/share-market", group: "service" },
  { key: "horoscope", href: "/rashifal", group: "service" },
];

export const MOBILE_NAV_ITEMS = [
  { key: "home", href: "/" },
  { key: "latest", href: "/#latest-news" },
  { key: "search", href: null },
  { key: "patro", href: "/patro" },
  { key: "profile", href: "/profile" },
] as const;
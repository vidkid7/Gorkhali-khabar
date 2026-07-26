import { laravelApi } from "@/lib/api/laravel";

export interface HomeArticle {
  id: string;
  slug: string;
  title: string;
  title_en?: string | null;
  excerpt?: string | null;
  excerpt_en?: string | null;
  featured_image?: string | null;
  reading_time?: number | null;
  published_at?: string | null;
  view_count?: number;
  comment_count?: number;
  category: { name: string; name_en?: string | null; slug: string; color: string };
  author: { name?: string | null };
}

export interface HomeReel {
  id: string;
  title: string;
  title_en?: string | null;
  slug: string;
  thumbnail?: string | null;
  view_count?: number;
}

export interface HomeGallery {
  id: string;
  title: string;
  title_en?: string | null;
  slug: string;
  cover_image?: string | null;
  images?: Array<{ id: string }>;
}

export interface HomePayload {
  breakingNews: Array<{ id: string; title: string; title_en?: string | null; article?: { slug: string } | null }>;
  featured: HomeArticle[];
  categoryGroups: Record<string, HomeArticle[]>;
  trending: HomeArticle[];
  mostCommented: HomeArticle[];
  reels: HomeReel[];
  matches: unknown[];
  olderArticles: HomeArticle[];
  editorPicks: HomeArticle[];
  provinceGroups: Record<string, HomeArticle[]>;
  latestUpdates: HomeArticle[];
  opinion: HomeArticle[];
  mediaHighlights: { reels: HomeReel[]; galleries: HomeGallery[] };
}

export interface HomeApi {
  get<T>(path: string): Promise<T>;
}

export interface HomepageSection {
  id: string;
  section_key: string;
  title: string;
  title_en?: string | null;
  category_slug?: string | null;
  layout: "featured" | "grid" | "list";
  sort_order: number;
  is_active: boolean;
}

export function getHomePayload(api: HomeApi = laravelApi): Promise<HomePayload> {
  return api.get<HomePayload>("/api/v1/home");
}

export function getHomepageSections(api: HomeApi = laravelApi): Promise<HomepageSection[]> {
  return api.get<HomepageSection[]>("/api/v1/homepage-sections");
}

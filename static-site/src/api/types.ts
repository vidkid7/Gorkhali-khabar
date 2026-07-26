export type LocalizedText = string | null;

export interface Category {
  id: string;
  slug: string;
  name: string;
  name_en: LocalizedText;
  color: string | null;
}

export interface Article {
  id: string;
  slug: string;
  title: string;
  title_en: LocalizedText;
  excerpt: LocalizedText;
  excerpt_en: LocalizedText;
  content?: LocalizedText;
  content_en?: LocalizedText;
  featured_image: LocalizedText;
  reading_time: number | null;
  published_at: string | null;
  view_count: number;
  comment_count: number;
  category: Category | null;
  author?: { id?: string; name: string } | null;
}

export interface HomeReel {
  id: string;
  title: string;
  title_en: LocalizedText;
  slug: string;
  thumbnail: LocalizedText;
  view_count: number | null;
}

export interface HomeGallery {
  id: string;
  title: string;
  title_en: LocalizedText;
  slug: string;
  cover_image: LocalizedText;
  images: Array<{ id: string }>;
}

export interface HomepageSection {
  id: string;
  section_key: string;
  title: string;
  title_en: LocalizedText;
  category_slug: string | null;
  layout: "featured" | "grid" | "list";
  sort_order: number;
  is_active: boolean;
}

export interface HomePayload {
  breakingNews: Array<{
    id: string;
    title: string;
    title_en: LocalizedText;
    article?: { slug: string } | null;
  }>;
  featured: Article[];
  categoryGroups: Record<string, Article[]>;
  trending: Article[];
  mostCommented: Article[];
  reels: HomeReel[];
  matches: Array<Record<string, unknown>>;
  olderArticles: Article[];
  editorPicks: Article[];
  provinceGroups: Record<string, Article[]>;
  latestUpdates: Article[];
  opinion: Article[];
  mediaHighlights: {
    reels: HomeReel[];
    galleries: HomeGallery[];
  };
}

export interface Paginated<T> {
  data: T[];
  page?: number;
  pageSize?: number;
  totalPages?: number;
  total?: number;
}

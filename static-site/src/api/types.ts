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
  reels: Array<Record<string, unknown>>;
  matches: Array<Record<string, unknown>>;
  olderArticles: Article[];
  editorPicks: Article[];
  provinceGroups: Record<string, Article[]>;
}

export interface Paginated<T> {
  data: T[];
  current_page?: number;
  last_page?: number;
  total?: number;
}

import { ComponentProps, Fragment } from "react";
import type { Metadata } from "next";
import { canonicalUrl, defaultOpenGraphImage } from "@/lib/seo";
import { getHomePayload, getHomepageSections, type HomeArticle, type HomepageSection } from "@/lib/home-api";
import { Header } from "@/components/layout/Header";
import { Footer } from "@/components/layout/Footer";
import { BreakingNewsTicker } from "@/components/ui/BreakingNewsTicker";
import { CategorySection } from "@/components/home/CategorySection";
import { HeroDeck } from "@/components/home/HeroDeck";
import { DailyBrief } from "@/components/home/DailyBrief";
import { ProvincialNews } from "@/components/home/ProvincialNews";
import { LatestUpdatesRail } from "@/components/home/LatestUpdatesRail";
import { EditorialFeatureStrip } from "@/components/home/EditorialFeatureStrip";
import { MediaHighlights } from "@/components/home/MediaHighlights";
import { AdSlot } from "@/components/ads/AdSlot";
import { shouldInsertSectionAd } from "@/components/ads/ad-placement";

export const dynamic = "force-dynamic";

export const metadata: Metadata = {
  title: "गोर्खाली खबर - सत्य, सन्तुलित र समयमै",
  description: "नेपालको विश्वसनीय अनलाइन समाचार पोर्टल — ताजा समाचार, राजनीति, खेलकुद, व्यापार, मनोरञ्जन",
  alternates: { canonical: canonicalUrl("/") },
  openGraph: {
    title: "गोर्खाली खबर - सत्य, सन्तुलित र समयमै",
    description: "नेपालको विश्वसनीय अनलाइन समाचार पोर्टल",
    url: canonicalUrl("/"),
    images: [defaultOpenGraphImage()],
  },
};

const categorySections = [
  ["sections.latestNews", "samachar", "#c62828", "featured"],
  ["sections.feature", "feature", "#ad1457", "featured"],
  ["sections.coverStory", "cover-story", "#bf360c", "grid"],
  ["sections.society", "samaj", "#2e7d32", "grid"],
  ["sections.entertainment", "manoranjan", "#6a1b9a", "grid"],
  ["sections.world", "world", "#37474f", "grid"],
  ["sections.health", "swasthya", "#c62828", "grid"],
  ["sections.education", "shiksha", "#ad1457", "grid"],
  ["sections.opinion", "bichar", "#4e342e", "list"],
  ["sections.politics", "rajniti", "#c62828", "featured"],
  ["sections.economy", "arthatantra", "#2e7d32", "featured"],
  ["sections.health", "swasthya", "#00897b", "grid"],
  ["sections.video", "video", "#d50000", "featured"],
] as const;

type NormalizedHomeArticle = HomeArticle & {
  view_count: number;
  comment_count: number;
};

function normalizeArticle(article: HomeArticle): NormalizedHomeArticle {
  return {
    ...article,
    view_count: article.view_count ?? 0,
    comment_count: article.comment_count ?? 0,
    category: article.category ?? { name: "समाचार", slug: "samachar", color: "#07579B" },
    author: article.author ?? {},
  };
}

function section(
  titleKey: string,
  slug: string,
  color: string,
  layout: ComponentProps<typeof CategorySection>["layout"],
  articles: HomeArticle[],
  title?: string,
  titleEn?: string | null,
) {
  return (
    <CategorySection
      sectionKey={titleKey}
      title={title}
      titleEn={titleEn}
      slug={slug}
      color={color}
      layout={layout}
      articles={articles.map(normalizeArticle)}
    />
  );
}

export default async function HomePage() {
  let data;
  let managedSections: HomepageSection[] = [];
  try {
    [data, managedSections] = await Promise.all([
      getHomePayload(),
      getHomepageSections().catch(() => []),
    ]);
  } catch (error) {
    console.error("Laravel homepage request failed:", error);
    data = {
      breakingNews: [],
      featured: [],
      categoryGroups: {},
      trending: [],
      mostCommented: [],
      reels: [],
      matches: [],
      olderArticles: [],
      editorPicks: [],
      provinceGroups: {},
      latestUpdates: [],
      opinion: [],
      mediaHighlights: { reels: [], galleries: [] },
    };
  }

  const provinces: ComponentProps<typeof ProvincialNews>["articlesByProvince"] = {
    bagmati: (data.provinceGroups.bagmati ?? []).map(normalizeArticle),
    koshi: (data.provinceGroups.koshi ?? []).map(normalizeArticle),
    madhesh: (data.provinceGroups.madhesh ?? []).map(normalizeArticle),
    gandaki: (data.provinceGroups.gandaki ?? []).map(normalizeArticle),
    lumbini: (data.provinceGroups.lumbini ?? []).map(normalizeArticle),
    karnali: (data.provinceGroups.karnali ?? []).map(normalizeArticle),
    sudurpaschim: (data.provinceGroups.sudurpaschim ?? []).map(normalizeArticle),
  };

  const renderedSections = managedSections.length > 0
    ? managedSections
    : categorySections.map(([section_key, category_slug, , layout], sort_order) => ({
        id: section_key,
        section_key,
        title: "",
        title_en: null,
        category_slug,
        layout,
        sort_order,
        is_active: true,
      }));

  return (
    <>
      <Header />
      <BreakingNewsTicker items={data.breakingNews} />
      <main className="public-home public-page-shell mx-auto w-full max-w-7xl min-w-0 px-3 py-5 sm:px-4 sm:py-8">
        <AdSlot position="HEADER" className="mx-auto mb-6 max-w-[728px] sm:mb-8" />
        <section className="editorial-band editorial-band--lead">
          <HeroDeck articles={data.featured.map(normalizeArticle)} />
        </section>
        <section id="latest-news" className="editorial-band">
          <DailyBrief articles={data.trending.slice(0, 5).map(normalizeArticle)} />
        </section>
        <LatestUpdatesRail articles={data.latestUpdates} />
        <div className="space-y-10 sm:space-y-14">
          {renderedSections.map((managed, index) => {
            const slug = managed.category_slug ?? "";
            const articles = data.categoryGroups[slug] ?? [];
            const color = articles[0]?.category?.color ?? "#07579B";
            return (
              <Fragment key={managed.id}>
                {section(
                  managed.section_key,
                  slug,
                  color,
                  managed.layout,
                  articles,
                  managed.title || undefined,
                  managed.title_en,
                )}
                {shouldInsertSectionAd(index) && (
                  <AdSlot
                    position="BETWEEN_SECTIONS"
                    className="mx-auto max-w-[970px]"
                  />
                )}
              </Fragment>
            );
          })}
          {section("article.trending", "", "#07579B", "list", data.trending)}
          {section("article.mostCommented", "", "#7b1fa2", "list", data.mostCommented)}
          {section("sections.didYouMiss", "samachar", "#607d8b", "grid", data.olderArticles)}
          <ProvincialNews articlesByProvince={provinces} />
          <EditorialFeatureStrip editorPicks={data.editorPicks} opinion={data.opinion} />
          <MediaHighlights reels={data.mediaHighlights.reels} galleries={data.mediaHighlights.galleries} />
        </div>
      </main>
      <Footer />
    </>
  );
}

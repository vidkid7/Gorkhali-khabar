import { Suspense } from "react";
import prisma from "@/lib/prisma";
import { Header } from "@/components/layout/Header";
import { Footer } from "@/components/layout/Footer";
import { ArticleCard } from "@/components/articles/ArticleCard";
import { BreakingNewsTicker } from "@/components/ui/BreakingNewsTicker";
import { SidebarListClient } from "@/components/ui/SidebarListClient";
import { CategorySection } from "@/components/home/CategorySection";
import { ReelsCarousel } from "@/components/home/ReelsCarousel";
import { LiveScoreWidget } from "@/components/home/LiveScoreWidget";
import { SectionHeader } from "@/components/home/SectionHeader";
import { AdSlot } from "@/components/ads/AdSlot";
import { FinanceWidget } from "@/components/widgets/FinanceWidget";
import { QuickLinks } from "@/components/home/QuickLinks";
import { EditorsPickClient } from "@/components/home/EditorsPickClient";
import { LatestUpdatesPanel } from "@/components/ui/LatestUpdatesPanel";
import { QuickNewsBanner } from "@/components/ui/QuickNewsBanner";
import {

  HeroSkeleton,
  ArticleCardSkeleton,
  SidebarSkeleton,
} from "@/components/ui/SkeletonLoader";

export const dynamic = "force-dynamic";

const articleSelect = {
  id: true, slug: true, title: true, title_en: true,
  excerpt: true, excerpt_en: true, featured_image: true,
  reading_time: true, published_at: true, view_count: true, comment_count: true,
  category: { select: { name: true, name_en: true, slug: true, color: true } },
  author: { select: { name: true } },
};

async function getBreakingNews() {
  return prisma.breakingNews.findMany({
    where: { is_active: true, OR: [{ expires_at: null }, { expires_at: { gt: new Date() } }] },
    include: { article: { select: { slug: true } } },
    orderBy: { created_at: "desc" },
    take: 10,
  });
}

async function getFeaturedArticles() {
  return prisma.article.findMany({
    where: { status: "PUBLISHED", is_featured: true },
    select: articleSelect,
    orderBy: { published_at: "desc" },
    take: 5,
  });
}

async function getArticlesByCategory(slug: string, take = 5) {
  return prisma.article.findMany({
    where: { status: "PUBLISHED", category: { slug } },
    select: articleSelect,
    orderBy: { published_at: "desc" },
    take,
  });
}

async function getTrendingArticles() {
  return prisma.article.findMany({
    where: { status: "PUBLISHED", published_at: { gte: new Date(Date.now() - 7 * 24 * 60 * 60 * 1000) } },
    select: articleSelect,
    orderBy: { view_count: "desc" },
    take: 5,
  });
}

async function getMostCommented() {
  return prisma.article.findMany({
    where: { status: "PUBLISHED", published_at: { gte: new Date(Date.now() - 7 * 24 * 60 * 60 * 1000) } },
    select: articleSelect,
    orderBy: { comment_count: "desc" },
    take: 5,
  });
}

async function getReels() {
  return prisma.reel.findMany({
    where: { is_active: true },
    orderBy: { created_at: "desc" },
    take: 10,
  });
}

async function getMatches() {
  return prisma.match.findMany({
    where: { status: { in: ["LIVE", "COMPLETED", "UPCOMING"] } },
    include: {
      home_team: { select: { name: true, name_en: true } },
      away_team: { select: { name: true, name_en: true } },
      tournament: { select: { name: true, name_en: true } },
    },
    orderBy: { match_date: "desc" },
    take: 4,
  });
}

async function getOldArticles() {
  return prisma.article.findMany({
    where: { status: "PUBLISHED", published_at: { lte: new Date(Date.now() - 12 * 60 * 60 * 1000) } },
    select: articleSelect,
    orderBy: { view_count: "desc" },
    take: 6,
  });
}

async function getEditorsPick() {
  return prisma.article.findMany({
    where: { status: "PUBLISHED", is_featured: true },
    select: articleSelect,
    orderBy: { published_at: "desc" },
    take: 3,
    skip: 5, // Skip the first 5 featured articles used in hero
  });
}

async function getCategories() {
  return prisma.category.findMany({
    where: { is_active: true },
    orderBy: { sort_order: "asc" },
  });
}

// ─── Server Components ───────────────────────────────

async function BreakingNewsSection() {
  const items = await getBreakingNews();
  if (items.length) return <BreakingNewsTicker items={items} />;

  // Fallback: show recent articles as a scrolling ticker when no breaking news is set
  const recentArticles = await prisma.article.findMany({
    where: { status: "PUBLISHED" },
    select: { id: true, slug: true, title: true, title_en: true },
    orderBy: { published_at: "desc" },
    take: 10,
  });
  if (!recentArticles.length) return null;
  const fallbackItems = recentArticles.map((a) => ({
    id: a.id,
    title: a.title,
    title_en: a.title_en,
    article: { slug: a.slug },
  }));
  return <BreakingNewsTicker items={fallbackItems} label="ताजा समाचार" />;
}

async function HeroSection() {
  const featured = await getFeaturedArticles();
  if (!featured.length) return null;
  const [main, ...rest] = featured;

  return (
    <section className="grid grid-cols-1 md:grid-cols-2 gap-6">
      <ArticleCard
        key={main.id} slug={main.slug} title={main.title} title_en={main.title_en}
        excerpt={main.excerpt} excerpt_en={main.excerpt_en} featured_image={main.featured_image}
        category={main.category} author={main.author} reading_time={main.reading_time}
        published_at={main.published_at} view_count={main.view_count} comment_count={main.comment_count} variant="hero"
      />
      <div className="space-y-4">
        {rest.map((a) => (
          <ArticleCard
            key={a.id} slug={a.slug} title={a.title} title_en={a.title_en}
            featured_image={a.featured_image} category={a.category} author={a.author}
            reading_time={a.reading_time} published_at={a.published_at}
            view_count={a.view_count} comment_count={a.comment_count} variant="horizontal"
          />
        ))}
      </div>
    </section>
  );
}

async function NewsSectionServer() {
  const articles = await getArticlesByCategory("samachar", 5);
  return <CategorySection sectionKey="sections.latestNews" articles={JSON.parse(JSON.stringify(articles))} color="#c62828" slug="samachar" layout="featured" />;
}

async function ReelsSectionServer() {
  const reels = await getReels();
  return <ReelsCarousel reels={JSON.parse(JSON.stringify(reels))} />;
}

async function FeatureSectionServer() {
  const articles = await getArticlesByCategory("feature", 5);
  return <CategorySection sectionKey="sections.feature" articles={JSON.parse(JSON.stringify(articles))} color="#ad1457" slug="feature" layout="featured" />;
}

async function CoverStorySectionServer() {
  const articles = await getArticlesByCategory("cover-story", 4);
  return <CategorySection sectionKey="sections.coverStory" articles={JSON.parse(JSON.stringify(articles))} color="#bf360c" slug="cover-story" layout="grid" />;
}

async function TechSectionServer() {
  const articles = await getArticlesByCategory("prabidhi", 5);
  return <CategorySection sectionKey="sections.technology" articles={JSON.parse(JSON.stringify(articles))} color="#6a1b9a" slug="prabidhi" layout="featured" />;
}

async function InterviewSectionServer() {
  const articles = await getArticlesByCategory("antarvaarta", 5);
  return <CategorySection sectionKey="sections.interview" articles={JSON.parse(JSON.stringify(articles))} color="#4e342e" slug="antarvaarta" layout="list" />;
}

async function SportsSectionServer() {
  const [articles, matches] = await Promise.all([
    getArticlesByCategory("khelkud", 5),
    getMatches(),
  ]);
  return (
    <div className="space-y-8">
      <CategorySection sectionKey="sections.sports" articles={JSON.parse(JSON.stringify(articles))} color="#e65100" slug="khelkud" layout="featured" />
      <LiveScoreWidget matches={JSON.parse(JSON.stringify(matches))} />
    </div>
  );
}

async function InternationalSectionServer() {
  const articles = await getArticlesByCategory("antarrashtriya", 4);
  return <CategorySection sectionKey="sections.international" articles={JSON.parse(JSON.stringify(articles))} color="#283593" slug="antarrashtriya" layout="grid" />;
}

async function WeirdWorldSectionServer() {
  const articles = await getArticlesByCategory("bichitra", 4);
  return <CategorySection sectionKey="sections.weirdWorld" articles={JSON.parse(JSON.stringify(articles))} color="#f57f17" slug="bichitra" layout="list" />;
}

async function LiteratureSectionServer() {
  const articles = await getArticlesByCategory("sahitya", 4);
  return <CategorySection sectionKey="sections.literature" articles={JSON.parse(JSON.stringify(articles))} color="#6d4c41" slug="sahitya" layout="list" />;
}

async function WeekendSectionServer() {
  const articles = await getArticlesByCategory("saptaahanta", 4);
  return <CategorySection sectionKey="sections.weekend" articles={JSON.parse(JSON.stringify(articles))} color="#558b2f" slug="saptaahanta" layout="grid" />;
}

async function DidYouMissSectionServer() {
  const articles = await getOldArticles();
  return <CategorySection sectionKey="sections.didYouMiss" articles={JSON.parse(JSON.stringify(articles))} color="#607d8b" slug="samachar" layout="grid" />;
}

async function SidebarSection() {
  const [trending, mostCommented] = await Promise.all([getTrendingArticles(), getMostCommented()]);
  return (
    <aside className="space-y-6">
      <SidebarListClient titleKey="article.trending" articles={trending.map((a) => ({
        id: a.id, slug: a.slug, title: a.title, title_en: a.title_en,
        view_count: a.view_count, comment_count: a.comment_count, category: a.category,
      }))} />
      <FinanceWidget />
      <SidebarListClient titleKey="article.mostCommented" articles={mostCommented.map((a) => ({
        id: a.id, slug: a.slug, title: a.title, title_en: a.title_en,
        view_count: a.view_count, comment_count: a.comment_count, category: a.category,
      }))} />
    </aside>
  );
}

async function EditorsPickSection() {
  const articles = await getEditorsPick();
  if (!articles.length) return null;
  return <EditorsPickClient articles={articles} />;
}

async function PoliticsSectionServer() {
  const articles = await getArticlesByCategory("rajniti", 5);
  return <CategorySection sectionKey="sections.politics" articles={JSON.parse(JSON.stringify(articles))} color="#c62828" slug="rajniti" layout="featured" />;
}

async function EconomySectionServer() {
  const articles = await getArticlesByCategory("arthatantra", 5);
  return <CategorySection sectionKey="sections.economy" articles={JSON.parse(JSON.stringify(articles))} color="#2e7d32" slug="arthatantra" layout="featured" />;
}

async function HealthSectionServer() {
  const articles = await getArticlesByCategory("swasthya", 4);
  return <CategorySection sectionKey="sections.health" articles={JSON.parse(JSON.stringify(articles))} color="#00897b" slug="swasthya" layout="grid" />;
}

async function VideoSectionServer() {
  const articles = await getArticlesByCategory("video", 5);
  return <CategorySection sectionKey="sections.video" articles={JSON.parse(JSON.stringify(articles))} color="#d50000" slug="video" layout="featured" />;
}

async function PhotoGallerySectionServer() {
  const articles = await getArticlesByCategory("photo-gallery", 4);
  return <CategorySection sectionKey="sections.gallery" articles={JSON.parse(JSON.stringify(articles))} color="#0097a7" slug="photo-gallery" layout="grid" />;
}

async function OpinionSectionServer() {
  const articles = await getArticlesByCategory("bichar", 5);
  return <CategorySection sectionKey="sections.opinion" articles={JSON.parse(JSON.stringify(articles))} color="#00838f" slug="bichar" layout="list" />;
}

async function EducationSectionServer() {
  const articles = await getArticlesByCategory("shiksha", 4);
  return <CategorySection sectionKey="sections.education" articles={JSON.parse(JSON.stringify(articles))} color="#1976d2" slug="shiksha" layout="grid" />;
}

async function QuickNewsBannerSection({ category, lang = "ne" }: { category: string; lang?: "ne" | "en" }) {
  const articles = await prisma.article.findMany({
    where: { status: "PUBLISHED", category: { slug: category } },
    select: {
      id: true, slug: true, title: true, title_en: true, published_at: true,
      category: { select: { name: true, name_en: true, slug: true, color: true } },
    },
    orderBy: { published_at: "desc" },
    take: 1,
  });
  if (!articles[0]) return null;
  const a = articles[0];
  return (
    <QuickNewsBanner
      title={a.title}
      title_en={a.title_en}
      slug={a.slug}
      categoryName={a.category.name}
      categoryColor={a.category.color}
      publishedAt={a.published_at}
    />
  );
}



// ─── Skeleton fallback ──────────────────────────────

function SectionSkeleton() {
  return (
    <div className="animate-pulse space-y-4">
      <div className="h-6 w-40 bg-surface rounded" />
      <div className="grid grid-cols-1 lg:grid-cols-5 gap-5">
        <div className="lg:col-span-3 h-64 bg-surface rounded-lg" />
        <div className="lg:col-span-2 space-y-3">
          {Array.from({ length: 4 }).map((_, i) => (
            <div key={i} className="flex gap-3"><div className="w-24 h-16 bg-surface rounded" /><div className="flex-1 space-y-2"><div className="h-3 bg-surface rounded w-full" /><div className="h-3 bg-surface rounded w-2/3" /></div></div>
          ))}
        </div>
      </div>
    </div>
  );
}

// ─── Page ────────────────────────────────────────────

export default async function HomePage() {
  return (
    <>
      <Header />
      <Suspense fallback={null}>
        <BreakingNewsSection />
      </Suspense>

      {/* Latest Updates floating panel */}
      <LatestUpdatesPanel />

      {/* Header Banner Ad */}
      <div className="mx-auto max-w-7xl px-4 pt-4">
        <AdSlot position="HEADER" />
      </div>

      <main className="mx-auto max-w-7xl px-3 sm:px-4 py-4 sm:py-6 space-y-8 sm:space-y-10 pb-safe">
        {/* Hero */}
        <Suspense fallback={<HeroSkeleton />}>
          <HeroSection />
        </Suspense>

        {/* Quick Links */}
        <QuickLinks />

        {/* News + Sidebar */}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div className="md:col-span-2">
            <Suspense fallback={<SectionSkeleton />}>
              <NewsSectionServer />
            </Suspense>
          </div>
          <div>
            <Suspense fallback={<SidebarSkeleton />}>
              <SidebarSection />
            </Suspense>
          </div>
        </div>

        {/* Quick Banner: latest politics */}
        <Suspense fallback={null}>
          <QuickNewsBannerSection category="rajniti" />
        </Suspense>

        {/* Editor's Pick */}
        <Suspense fallback={<SectionSkeleton />}>
          <EditorsPickSection />
        </Suspense>

        {/* Ad between sections */}
        <AdSlot position="BETWEEN_SECTIONS" />

        {/* Reels Carousel */}
        <Suspense fallback={<div className="h-72 bg-surface rounded-xl animate-pulse" />}>
          <ReelsSectionServer />
        </Suspense>

        <hr className="section-divider" />

        {/* Politics */}
        <Suspense fallback={<SectionSkeleton />}>
          <PoliticsSectionServer />
        </Suspense>

        <hr className="section-divider" />

        {/* Economy + Finance side-by-side */}
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
          <Suspense fallback={<SectionSkeleton />}>
            <EconomySectionServer />
          </Suspense>
          <Suspense fallback={<SectionSkeleton />}>
            <FeatureSectionServer />
          </Suspense>
        </div>

        {/* Quick Banner: latest economy */}
        <Suspense fallback={null}>
          <QuickNewsBannerSection category="arthatantra" />
        </Suspense>

        {/* Cover Story */}
        <Suspense fallback={<SectionSkeleton />}>
          <CoverStorySectionServer />
        </Suspense>

        <hr className="section-divider" />

        {/* Video Section */}
        <Suspense fallback={<SectionSkeleton />}>
          <VideoSectionServer />
        </Suspense>

        {/* Ad between sections */}
        <AdSlot position="IN_ARTICLE" />

        {/* Technology + Interview side-by-side */}
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
          <Suspense fallback={<SectionSkeleton />}>
            <TechSectionServer />
          </Suspense>
          <Suspense fallback={<SectionSkeleton />}>
            <InterviewSectionServer />
          </Suspense>
        </div>

        {/* Sports + Live Score */}
        <Suspense fallback={<SectionSkeleton />}>
          <SportsSectionServer />
        </Suspense>

        <hr className="section-divider" />

        {/* Health section */}
        <Suspense fallback={<SectionSkeleton />}>
          <HealthSectionServer />
        </Suspense>

        {/* Quick Banner: latest health */}
        <Suspense fallback={null}>
          <QuickNewsBannerSection category="swasthya" />
        </Suspense>

        {/* Weekend */}
        <Suspense fallback={<SectionSkeleton />}>
          <WeekendSectionServer />
        </Suspense>

        <hr className="section-divider" />

        {/* Opinion + Education side-by-side */}
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
          <Suspense fallback={<SectionSkeleton />}>
            <OpinionSectionServer />
          </Suspense>
          <Suspense fallback={<SectionSkeleton />}>
            <EducationSectionServer />
          </Suspense>
        </div>

        {/* Ad before international */}
        <AdSlot position="FOOTER" />

        {/* International */}
        <Suspense fallback={<SectionSkeleton />}>
          <InternationalSectionServer />
        </Suspense>

        <hr className="section-divider" />

        {/* Photo Gallery */}
        <Suspense fallback={<SectionSkeleton />}>
          <PhotoGallerySectionServer />
        </Suspense>

        {/* Literature + Weird World side-by-side */}
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
          <Suspense fallback={<SectionSkeleton />}>
            <LiteratureSectionServer />
          </Suspense>
          <Suspense fallback={<SectionSkeleton />}>
            <WeirdWorldSectionServer />
          </Suspense>
        </div>

        {/* Did You Miss? */}
        <Suspense fallback={<SectionSkeleton />}>
          <DidYouMissSectionServer />
        </Suspense>
      </main>
      <Footer />
    </>
  );
}

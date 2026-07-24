const { PrismaClient } = require('@prisma/client');
const prisma = new PrismaClient();

async function test() {
  const provinceSlugs = [
    'bagmati-pradesh',
    'koshi-pradesh',
    'madhesh-pradesh',
    'gandaki-pradesh',
    'lumbini-pradesh',
    'karnali-pradesh',
    'sudurpaschim-pradesh',
  ];
  
  const articles = await prisma.article.findMany({
    where: {
      status: 'PUBLISHED',
      category: { slug: { in: provinceSlugs } },
    },
    select: {
      id: true,
      slug: true,
      title: true,
      title_en: true,
      excerpt: true,
      excerpt_en: true,
      featured_image: true,
      reading_time: true,
      published_at: true,
      view_count: true,
      comment_count: true,
      category: { select: { name: true, name_en: true, slug: true, color: true } },
      author: { select: { name: true } },
    },
    orderBy: { published_at: 'desc' },
    take: 35,
  });
  
  console.log('Found:', articles.length, 'articles');
  articles.forEach(a => console.log(a.id, '=>', a.title, '| category:', a.category?.slug));
  
  await prisma.$disconnect();
}
test().catch(console.error);
const { PrismaClient } = require('@prisma/client');
const prisma = new PrismaClient();

async function test() {
  const articles = await prisma.article.findMany({
    where: {
      status: 'PUBLISHED',
      category: { slug: 'bagmati-pradesh' }
    },
    include: {
      category: { select: { id: true, name: true, slug: true } }
    },
    orderBy: { published_at: 'desc' },
    take: 5
  });
  console.log('Found:', articles.length, 'articles');
  articles.forEach(a => console.log(a.id, '=>', a.title, '| category:', a.category?.slug));
  await prisma.$disconnect();
}
test().catch(console.error);
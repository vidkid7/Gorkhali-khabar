# Editorial Verticals and Category Seeding Design

## Objective

Add editorial depth to Gorkhali Khabar through new, admin-managed reporting
categories, realistic labelled sample content, and additive category-page
patterns inspired by the information hierarchy of major newsroom websites.

## Chosen Direction

Three approaches were considered:

1. Add every category to the main navigation.
2. Keep the main navigation focused and group new editorial verticals under
   `अन्य / More`.
3. Create one catch-all feature hub with no separate category routes.

The selected approach is **2**. It preserves the current primary navigation
while giving readers direct routes and the admin full control of each new
vertical.

## Categories

The seeder will ensure all navigation-referenced categories exist:

- अन्तर्राष्ट्रिय / International
- फिचर / Features
- भिडियो / Video

It will add these new editorial verticals:

- अनुसन्धान / Investigations
- जलवायु र वातावरण / Climate & Environment
- कृषि / Agriculture
- पर्यटन / Travel
- कला र संस्कृति / Arts & Culture
- जीवनशैली / Lifestyle
- सुरक्षा र अपराध / Security & Crime
- रोजगारी / Jobs & Careers
- प्रवास / Diaspora

All new verticals appear under `अन्य / More` in the public navigation. They
also appear in the footer/category discovery area where space permits. Existing
categories, province categories, and routes remain unchanged.

## Content and Admin Management

Each category is an existing Laravel `Category` record and is therefore managed
through the existing admin category and article interfaces. The seeder will
upsert the categories by slug and create three clearly labelled demonstration
articles per added or previously missing category. Seeded headlines and copy are
fictional examples for demonstration; no real reporting, bylines, or claims are
represented as current news.

Every article includes title, English title, summary, English summary, body,
category assignment, published state, a safe local placeholder image, and a
recent seeded timestamp. Re-running the seeder is idempotent and does not
overwrite administrator-authored content.

## Additive Public Design

The design borrows only high-level newsroom patterns:

- A restrained topic strip in the `अन्य / More` menu that improves category
  discovery.
- A ranked `धेरै पढिएको / Most Read` rail using existing article data, not an
  external service.
- Category pages that retain their lead story but add a clearer latest-stories
  list and compact topic navigation.

The existing Gorkhali Khabar logo, red/navy visual identity, ad placements,
header, footer, and prior homepage work remain intact. No Washington Post
content, branding, visual assets, or code will be copied.

## Verification

Tests will verify category/upsert completeness, sample-article count and
idempotency, navigation grouping, and category-page rendering. Live checks will
verify the new category routes, the More menu at desktop and mobile sizes, and
admin visibility after reseeding.

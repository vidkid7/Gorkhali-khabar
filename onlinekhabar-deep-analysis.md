# OnlineKhabar.com — Deep Website Analysis & Blueprint

> **Source:** https://www.onlinekhabar.com/
> **Analysis Date:** 2026-04-12
> **Purpose:** Complete reverse-engineering of site structure, UI components, backend architecture, and content taxonomy for building a professional clone using Next.js + React + PostgreSQL with Admin Panel.

---

## 1. SITE OVERVIEW

| Property | Value |
|---|---|
| **Site Name** | Online Khabar (अनलाइनखबर) |
| **Tagline** | No 1 News Portal from Nepal in Nepali |
| **Chairman & MD** | धर्मराज भुसाल |
| **Chief Editor** | बसन्त बस्नेत |
| **Registration** | सूचना विभाग दर्ता नं. २१४ / ०७३–७४ |
| **Contact** | +977-1-4790176, +977-1-4796489 |
| **Email** | news@onlinekhabar.com |
| **Copyright** | © २००६-२०२६ Onlinekhabar.com |
| **CMS** | WordPress 6.9.4 (Custom Theme: `onlinekhabar-2021`) |
| **Language** | Nepali (primary), English (secondary section) |
| **Social** | Facebook (fb:pages 108349739223556), Twitter (@online_khabar) |
| **Analytics** | Google Analytics (UA-4599822-2), Custom analytics (analytics.onlinekhabar.com) |
| **Ad System** | Custom WordPress plugin (`okam` — ok ad manager) |

---

## 2. TECHNOLOGY STACK (Original)

### 2.1 Frontend
- **jQuery 3.7.1** + jQuery Migrate 3.4.1
- **Underscore.js 1.13.7**
- **Owl Carousel** (for sliders/carousels)
- **Font Awesome** (icon library)
- **Google Fonts** (preconnected)
- **Custom CSS Architecture:**
  - `_spacing.css` — spacing utilities
  - `_main-style.css` — core styles
  - `_business.css` — business section styles
  - `_lifestyle.css` — lifestyle section styles
  - `_entertainment.css` — entertainment section
  - `_sport-news.css` — sports section
  - `_worldcup.css` — world cup specific
  - `_bichar.css` — opinion/editorial section
  - `_author.css` — author pages
  - `_author-single.css` — single author view
  - `_user-profile.css` — user profiles
  - `_oksports.css` — OK Sports section
  - `_cric.css` — cricket section
  - `_football.css` — football section
  - `_tab.css` — tablet responsive
  - `_mobile.css` — mobile responsive
  - `_rotae_adv.css` — rotating advertisements
  - `_adv.css` — advertisement styles
  - `new-ext-style.css` — latest extended styles

### 2.2 Backend / Plugins
- **WordPress REST API** (`/wp-json/okapi/v2/`)
- **Custom Plugins:**
  - `ok-comments-like-dislike` — comment voting system
  - `ok-user-manager` — user auth, Google OAuth login
  - `ok-analytics` — custom analytics tracking
  - `ok-health` — health content management
  - `ok-share-shikshya` — education content sharing
  - `ok-web-stories` — web story format
  - `okam` — ad manager with mobile detection
- **API Endpoints:**
  - `POST /wp-admin/admin-ajax.php` — AJAX handler
  - `GET /wp-json/okapi/v2/post-views-count` — view counter
  - `GET /wp-json/wp/v2/pages/699430` — page data
  - RSS: `/feed` and `/comments/feed`
  - oEmbed support

### 2.3 Authentication
- WordPress user system
- Google OAuth (Client ID: `36651860021-...`)
- Callback: `/?verify_google_login`
- Custom user manager plugin

---

## 3. COMPLETE NAVIGATION STRUCTURE

### 3.1 Top Bar / Ticker
- **Date display** (Nepali calendar: e.g., "२०८२ वैशाख १३, शनिबार")
- **Breaking news ticker** (auto-scrolling horizontal text)
- **Language switcher** (Nepali / English)
- **Social media links** (Facebook, Twitter/X, YouTube, Instagram)
- **User login/register button**

### 3.2 Main Header
- **Logo** (`main-logo-new.svg`)
- **Search bar** with search icon
- **Top advertisement banner** (leaderboard 728×90 / responsive)

### 3.3 Primary Navigation Menu (Confirmed — Live Site 2026)
| Menu Item (Nepali) | English | URL Pattern | Has Megamenu |
|---|---|---|---|
| होमपेज | Homepage | `/` | ❌ |
| समाचार | News | `/content/news/rastiya` | ✅ Yes |
| बिजनेस | Business | `/business` | ✅ |
| जीवनशैली | Lifestyle | `/lifestyle` | ✅ |
| मनोरञ्जन | Entertainment | `/entertainment` | ✅ |
| विचार | Opinion | `/opinion` | ✅ |
| खेलकुद | Sports | `/sports` | ✅ |
| अन्य | Others | Various | ✅ |

> **Note:** Navigation order confirmed by re-scraping live site. Previous analysis had incorrect order and category labels.

### 3.4 Mega Menu Structure
Each mega menu dropdown contains:
- **Grid of 4-6 featured articles** with thumbnails
- **Category tags** (colored: `.ok-news-tags.red`)
- **Article titles**
- **Subcategory sidebar** with additional category links

### 3.5 Sub-Navigation Categories (Full Taxonomy — Confirmed Live Site)

#### समाचार (News) — `/content/news/rastiya`
| Sub-Section | URL |
|---|---|
| राष्ट्रिय (National) | `/content/news/rastiya` |
| प्रदेश (Province) | `/content/desh-samachar` |
| फिचर (Feature) | `/content/feature-samachar` |
| अन्तर्वार्ता (Interview) | `/content/interview` |
| साहित्य (Literature) | `/content/sathiya-gatibidhi` |
| फोटो ग्यालरी | `/content/nepalbeauty` |
| सप्ताहान्त (Weekend) | `/segment/weekend` |
| कभर स्टोरी | `/segment/coverstory` |
| विचित्र संसार | `/content/bichitra` |
| पौरखी प्रवासी | `/segment/nepali-diaspora` |
| इन्टर्‍याक्टिभ स्टोरी | `/interactive-html` |
| OK रिल्स | `/ok-reels/*` |
| ब्लग (Blog) | `/content/blog` |

#### बिजनेस (Business) — `/business`
| Sub-Section | URL |
|---|---|
| फिचर (Feature) | `/content/business/business-feature` |
| अटो (Auto) | `/content/auto` |
| बैंक/वित्त (Bank/Finance) | `/content/business/bank` |
| पर्यटन (Tourism) | `/content/tourism` |
| सूचना-प्रविधि (Technology) | `/content/technology-news` |
| कर्पोरेट (Corporate) | `/content/business/corporate` |
| अन्तर्वार्ता (Biz-Talk) | `/content/biz-talk` |
| विचार (Opinion) | `/content/economy-opinion` |
| ब्लग (Blog) | `/content/economy-blog` |
| सेयर बजार (Share Market) | `/content/business/share-market` |
| अर्थनीति (Economic Policy) | `/content/business/eco-policy` |
| रोजगार (Employment) | `/content/rojgar` |

#### जीवनशैली (Lifestyle) — `/lifestyle`
| Sub-Section | URL |
|---|---|
| लालन-पालन (Parenting) | `/content/lalan-palan` |
| खानपान (Food) | `/content/khanpan` |
| फेसन/सौन्दर्य (Fashion/Beauty) | `/content/feshion` |
| लाइफस्टाइल कभर स्टोरी | `/content/beauty` |
| हेल्थ टिप्स (Health Tips) | `/content/health-tips` |
| सम्बन्ध (Relationship) | `/content/sambandha` |
| संस्कार/संस्कृति (Culture) | `/content/religionfallow` |
| यौन स्वास्थ्य (Sexual Health) | `/content/health/sex-health` |
| यात्रा (Travel) | `/content/lifestyle-travel` |

#### मनोरञ्जन (Entertainment) — `/entertainment`
| Sub-Section | URL |
|---|---|
| ताजा समाचार (Latest News) | `/content/entertainment/ent-news` |
| गसिप (Gossip) | `/content/gassip` |
| बलिउड/हलिउड (Bollywood/Hollywood) | `/content/bolly-hollywood` |
| भिडियो (Video) | `/content/ent-video` |
| मनोरञ्जन-वार्ता (Interview) | `/content/ent-interview` |
| मोडल ग्यालरी (Model Gallery) | `/content/modelgallery` |
| फिचर (Feature) | `/content/feature-samachar` |

#### विचार (Opinion) — `/opinion`
| Sub-Section | URL |
|---|---|
| लेखक छान्नुहोस् (Choose Author) | Author filter on `/opinion` |
| अन्तर्वार्ता (Interview) | `/content/interview` |

#### खेलकुद (Sports) — `/sports`
| Sub-Section | URL |
|---|---|
| फुटबल (Football) | `/content/football` |
| क्रिकेट (Cricket) | `/content/cricket` |
| NSL 2025 | `/nsl-2025` |
| अन्य खेलकुद | `/content/othersport` |

---

## 4. HOMEPAGE LAYOUT (Section by Section)

### 4.1 Overall Layout
```
┌──────────────────────────────────────────────────┐
│                  TOP BAR (ticker)                 │
├──────────────────────────────────────────────────┤
│          HEADER (logo + search + ad)             │
├──────────────────────────────────────────────────┤
│              MAIN NAVIGATION                      │
│   होमपेज|समाचार|बिजनेस|जीवनशैली|मनोरञ्जन       │
│   |विचार|खेलकुद|अन्य                            │
├──────────────────────────────────────────────────┤
│              TOP AD BANNER                        │
├──────────────────────────────────────────────────┤
│  HERO / BISES SECTION (Featured editorial)       │
│  ┌─────────────────────────┐                     │
│  │ Full-width featured     │                     │
│  │ article with author,    │                     │
│  │ time, comments count    │                     │
│  │ + large thumbnail       │                     │
│  │ + excerpt               │                     │
│  └─────────────────────────┘                     │
├──────────────────────────────────────────────────┤
│  AD SLOT (after-breaking1)                       │
├──────────────────────────────────────────────────┤
│  SECOND FEATURED (ok-bises-type-2)               │
├──────────────────────────────────────────────────┤
│  OK REELS SECTION (horizontal scroll)            │
│  ┌──┐ ┌──┐ ┌──┐ ┌──┐ ┌──┐                       │
│  │▶ │ │▶ │ │▶ │ │▶ │ │▶ │                       │
│  └──┘ └──┘ └──┘ └──┘ └──┘                       │
├──────────────────────────────────────────────────┤
│  NEWS SECTION (समाचार)                           │
│  ┌─────────────────┐ ┌──────────┐               │
│  │ Main Left Col   │ │ Sidebar  │               │
│  │ ┌──────────────┐│ │ ┌──────┐ │               │
│  │ │ Lead Article ││ │ │Trend │ │               │
│  │ │ (full-width) ││ │ │Items │ │               │
│  │ └──────────────┘│ │ └──────┘ │               │
│  │ ┌─────┐ ┌─────┐│ │ ┌──────┐ │               │
│  │ │Card │ │Card ││ │ │ Ads  │ │               │
│  │ │     │ │     ││ │ │      │ │               │
│  │ └─────┘ └─────┘│ │ └──────┘ │               │
│  └─────────────────┘ └──────────┘               │
├──────────────────────────────────────────────────┤
│  FEATURE SECTION (फिचर)                          │
├──────────────────────────────────────────────────┤
│  COVER STORY SECTION (कभर स्टोरी)                │
│  With election seat counter (१६५ प्रत्यक्ष सिट)  │
│  Party breakdown widget (live data)              │
├──────────────────────────────────────────────────┤
│  WEEKEND SECTION (सप्ताहान्त)                     │
├──────────────────────────────────────────────────┤
│  TECHNOLOGY SECTION (सूचना-प्रविधि)              │
├──────────────────────────────────────────────────┤
│  TRENDING DOCTOR SECTION                         │
├──────────────────────────────────────────────────┤
│  INTERVIEW SECTION (अन्तर्वार्ता)                │
│  (with quote icon and large banner)              │
├──────────────────────────────────────────────────┤
│  SPORTS SECTION (खेलकुद)                         │
├──────────────────────────────────────────────────┤
│  LITERATURE SECTION (साहित्य)                     │
├──────────────────────────────────────────────────┤
│  BLOG SECTION (ब्लग)          ← CONFIRMED NEW   │
├──────────────────────────────────────────────────┤
│  INTERACTIVE STORY                                │
├──────────────────────────────────────────────────┤
│  INTERNATIONAL SECTION (अन्तर्राष्ट्रिय)         │
├──────────────────────────────────────────────────┤
│  VIDEO SECTION (भिडियो)                           │
├──────────────────────────────────────────────────┤
│  BIZARRE WORLD (विचित्र संसार)                    │
├──────────────────────────────────────────────────┤
│  DIASPORA SECTION (पौरखी प्रवासी)                │
├──────────────────────────────────────────────────┤
│  PHOTO GALLERY (फोटो ग्यालरी)                    │
├──────────────────────────────────────────────────┤
│  MISSED SECTION (छुटाउनुभयो कि ?)               │
├──────────────────────────────────────────────────┤
│                  FOOTER                           │
└──────────────────────────────────────────────────┘
```

### 4.1.1 Mobile Bottom Navigation Bar (Mobile Only — Confirmed)
```
┌──────────────────────────────────────────────────┐
│  MOBILE BOTTOM FIXED NAV (position: fixed)       │
│  [ताजा अपडेट] [ट्रेन्डिङ] [प्रोफाइल] [सर्च🔍]  │
└──────────────────────────────────────────────────┘
```
- **ताजा अपडेट** — Latest news feed
- **ट्रेन्डिङ** — Trending articles
- **प्रोफाइल** — User profile / login
- **सर्च** — Search overlay

### 4.2 Bises (Featured) Section — `ok-bises`, `ok-bises-type-2`
- **Tag badge** (e.g., "प्रहरी") — colored label
- **Article title** (h2, large)
- **Author info:** avatar + name
- **Timestamp:** clock icon + relative time (e.g., "२ घण्टा अगाडि")
- **Comment count:** comment icon + count
- **Featured image** (large, full-width)
- **Excerpt** (paragraph summary)

### 4.3 News Grid Section
- **Container:** `.ok-section.ok-samachar-section`
- **Layout:** Flexbox, left column + right sidebar
- **Section title** with arrow link to full category
- **Lead article:** `.ok-samachar-spot-news` — large image left, text right
- **Grid cards:** `.ok-news-post.ok-post-ltr` — 2 columns, 6 articles
  - Thumbnail (270×170)
  - Category tag (red badge)
  - Title (h2)
  - Timestamp

### 4.4 OK Reels Section
- Horizontal scrollable row of 5+ video thumbnails
- Each reel links to `/ok-reels/{slug}`
- Video thumbnail image with play indicator

### 4.5 Sports Section
- Live score cards (carousel, Owl Carousel)
- League/tournament tabs: NSL, International, Gold Cup
- Match cards with team logos, scores, dates
- Article grid below

### 4.6 Interview Section
- Large quote icon (white on colored bg)
- Full-width banner image
- Featured interview article

---

## 5. ARTICLE/SINGLE POST PAGE

### 5.1 Layout
```
┌──────────────────────────────────────────────────┐
│ BREADCRUMB: Home > Category > Article Title      │
├──────────────────────────────────────────────────┤
│ ARTICLE HEADER                                   │
│ ┌──────────────────────────────────────────────┐ │
│ │ Category Tag                                 │ │
│ │ H1: Article Title                            │ │
│ │ Author Avatar | Author Name | Date | Views   │ │
│ │ Social Share Buttons (FB, Twitter, Copy)      │ │
│ └──────────────────────────────────────────────┘ │
├──────────────────────────────────────────────────┤
│ AI SUMMARY BOX (OK AI)                           │
│ "Generated by OK AI. Editorially reviewed."      │
│ • Bullet point summary 1                         │
│ • Bullet point summary 2                         │
│ • Bullet point summary 3                         │
├──────────────────────────────────────────────────┤
│ FEATURED IMAGE (full-width)                      │
├──────────────────────────────────────────────────┤
│ ARTICLE BODY                                     │
│ - Paragraphs with inline images                  │
│ - Pull quotes                                    │
│ - Embedded media                                 │
├──────────────────────────────────────────────────┤
│ TAGS / KEYWORDS                                  │
├──────────────────────────────────────────────────┤
│ LIKE/DISLIKE BUTTONS                             │
├──────────────────────────────────────────────────┤
│ COMMENTS SECTION                                 │
│ - Comment form (requires login)                  │
│ - Comment list with like/dislike                 │
├──────────────────────────────────────────────────┤
│ RELATED ARTICLES                                 │
├──────────────────────────────────────────────────┤
│ SIDEBAR (on desktop)                             │
│ - Trending news                                  │
│ - Recent news                                    │
│ - Advertisements                                 │
│ - Social media follow                            │
└──────────────────────────────────────────────────┘
```

### 5.2 AI Summary Feature
- Header: "News Summary — Generated by OK AI. Editorially reviewed."
- Bullet-point summary of key facts
- Appears above the article body

### 5.3 Author Block
- Author avatar (270×170 thumbnail)
- Author name (clickable to author page)
- Relative timestamp with clock icon
- Comment count with icon

### 5.4 Social Sharing
- Facebook share
- Twitter/X share
- Copy link
- Share via Messenger
- Share via Viber/WhatsApp

---

## 6. CSS CLASS SYSTEM & UI COMPONENTS

### 6.1 Container System
| Class | Purpose |
|---|---|
| `.ok-container` | Main content container (max-width centered) |
| `.ok__container` | Alternative container |
| `.flx` | Flexbox display |
| `.flx-wrp` | Flex wrap |
| `.ok-col-left` | Left column in layout |
| `.ok-col-right` | Right sidebar |

### 6.2 Grid System
| Class | Purpose |
|---|---|
| `.ok-grid-12` | 12-column grid |
| `.span-12` | Full width |
| `.span-6` | Half width (2-column) |
| `.span-4` | Third width |
| `.span-3` | Quarter width |

### 6.3 News Post Components
| Class | Purpose |
|---|---|
| `.ok-news-post` | Base news article card |
| `.ok-post-ltr` | Left-to-right layout card |
| `.ok-post-thumb` | Article thumbnail image |
| `.ok-post-content-wrap` | Content wrapper inside card |
| `.ok-news-title-txt` | Article title text (h2) |
| `.ok-news-tags` | Category tag badge |
| `.ok-news-tags.red` | Red-colored tag |
| `.ok-samachar-spot-news` | Lead/featured news card |
| `.post-img-wrap` | Image wrapper in lead card |
| `.post-title-wrap` | Title wrapper in lead card |

### 6.4 Author & Meta
| Class | Purpose |
|---|---|
| `.ok-news-author-wrap` | Author container |
| `.ok-news-author` | Author name + icon |
| `.author-icon` | Author avatar wrapper |
| `.author-name` | Author name text |
| `.ok-news-post-hour` | Timestamp display |
| `.ok-news-comment` | Comment count |
| `.ok-title-info` | Metadata row (author + time + comments) |

### 6.5 Section Components
| Class | Purpose |
|---|---|
| `.ok-section` | Generic section wrapper |
| `.ok-section-title` | Section heading with arrow link |
| `.ok-bises` | Featured/editorial section |
| `.ok-bises-type-2` | Alternate featured layout |
| `.ok-bises-tag` | Featured section tag |
| `.ok-bises-feauted-img` | Featured image wrapper |
| `.ok-megamenu` | Mega menu dropdown |
| `.all-cats` | All categories mega menu |
| `.cat-label` | Category label in sidebar |

### 6.6 Advertisement System
| Class | Purpose |
|---|---|
| `.ok-full-widht-adv` | Full-width ad container |
| `.add__fullwidth` | Ad wrapper |
| `.okam-ad-position-wrap` | Ad position marker |
| `.okam-device-desktop` | Desktop-only ad |
| `.okam-device-mobile` | Mobile-only ad |
| `.after-topics` | Ad position: after topics |
| `.home-after-breaking1` | Ad position alias |
| `.home-after-breaking1mb` | Mobile ad alias |

### 6.7 Interactive Elements
| Class | Purpose |
|---|---|
| `.circle-arrow` | Circular arrow button (→) |
| `.right-spot` | Right sidebar spot |
| `.left-spot` | Left content spot |

---

## 7. AD POSITIONS MAP

| Position Alias | Location | Device |
|---|---|---|
| `home-after-breaking1` | After breaking news/hero | Desktop |
| `home-after-breaking1mb` | After breaking news/hero | Mobile |
| `home-after-webstory` | After web stories | Desktop |
| `home-after-webstory-mb` | After web stories | Mobile |
| `home-aftersamachar-half1` | Inside news grid | Desktop |
| `home-aftersamachar-half1-mb` | Inside news grid | Mobile |
| Top leaderboard | Header area | All |
| Sidebar rectangles | Right sidebar | Desktop |
| In-article ads | Between paragraphs | All |
| Footer ads | Before footer | All |

---

## 8. RESPONSIVE BREAKPOINTS

| Breakpoint | CSS File | Target |
|---|---|---|
| Default | `_main-style.css` | Desktop (1200px+) |
| Tablet | `_tab.css` | Tablet (768px–1199px) |
| Mobile | `_mobile.css` | Mobile (<768px) |
| Max 600px | WordPress admin bar adjustment | Small mobile |

### 8.1 Mobile-Specific Behaviors
- Hamburger menu replacing navigation
- Stacked single-column layouts
- Mobile-specific ad slots
- Smaller thumbnails
- Touch-friendly tap targets
- Device detection via `okam-mobile-detect.js`

---

## 9. SPORTS SECTION (Dedicated Page)

### 9.1 Structure
```
┌──────────────────────────────────────────────────┐
│ LEAGUE TAB NAVIGATION                            │
│ [NSL 2025] [Int'l Women] [RARA Gold Cup]         │
├──────────────────────────────────────────────────┤
│ LIVE SCORE CAROUSEL                              │
│ ← [Match Card] [Match Card] [Match Card] →      │
│    Team Logo | Score | Date/Time                  │
├──────────────────────────────────────────────────┤
│ CRICKET TABLE (अन्तराष्ट्रिय क्रिकेट तालिका)    │
├──────────────────────────────────────────────────┤
│ DOMESTIC CRICKET TABLE                           │
├──────────────────────────────────────────────────┤
│ FOOTBALL TABLE                                   │
├──────────────────────────────────────────────────┤
│ SPORTS NEWS GRID                                 │
└──────────────────────────────────────────────────┘
```

### 9.2 Live Score Card
- Team logo images
- Team name (e.g., "Lalitpur City FC")
- Result status ("WON", "LOST", "DRAW")
- Date in Nepali calendar
- Match time
- Carousel navigation arrows

---

## 10. CATEGORY/ARCHIVE PAGES

### 10.1 Layout
```
┌──────────────────────────────────────────────────┐
│ CATEGORY HEADER (Category Name)                  │
├──────────────────────────────────────────────────┤
│  ┌─────────────────┐ ┌──────────┐               │
│  │ Main Content    │ │ Sidebar  │               │
│  │ Article List    │ │          │               │
│  │ ┌──────────────┐│ │ Trending │               │
│  │ │ Article Card ││ │ Recent   │               │
│  │ │ Thumb + Title││ │ Ads      │               │
│  │ │ + Excerpt    ││ │          │               │
│  │ └──────────────┘│ │          │               │
│  │ ... (10-15)     │ │          │               │
│  │ PAGINATION      │ │          │               │
│  └─────────────────┘ └──────────┘               │
├──────────────────────────────────────────────────┤
│ FOOTER                                           │
└──────────────────────────────────────────────────┘
```

### 10.2 Pagination
- Numbered pages
- Previous/Next buttons
- Ajax load more (for infinite scroll sections)

---

## 11. FOOTER STRUCTURE

```
┌──────────────────────────────────────────────────┐
│ FOOTER TOP                                       │
│ ┌──────────┐ ┌──────────┐ ┌──────────┐          │
│ │ Logo     │ │ Quick    │ │ Category │          │
│ │ About    │ │ Links    │ │ Links    │          │
│ │ Contact  │ │          │ │          │          │
│ └──────────┘ └──────────┘ └──────────┘          │
├──────────────────────────────────────────────────┤
│ FOOTER BOTTOM                                    │
│ Chairman: धर्मराज भुसाल                           │
│ Chief Editor: बसन्त बस्नेत                       │
│ Registration: २१४ / ०७३–७४                        │
│ Phone: +977-1-4790176, +977-1-4796489            │
│ Email: news@onlinekhabar.com                     │
│ © २००६-२०२६ Onlinekhabar.com सर्वाधिकार सुरक्षित │
│ Social Icons: FB, Twitter, YouTube, Instagram    │
└──────────────────────────────────────────────────┘
```

---

## 12. SEO & META STRUCTURE

### 12.1 Head Tags
```html
<title>{Page Title} – No 1 News Portal from Nepal in Nepali.</title>
<meta name="description" content="..." />
<meta name="robots" content="max-image-preview:large" />
<link rel="canonical" href="{URL}" />
```

### 12.2 Open Graph (Facebook)
```html
<meta property="og:title" content="{Title}" />
<meta property="og:url" content="{URL}" />
<meta property="og:description" content="{Description}" />
<meta property="og:image" content="{Image URL}" />
<meta property="og:type" content="article" />
<meta property="og:site_name" content="Online Khabar" />
<meta property="fb:pages" content="108349739223556" />
<meta property="fb:app_id" content="366639890155270" />
```

### 12.3 Twitter Card
```html
<meta property="twitter:card" content="summary_large_image" />
<meta property="twitter:site" content="@online_khabar" />
<meta property="twitter:title" content="{Title}" />
<meta property="twitter:description" content="{Description}" />
<meta property="twitter:image" content="{Image URL}" />
```

### 12.4 RSS/Feed
- Main feed: `/feed`
- Comments feed: `/comments/feed`
- oEmbed JSON: `/wp-json/oembed/1.0/embed?url={URL}`
- oEmbed XML: `/wp-json/oembed/1.0/embed?url={URL}&format=xml`

### 12.5 Favicon / App Icons
- 32×32: `/wp-content/uploads/2017/05/logo-mobile1-50x50.png`
- 192×192: `/wp-content/uploads/2017/05/logo-mobile1.png`
- Apple Touch Icon: same 192×192
- MS Tile Image: same 192×192

---

## 13. USER SYSTEM

### 13.1 Authentication Methods (Confirmed — Live Site Login Modal)
- **Email/Password** registration (mandatory for commenting)
- **Google OAuth** — `/?verify_google_login` callback
- **Facebook OAuth** — `fb:app_id: 366639890155270`
- **Twitter/X OAuth** — `@online_khabar`
- Users can set a **display name (nickname)** separate from real name
- Comments require registration; social login is an alternative

### 13.2 User Features
- User profile page
- Comment posting (requires login)
- Comment like/dislike
- Bookmark/save articles (likely)
- User dashboard

### 13.3 User Profile Page
- CSS: `_user-profile.css`
- Avatar, bio, articles authored
- Activity history

---

## 14. CONTENT TYPES / DATA MODELS

### 14.1 Article (Post)
| Field | Type | Description |
|---|---|---|
| id | Integer | Unique article ID (e.g., 1909741) |
| title | String | Article title (Nepali/English) |
| slug | String | URL-friendly slug |
| content | HTML | Full article body |
| excerpt | String | Short summary |
| ai_summary | JSON | AI-generated bullet points |
| featured_image | URL | Main thumbnail |
| category | FK | Primary category |
| tags | M2M | Multiple tags |
| author | FK | Author reference |
| published_at | DateTime | Publication timestamp |
| updated_at | DateTime | Last update |
| status | Enum | draft/published/archived |
| views_count | Integer | View counter |
| comments_count | Integer | Comment counter |
| is_featured | Boolean | Homepage featured |
| is_breaking | Boolean | Breaking news flag |
| seo_title | String | Custom SEO title |
| seo_description | String | Custom meta description |
| og_image | URL | Custom OG image |

### 14.2 Category
| Field | Type | Description |
|---|---|---|
| id | Integer | Unique ID |
| name | String | Category name (Nepali) |
| name_en | String | Category name (English) |
| slug | String | URL slug |
| parent_id | FK | Parent category (nullable) |
| description | String | Category description |
| color | String | Theme color for tags |
| sort_order | Integer | Display order |
| is_active | Boolean | Active/inactive |
| icon | String | Icon class or URL |

### 14.3 Author
| Field | Type | Description |
|---|---|---|
| id | Integer | Unique ID |
| name | String | Display name |
| email | String | Email |
| avatar | URL | Profile image |
| bio | Text | Short biography |
| role | Enum | editor/reporter/columnist |
| social_links | JSON | FB, Twitter, etc. |
| is_active | Boolean | Active writer |

### 14.4 Comment
| Field | Type | Description |
|---|---|---|
| id | Integer | Unique ID |
| article_id | FK | Related article |
| user_id | FK | Commenting user |
| parent_id | FK | Reply-to comment (nullable) |
| content | Text | Comment body |
| likes | Integer | Like count |
| dislikes | Integer | Dislike count |
| status | Enum | pending/approved/spam |
| created_at | DateTime | Posted time |

### 14.5 Advertisement
| Field | Type | Description |
|---|---|---|
| id | Integer | Unique ID |
| position_alias | String | e.g., "home-after-breaking1" |
| device_type | Enum | desktop/mobile/all |
| content_type | Enum | image/html/script |
| content | Text | Ad HTML/image URL |
| target_url | URL | Click destination |
| impressions | Integer | View counter |
| clicks | Integer | Click counter |
| start_date | DateTime | Campaign start |
| end_date | DateTime | Campaign end |
| is_active | Boolean | Currently active |
| priority | Integer | Display priority |

### 14.6 Tag
| Field | Type | Description |
|---|---|---|
| id | Integer | Unique ID |
| name | String | Tag name |
| slug | String | URL slug |

### 14.7 Live Score (Sports)
| Field | Type | Description |
|---|---|---|
| id | Integer | Unique ID |
| tournament | FK | Tournament reference |
| team_home | String | Home team name |
| team_away | String | Away team name |
| team_home_logo | URL | Logo image |
| team_away_logo | URL | Logo image |
| score_home | Integer | Home score |
| score_away | Integer | Away score |
| match_date | DateTime | Match date/time |
| status | Enum | upcoming/live/completed |
| result | String | WON/LOST/DRAW |

### 14.8 OK Reel
| Field | Type | Description |
|---|---|---|
| id | Integer | Unique ID |
| title | String | Reel title |
| slug | String | URL slug |
| thumbnail | URL | Preview image |
| video_url | URL | Video source |
| duration | Integer | Video length (seconds) |
| views | Integer | View count |
| published_at | DateTime | Publication date |
| is_active | Boolean | Currently shown |

### 14.9 Web Story
| Field | Type | Description |
|---|---|---|
| id | Integer | Unique ID |
| title | String | Story title |
| slides | JSON | Array of slide objects |
| thumbnail | URL | Cover image |
| published_at | DateTime | Publication date |

### 14.10 Photo Gallery
| Field | Type | Description |
|---|---|---|
| id | Integer | Unique ID |
| title | String | Gallery title |
| description | Text | Description |
| images | JSON | Array of image objects |
| category | FK | Category |
| published_at | DateTime | Date |

---

## 15. COLOR SCHEME

| Element | Color | Hex |
|---|---|---|
| Primary Red (tags, accents) | Red | `#e50000` / `#d32f2f` |
| Primary Brand | Dark Red | `#c62828` |
| Text Primary | Dark | `#1a1a1a` / `#333` |
| Text Secondary | Gray | `#666` / `#888` |
| Background | White | `#ffffff` |
| Background Alt | Light Gray | `#f5f5f5` / `#eee` |
| Links | Dark Blue | `#1a0dab` |
| Nav Background | White/Dark | varies |
| Section Headers | Bold Black | `#000` |
| Tag Badge (Red) | Category Red | `#e53935` |
| Footer Background | Dark | `#1a1a1a` / `#222` |
| Footer Text | Light | `#ccc` / `#fff` |

---

## 16. TYPOGRAPHY

| Element | Font | Size | Weight |
|---|---|---|---|
| Body text | Google Fonts (Nepali) | 16px | 400 |
| H1 (Article title) | Same | 28-32px | 700 |
| H2 (Section title) | Same | 22-26px | 700 |
| H2 (Card title) | Same | 16-18px | 600 |
| Meta text (time, author) | Same | 12-13px | 400 |
| Navigation | Same | 14-15px | 500 |
| Tag badge | Same | 11-12px | 600 |
| Excerpt | Same | 14-15px | 400 |

---

## 17. API ENDPOINTS IDENTIFIED

| Method | Endpoint | Purpose |
|---|---|---|
| GET | `/wp-json/okapi/v2/post-views-count` | Track/get article views |
| POST | `/wp-admin/admin-ajax.php` | AJAX actions (like, dislike, load more) |
| GET | `/wp-json/wp/v2/pages/{id}` | Get page data |
| GET | `/wp-json/oembed/1.0/embed` | oEmbed data |
| GET | `/feed` | RSS feed |
| GET | `/?verify_google_login` | Google OAuth callback |
| GET | `/wp-json/okapi/v1/*` | Ad API (via okam plugin) |

---

## 18. INTERACTIVE FEATURES

### 18.1 Comment Like/Dislike System
- Plugin: `ok-comments-like-dislike`
- AJAX-based voting
- Nonce security: `cld_js_object.admin_ajax_nonce`
- Font Awesome icons for thumbs up/down

### 18.2 View Counter
- Custom analytics plugin
- Tracks via `okapi/v2/post-views-count`
- Separate analytics server: `analytics.onlinekhabar.com`

### 18.3 Search
- WordPress native search
- Possibly custom search with autocomplete

### 18.4 Infinite Scroll / Load More
- AJAX-powered on category pages
- Uses Underscore.js templates (Mustache-style `{{}}`)

### 18.5 Carousel/Sliders
- Owl Carousel for:
  - OK Reels horizontal scroll
  - Sports live score slider
  - Photo galleries
  - Web stories

### 18.6 Breaking News Ticker
- Auto-scrolling horizontal text
- Links to latest breaking articles

---

## 19. PROPOSED POSTGRESQL SCHEMA

```sql
-- Core content tables
CREATE TABLE categories (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    name_en VARCHAR(255),
    slug VARCHAR(255) UNIQUE NOT NULL,
    parent_id INTEGER REFERENCES categories(id),
    description TEXT,
    color VARCHAR(7) DEFAULT '#e53935',
    icon VARCHAR(255),
    sort_order INTEGER DEFAULT 0,
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW()
);

CREATE TABLE authors (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE,
    avatar_url TEXT,
    bio TEXT,
    role VARCHAR(50) DEFAULT 'reporter',
    social_links JSONB DEFAULT '{}',
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMPTZ DEFAULT NOW()
);

CREATE TABLE tags (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL
);

CREATE TABLE articles (
    id SERIAL PRIMARY KEY,
    title VARCHAR(500) NOT NULL,
    slug VARCHAR(500) UNIQUE NOT NULL,
    content TEXT,
    excerpt TEXT,
    ai_summary JSONB,
    featured_image TEXT,
    category_id INTEGER REFERENCES categories(id),
    author_id INTEGER REFERENCES authors(id),
    status VARCHAR(20) DEFAULT 'draft',
    is_featured BOOLEAN DEFAULT false,
    is_breaking BOOLEAN DEFAULT false,
    views_count INTEGER DEFAULT 0,
    comments_count INTEGER DEFAULT 0,
    seo_title VARCHAR(500),
    seo_description TEXT,
    og_image TEXT,
    published_at TIMESTAMPTZ,
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW()
);

CREATE TABLE article_tags (
    article_id INTEGER REFERENCES articles(id) ON DELETE CASCADE,
    tag_id INTEGER REFERENCES tags(id) ON DELETE CASCADE,
    PRIMARY KEY (article_id, tag_id)
);

-- User system
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255),
    avatar_url TEXT,
    role VARCHAR(50) DEFAULT 'reader',
    provider VARCHAR(50) DEFAULT 'email',
    provider_id VARCHAR(255),
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW()
);

CREATE TABLE comments (
    id SERIAL PRIMARY KEY,
    article_id INTEGER REFERENCES articles(id) ON DELETE CASCADE,
    user_id INTEGER REFERENCES users(id),
    parent_id INTEGER REFERENCES comments(id),
    content TEXT NOT NULL,
    likes INTEGER DEFAULT 0,
    dislikes INTEGER DEFAULT 0,
    status VARCHAR(20) DEFAULT 'pending',
    created_at TIMESTAMPTZ DEFAULT NOW()
);

-- Advertisement system
CREATE TABLE ad_positions (
    id SERIAL PRIMARY KEY,
    alias VARCHAR(100) UNIQUE NOT NULL,
    description VARCHAR(255),
    page VARCHAR(50) DEFAULT 'home',
    device_type VARCHAR(20) DEFAULT 'all'
);

CREATE TABLE advertisements (
    id SERIAL PRIMARY KEY,
    position_id INTEGER REFERENCES ad_positions(id),
    title VARCHAR(255),
    content_type VARCHAR(20) DEFAULT 'image',
    content TEXT NOT NULL,
    target_url TEXT,
    impressions INTEGER DEFAULT 0,
    clicks INTEGER DEFAULT 0,
    start_date TIMESTAMPTZ,
    end_date TIMESTAMPTZ,
    is_active BOOLEAN DEFAULT true,
    priority INTEGER DEFAULT 0,
    created_at TIMESTAMPTZ DEFAULT NOW()
);

-- Sports
CREATE TABLE tournaments (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE,
    sport VARCHAR(50) DEFAULT 'football',
    season VARCHAR(20),
    is_active BOOLEAN DEFAULT true
);

CREATE TABLE matches (
    id SERIAL PRIMARY KEY,
    tournament_id INTEGER REFERENCES tournaments(id),
    team_home VARCHAR(255) NOT NULL,
    team_away VARCHAR(255) NOT NULL,
    team_home_logo TEXT,
    team_away_logo TEXT,
    score_home INTEGER,
    score_away INTEGER,
    match_date TIMESTAMPTZ,
    status VARCHAR(20) DEFAULT 'upcoming',
    venue VARCHAR(255),
    created_at TIMESTAMPTZ DEFAULT NOW()
);

-- OK Reels
CREATE TABLE reels (
    id SERIAL PRIMARY KEY,
    title VARCHAR(500) NOT NULL,
    slug VARCHAR(500) UNIQUE NOT NULL,
    thumbnail TEXT,
    video_url TEXT NOT NULL,
    duration INTEGER,
    views INTEGER DEFAULT 0,
    is_active BOOLEAN DEFAULT true,
    published_at TIMESTAMPTZ,
    created_at TIMESTAMPTZ DEFAULT NOW()
);

-- Photo Galleries
CREATE TABLE galleries (
    id SERIAL PRIMARY KEY,
    title VARCHAR(500) NOT NULL,
    slug VARCHAR(500) UNIQUE NOT NULL,
    description TEXT,
    category_id INTEGER REFERENCES categories(id),
    images JSONB DEFAULT '[]',
    published_at TIMESTAMPTZ,
    created_at TIMESTAMPTZ DEFAULT NOW()
);

-- Web Stories
CREATE TABLE web_stories (
    id SERIAL PRIMARY KEY,
    title VARCHAR(500) NOT NULL,
    slug VARCHAR(500) UNIQUE NOT NULL,
    thumbnail TEXT,
    slides JSONB DEFAULT '[]',
    is_active BOOLEAN DEFAULT true,
    published_at TIMESTAMPTZ,
    created_at TIMESTAMPTZ DEFAULT NOW()
);

-- Analytics
CREATE TABLE page_views (
    id SERIAL PRIMARY KEY,
    article_id INTEGER REFERENCES articles(id),
    user_agent TEXT,
    ip_address INET,
    referrer TEXT,
    created_at TIMESTAMPTZ DEFAULT NOW()
);

-- Bookmarks
CREATE TABLE bookmarks (
    user_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
    article_id INTEGER REFERENCES articles(id) ON DELETE CASCADE,
    created_at TIMESTAMPTZ DEFAULT NOW(),
    PRIMARY KEY (user_id, article_id)
);

-- Breaking News
CREATE TABLE breaking_news (
    id SERIAL PRIMARY KEY,
    title VARCHAR(500) NOT NULL,
    article_id INTEGER REFERENCES articles(id),
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMPTZ DEFAULT NOW(),
    expires_at TIMESTAMPTZ
);

-- Site Settings
CREATE TABLE site_settings (
    key VARCHAR(100) PRIMARY KEY,
    value JSONB NOT NULL,
    updated_at TIMESTAMPTZ DEFAULT NOW()
);

-- Indexes
CREATE INDEX idx_articles_category ON articles(category_id);
CREATE INDEX idx_articles_author ON articles(author_id);
CREATE INDEX idx_articles_status ON articles(status);
CREATE INDEX idx_articles_published ON articles(published_at DESC);
CREATE INDEX idx_articles_featured ON articles(is_featured) WHERE is_featured = true;
CREATE INDEX idx_articles_slug ON articles(slug);
CREATE INDEX idx_comments_article ON comments(article_id);
CREATE INDEX idx_page_views_article ON page_views(article_id);
CREATE INDEX idx_categories_slug ON categories(slug);

-- Blog posts (community/editorial blog, separate from news articles)
CREATE TABLE blogs (
    id SERIAL PRIMARY KEY,
    title VARCHAR(500) NOT NULL,
    slug VARCHAR(500) UNIQUE NOT NULL,
    content TEXT,
    excerpt TEXT,
    featured_image TEXT,
    author_id INTEGER REFERENCES authors(id),
    category VARCHAR(50) DEFAULT 'general', -- general, business, lifestyle
    status VARCHAR(20) DEFAULT 'draft',
    views_count INTEGER DEFAULT 0,
    published_at TIMESTAMPTZ,
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW()
);

-- NextAuth sessions (for JWT/session management)
CREATE TABLE sessions (
    id TEXT PRIMARY KEY,
    user_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
    expires TIMESTAMPTZ NOT NULL,
    session_token TEXT UNIQUE NOT NULL,
    created_at TIMESTAMPTZ DEFAULT NOW()
);

-- OAuth provider accounts per user (Google, Facebook, Twitter)
CREATE TABLE user_oauth_accounts (
    id SERIAL PRIMARY KEY,
    user_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
    provider VARCHAR(50) NOT NULL,      -- 'google' | 'facebook' | 'twitter'
    provider_account_id VARCHAR(255) NOT NULL,
    access_token TEXT,
    refresh_token TEXT,
    expires_at INTEGER,
    created_at TIMESTAMPTZ DEFAULT NOW(),
    UNIQUE(provider, provider_account_id)
);

-- Comment vote tracking (prevent double voting)
CREATE TABLE comment_votes (
    user_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
    comment_id INTEGER REFERENCES comments(id) ON DELETE CASCADE,
    vote_type VARCHAR(10) NOT NULL, -- 'like' | 'dislike'
    created_at TIMESTAMPTZ DEFAULT NOW(),
    PRIMARY KEY (user_id, comment_id)
);

-- Article like/dislike (article-level voting)
CREATE TABLE article_votes (
    user_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
    article_id INTEGER REFERENCES articles(id) ON DELETE CASCADE,
    vote_type VARCHAR(10) NOT NULL, -- 'like' | 'dislike'
    created_at TIMESTAMPTZ DEFAULT NOW(),
    PRIMARY KEY (user_id, article_id)
);

-- Media files (uploaded images, videos)
CREATE TABLE media_files (
    id SERIAL PRIMARY KEY,
    filename VARCHAR(500) NOT NULL,
    original_name VARCHAR(500),
    mime_type VARCHAR(100),
    file_size INTEGER,
    width INTEGER,
    height INTEGER,
    url TEXT NOT NULL,
    thumbnail_url TEXT,
    medium_url TEXT,
    large_url TEXT,
    alt_text TEXT,
    uploaded_by INTEGER REFERENCES users(id),
    created_at TIMESTAMPTZ DEFAULT NOW()
);

-- Election/Live widget data (for interactive election seat counter)
CREATE TABLE live_widgets (
    id SERIAL PRIMARY KEY,
    widget_type VARCHAR(50) NOT NULL,  -- 'election_counter' | 'live_poll' etc.
    title VARCHAR(255),
    data JSONB NOT NULL DEFAULT '{}',  -- JSON data for widget
    is_active BOOLEAN DEFAULT true,
    display_on VARCHAR(50) DEFAULT 'cover_story', -- which section to show
    updated_at TIMESTAMPTZ DEFAULT NOW(),
    created_at TIMESTAMPTZ DEFAULT NOW()
);

-- Trending articles cache (updated every 15 min)
CREATE TABLE trending_cache (
    id SERIAL PRIMARY KEY,
    article_id INTEGER REFERENCES articles(id) ON DELETE CASCADE,
    views_24h INTEGER DEFAULT 0,
    trending_score NUMERIC DEFAULT 0,
    rank INTEGER,
    calculated_at TIMESTAMPTZ DEFAULT NOW()
);
CREATE UNIQUE INDEX idx_trending_article ON trending_cache(article_id);
```

---

## 20. PROPOSED NEXT.JS PROJECT STRUCTURE

```
news-portal/
├── prisma/
│   └── schema.prisma          # Prisma ORM schema (maps to PostgreSQL)
│
├── src/
│   ├── app/                    # Next.js App Router
│   │   ├── layout.tsx          # Root layout (header, footer)
│   │   ├── page.tsx            # Homepage
│   │   ├── loading.tsx         # Loading skeleton
│   │   ├── error.tsx           # Error boundary
│   │   ├── not-found.tsx       # 404 page
│   │   │
│   │   ├── (public)/           # Public routes group
│   │   │   ├── content/
│   │   │   │   └── [category]/
│   │   │   │       └── page.tsx    # Category archive
│   │   │   ├── sports/
│   │   │   │   └── page.tsx        # Sports hub
│   │   │   ├── [year]/[month]/[id]/[slug]/
│   │   │   │   └── page.tsx        # Article page
│   │   │   ├── author/[id]/
│   │   │   │   └── page.tsx        # Author profile
│   │   │   ├── tag/[slug]/
│   │   │   │   └── page.tsx        # Tag archive
│   │   │   ├── ok-reels/
│   │   │   │   ├── page.tsx        # Reels listing
│   │   │   │   └── [slug]/page.tsx # Single reel
│   │   │   ├── gallery/
│   │   │   │   └── [slug]/page.tsx # Photo gallery
│   │   │   ├── interactive-html/
│   │   │   │   └── page.tsx        # Interactive stories
│   │   │   └── search/
│   │   │       └── page.tsx        # Search results
│   │   │
│   │   ├── auth/               # Auth routes
│   │   │   ├── login/page.tsx
│   │   │   ├── register/page.tsx
│   │   │   └── callback/page.tsx   # OAuth callback
│   │   │
│   │   ├── admin/              # Admin Panel
│   │   │   ├── layout.tsx          # Admin layout (sidebar)
│   │   │   ├── page.tsx            # Dashboard
│   │   │   ├── articles/
│   │   │   │   ├── page.tsx        # Article list
│   │   │   │   ├── new/page.tsx    # Create article
│   │   │   │   └── [id]/edit/page.tsx  # Edit article
│   │   │   ├── categories/
│   │   │   │   └── page.tsx        # Manage categories
│   │   │   ├── authors/
│   │   │   │   └── page.tsx        # Manage authors
│   │   │   ├── comments/
│   │   │   │   └── page.tsx        # Moderate comments
│   │   │   ├── ads/
│   │   │   │   └── page.tsx        # Ad management
│   │   │   ├── sports/
│   │   │   │   └── page.tsx        # Sports/scores mgmt
│   │   │   ├── reels/
│   │   │   │   └── page.tsx        # Reels management
│   │   │   ├── galleries/
│   │   │   │   └── page.tsx        # Gallery management
│   │   │   ├── analytics/
│   │   │   │   └── page.tsx        # Analytics dashboard
│   │   │   ├── settings/
│   │   │   │   └── page.tsx        # Site settings
│   │   │   ├── users/
│   │   │   │   └── page.tsx        # User management
│   │   │   └── breaking-news/
│   │   │       └── page.tsx        # Breaking news mgmt
│   │   │
│   │   └── api/                # API Routes
│   │       ├── articles/
│   │       │   ├── route.ts        # CRUD articles
│   │       │   └── [id]/route.ts
│   │       ├── categories/route.ts
│   │       ├── comments/
│   │       │   ├── route.ts
│   │       │   └── [id]/vote/route.ts  # Like/dislike
│   │       ├── auth/
│   │       │   ├── login/route.ts
│   │       │   ├── register/route.ts
│   │       │   └── google/route.ts
│   │       ├── views/route.ts      # Track page views
│   │       ├── search/route.ts     # Search API
│   │       ├── ads/route.ts        # Ad serving
│   │       ├── sports/route.ts     # Sports data
│   │       ├── reels/route.ts
│   │       ├── feed/route.ts       # RSS feed
│   │       └── upload/route.ts     # Media upload
│   │
│   ├── components/
│   │   ├── layout/
│   │   │   ├── Header.tsx          # Top bar + nav
│   │   │   ├── TopBar.tsx          # Ticker + date
│   │   │   ├── Navigation.tsx      # Main nav
│   │   │   ├── MegaMenu.tsx        # Dropdown menus
│   │   │   ├── MobileNav.tsx       # Hamburger menu
│   │   │   ├── Footer.tsx          # Site footer
│   │   │   ├── Sidebar.tsx         # Right sidebar
│   │   │   └── SearchBar.tsx       # Search component
│   │   │
│   │   ├── home/
│   │   │   ├── HeroSection.tsx     # Featured articles
│   │   │   ├── BreakingTicker.tsx  # Breaking news bar
│   │   │   ├── NewsSection.tsx     # समाचार grid
│   │   │   ├── FeatureSection.tsx  # फिचर section
│   │   │   ├── CoverStory.tsx      # कभर स्टोरी
│   │   │   ├── WeekendSection.tsx  # सप्ताहान्त
│   │   │   ├── TechSection.tsx     # Technology
│   │   │   ├── InterviewSection.tsx # Interview
│   │   │   ├── SportsSection.tsx   # Sports preview
│   │   │   ├── InternationalSection.tsx
│   │   │   ├── VideoSection.tsx    # Video section
│   │   │   ├── BizarreSection.tsx  # विचित्र संसार
│   │   │   ├── DiasporaSection.tsx # पौरखी प्रवासी
│   │   │   ├── GallerySection.tsx  # Photo gallery
│   │   │   ├── ReelsSection.tsx    # OK Reels scroll
│   │   │   └── MissedSection.tsx   # छुटाउनुभयो
│   │   │
│   │   ├── article/
│   │   │   ├── ArticleCard.tsx     # News card component
│   │   │   ├── ArticleCardLTR.tsx  # Left-to-right card
│   │   │   ├── FeaturedCard.tsx    # Large featured card
│   │   │   ├── SpotNewsCard.tsx    # Lead article card
│   │   │   ├── ArticleBody.tsx     # Rich content renderer
│   │   │   ├── AISummary.tsx       # AI summary box
│   │   │   ├── AuthorBlock.tsx     # Author info
│   │   │   ├── SocialShare.tsx     # Share buttons
│   │   │   ├── RelatedArticles.tsx # Related posts
│   │   │   └── ArticleMeta.tsx     # Time, views, comments
│   │   │
│   │   ├── comments/
│   │   │   ├── CommentSection.tsx  # Full comment area
│   │   │   ├── CommentForm.tsx     # Add comment
│   │   │   ├── CommentItem.tsx     # Single comment
│   │   │   └── VoteButtons.tsx     # Like/dislike
│   │   │
│   │   ├── sports/
│   │   │   ├── LiveScoreCard.tsx   # Match score card
│   │   │   ├── ScoreCarousel.tsx   # Carousel of scores
│   │   │   ├── LeagueTable.tsx     # Standings table
│   │   │   └── TournamentTabs.tsx  # Tab navigation
│   │   │
│   │   ├── media/
│   │   │   ├── ReelPlayer.tsx      # Reel video player
│   │   │   ├── PhotoGallery.tsx    # Gallery viewer
│   │   │   └── VideoEmbed.tsx      # Video embed
│   │   │
│   │   ├── ads/
│   │   │   ├── AdSlot.tsx          # Generic ad container
│   │   │   ├── BannerAd.tsx        # Full-width banner
│   │   │   └── SidebarAd.tsx       # Sidebar rectangle
│   │   │
│   │   ├── ui/                     # Shared UI primitives
│   │   │   ├── Button.tsx
│   │   │   ├── Badge.tsx           # Category tag badge
│   │   │   ├── Skeleton.tsx        # Loading skeletons
│   │   │   ├── Pagination.tsx
│   │   │   ├── Breadcrumb.tsx
│   │   │   ├── Tabs.tsx
│   │   │   ├── Carousel.tsx
│   │   │   ├── Modal.tsx
│   │   │   └── Toast.tsx
│   │   │
│   │   └── admin/
│   │       ├── AdminSidebar.tsx    # Admin navigation
│   │       ├── DashboardStats.tsx  # Stats cards
│   │       ├── ArticleEditor.tsx   # Rich text editor
│   │       ├── DataTable.tsx       # CRUD table
│   │       ├── MediaUploader.tsx   # File upload
│   │       └── Charts.tsx          # Analytics charts
│   │
│   ├── lib/
│   │   ├── db.ts                   # Prisma client
│   │   ├── auth.ts                 # Auth utilities
│   │   ├── api.ts                  # API helpers
│   │   ├── seo.ts                  # SEO metadata helpers
│   │   ├── date.ts                 # Nepali date utilities
│   │   └── upload.ts               # File upload helpers
│   │
│   ├── hooks/
│   │   ├── useAuth.ts
│   │   ├── useInfiniteScroll.ts
│   │   ├── useBreakpoint.ts
│   │   └── useViewCounter.ts
│   │
│   ├── types/
│   │   └── index.ts                # TypeScript interfaces
│   │
│   └── styles/
│       ├── globals.css             # Global styles + Tailwind
│       └── admin.css               # Admin-specific styles
│
├── public/
│   ├── images/
│   │   ├── logo.svg
│   │   ├── logo-mobile.png
│   │   ├── clock-icon.png
│   │   ├── comment-icon.png
│   │   ├── quote-white.png
│   │   └── ok-icon.png
│   └── fonts/
│
├── next.config.js
├── tailwind.config.js
├── postcss.config.js
├── tsconfig.json
├── package.json
├── .env.local                      # DB URL, OAuth keys, etc.
├── docker-compose.yml              # PostgreSQL + app
└── README.md
```

---

## 21. ADMIN PANEL FEATURES

### 21.1 Dashboard
- Total articles, views, comments today
- Charts: views over time, top articles
- Recent activity feed
- Quick actions: New Article, Breaking News

### 21.2 Article Management
- CRUD with rich text editor (TipTap/Editor.js)
- Category & tag assignment
- Featured image upload
- SEO fields (title, description, OG image)
- AI summary generation
- Draft/Publish/Schedule workflow
- Preview before publish
- Bulk actions (publish, archive, delete)

### 21.3 Category Management
- Create/edit/delete categories
- Nested categories (parent-child)
- Color assignment for tags
- Sort order management

### 21.4 Author Management
- Create/edit author profiles
- Assign roles (editor, reporter, columnist)
- View author article statistics

### 21.5 Comment Moderation
- Pending/approved/spam filters
- Bulk approve/reject
- View comment context

### 21.6 Ad Management
- Create ad positions (map to page locations)
- Upload ad creatives
- Set date ranges and priorities
- View impression/click analytics
- Device targeting (desktop/mobile)

### 21.7 Sports Management
- Add/edit tournaments
- Manage match scores (live update)
- Team logos upload
- League table configuration

### 21.8 Reels & Gallery Management
- Upload video reels
- Create photo galleries
- Manage web stories

### 21.9 Breaking News
- Quick publish breaking news ticker
- Set expiry time
- Link to full article

### 21.10 Analytics Dashboard
- Page views over time
- Top articles by views
- Traffic sources
- Device breakdown
- User engagement metrics

### 21.11 User Management
- View registered users
- Role management (admin, editor, reader)
- Ban/deactivate users

### 21.12 Site Settings
- Site name, tagline, contact info
- Social media links
- SEO defaults
- Homepage section ordering
- Footer content

---

## 22. KEY IMPLEMENTATION NOTES

### 22.1 Performance Optimization
- **ISR (Incremental Static Regeneration)** for article pages
- **SSR** for homepage (frequently updated)
- **Edge caching** with CDN headers
- **Image optimization** via Next.js `<Image>` component
- **Lazy loading** images below the fold
- **Code splitting** per route

### 22.2 Nepali Calendar Support
- Use `nepali-date-converter` or custom utility
- Display dates in Devanagari numerals (e.g., "२ घण्टा अगाडि")
- Relative time formatting in Nepali

### 22.3 Multilingual
- Primary: Nepali (`ne`)
- Secondary: English (`en`)
- Use Next.js i18n routing or middleware

### 22.4 RSS Feed
- Generate at `/feed` and `/comments/feed`
- Include: title, excerpt, image, author, date
- Content-Type: `application/rss+xml`

### 22.5 SEO
- Dynamic meta tags per page
- Structured data (JSON-LD) for articles
- Canonical URLs
- Sitemap generation
- robots.txt

---

*End of Analysis — Ready for Implementation*


---

## 23. REQUIREMENTS COMPLIANCE GAP ANALYSIS

> Cross-reference: `requirements.md` (30 requirements) vs scraped site data

| Req # | Title | Status | Notes |
|---|---|---|---|
| 1 | Article Management | ✅ Covered | Full CRUD, SEO fields, AI summary, scheduling |
| 2 | Category & Tag System | ✅ Covered | Nested categories, color tags, slugs |
| 3 | User Auth & Authorization | ✅ Updated | **Added Twitter OAuth** — 4 providers: email, Google, Facebook, Twitter |
| 4 | Comment System with Voting | ✅ Covered | **Added `comment_votes` table** to prevent double-voting |
| 5 | Responsive Design | ✅ Covered | 3 breakpoints + **mobile bottom nav bar** now documented |
| 6 | Multi-Language Support | ✅ Covered | Nepali primary, English secondary, Nepali calendar |
| 7 | Homepage Layout (18 sections) | ✅ Updated | **Added Blog section** to homepage layout |
| 8 | Article Display Page | ✅ Covered | AI summary, breadcrumb, social share, related articles |
| 9 | Navigation System | ✅ Updated | **Corrected nav order**: होमपेज→समाचार→बिजनेस→जीवनशैली→मनोरञ्जन→विचार→खेलकुद→अन्य |
| 10 | Search Functionality | ✅ Covered | Full-text, Nepali+English, API route |
| 11 | Sports Section | ✅ Covered | Live scores, league tabs, carousel |
| 12 | OK Reels Video | ✅ Covered | Horizontal scroll, slug-based pages |
| 13 | Photo Gallery | ✅ Covered | JSON-stored images, JSONB array |
| 14 | Advertisement Management | ✅ Covered | Position aliases, device targeting, click tracking |
| 15 | Analytics Dashboard | ✅ Covered | Page views table, admin charts |
| 16 | SEO Optimization | ✅ Covered | OG tags, Twitter cards, JSON-LD, sitemaps |
| 17 | Security Implementation | ⚠️ Updated | See Section 26 — bcrypt, CSRF, rate limits added |
| 18 | Performance Optimization | ⚠️ Updated | See Section 26 — Lighthouse targets, ISR 60s added |
| 19 | Admin Panel Dashboard | ✅ Covered | 13-module sidebar, media library, settings |
| 20 | Breaking News Management | ✅ Covered | `breaking_news` table, ticker component |
| 21 | Author Management | ✅ Covered | Author profiles, roles, social links |
| 22 | Category Archive Pages | ✅ Covered | Paginated archives, sidebar |
| 23 | User Profile & Bookmarks | ✅ Covered | `bookmarks` table, profile page route |
| 24 | Trending & Popular Content | ✅ Updated | **Added `trending_cache` table** with 15-min update |
| 25 | Error Handling & UX Feedback | ✅ Updated | See Section 27 — error boundaries, toast, skeleton added |
| 26 | Database Schema & Integrity | ✅ Updated | **Added 7 missing tables**: sessions, user_oauth_accounts, comment_votes, article_votes, media_files, live_widgets, trending_cache, blogs |
| 27 | Media Upload & Management | ✅ Updated | **Added `media_files` table**, media library in admin |
| 28 | Site Settings Management | ✅ Covered | `site_settings` key-value table, admin UI |
| 29 | Accessibility Compliance | ✅ Updated | See Section 28 — WCAG AA, ARIA, skip links added |
| 30 | API Design & Documentation | ✅ Updated | See Section 29 — versioned /api/v1/, REST conventions |

---

## 24. MOBILE BOTTOM NAVIGATION BAR

### 24.1 Component Details
- **Position:** `fixed; bottom: 0; left: 0; right: 0; z-index: 9999`
- **Visibility:** Mobile only (`<768px`)
- **Background:** White with top border shadow
- **Height:** ~56px

### 24.2 Tabs
| Tab | Icon | Action |
|---|---|---|
| ताजा अपडेट | 🔔 Bell | Opens latest news feed / sliding panel |
| ट्रेन्डिङ | 🔥 Fire | Opens trending articles list |
| प्रोफाइल | 👤 Person | Login/register or user profile |
| सर्च | 🔍 Magnifier | Opens full-screen search overlay |

### 24.3 Implementation
```tsx
// components/layout/MobileBottomNav.tsx
export function MobileBottomNav() {
  return (
    <nav className="fixed bottom-0 left-0 right-0 z-50 bg-white border-t
                    flex md:hidden h-14 items-center">
      <NavTab label="ताजा अपडेट" icon={<BellIcon />} href="/latest" />
      <NavTab label="ट्रेन्डिङ"   icon={<FireIcon />}  href="/trending" />
      <NavTab label="प्रोफाइल"   icon={<UserIcon />}  href="/profile" />
      <NavTab label="सर्च"       icon={<SearchIcon />} onClick={openSearch} />
    </nav>
  );
}
```

---

## 25. SECTION-BY-SECTION CONTENT TAXONOMY

### 25.1 Homepage Section Map
| Section | Data Source | ISR Revalidate | Component |
|---|---|---|---|
| Breaking Ticker | `breaking_news` table | 30s | `BreakingTicker.tsx` |
| Hero/Bises | `articles` WHERE `is_featured=true` | 60s | `HeroSection.tsx` |
| OK Reels | `reels` table | 300s | `ReelsSection.tsx` |
| समाचार Grid | `articles` category=news | 60s | `NewsSection.tsx` |
| फिचर | `articles` category=feature | 60s | `FeatureSection.tsx` |
| कभर स्टोरी | `articles` category=cover-story + `live_widgets` | 60s | `CoverStory.tsx` |
| सप्ताहान्त | `articles` category=weekend | 300s | `WeekendSection.tsx` |
| Technology | `articles` category=technology | 60s | `TechSection.tsx` |
| Interview | `articles` category=interview | 60s | `InterviewSection.tsx` |
| Sports | `matches` + `articles` category=sports | 30s | `SportsSection.tsx` |
| Literature | `articles` category=literature | 300s | `LiteratureSection.tsx` |
| ब्लग | `blogs` table | 300s | `BlogSection.tsx` |
| International | `articles` category=international | 60s | `InternationalSection.tsx` |
| Video | `articles` category=video | 60s | `VideoSection.tsx` |
| Bizarre World | `articles` category=bizarre | 300s | `BizarreSection.tsx` |
| Diaspora | `articles` category=diaspora | 300s | `DiasporaSection.tsx` |
| Photo Gallery | `galleries` table | 300s | `GallerySection.tsx` |
| Missed (छुटाउनुभयो) | `articles` recently published | 60s | `MissedSection.tsx` |

---

## 26. PERFORMANCE & SECURITY SPECIFICATIONS

### 26.1 Performance Targets (from requirements.md)
| Metric | Target |
|---|---|
| Lighthouse Performance (Desktop) | ≥ 90 |
| Lighthouse Performance (Mobile) | ≥ 80 |
| JS Bundle Size | < 200KB gzipped |
| API Response Time (articles) | < 200ms |
| Search Response Time | < 500ms |
| ISR Revalidation | 60 seconds for articles |
| Image Format | WebP with fallback JPEG |
| Image Lazy Loading | Below-the-fold images |

### 26.2 Performance Implementation
- **ISR** (`revalidate: 60`) on all article and homepage pages
- **SSG** for static pages (about, contact)
- **Next.js `<Image>`** with `sizes`, `priority` on hero images
- **`next/font`** for Google Fonts (self-hosted, no external request)
- **`next/dynamic`** for heavy components (Editor, Charts, Maps)
- **React Suspense + Skeleton** for async data sections
- **`prefetch: true`** on navigation links
- **CDN** for media assets (e.g., Cloudflare R2 or AWS S3 + CloudFront)

### 26.3 Security Specifications (from requirements.md)
| Security Control | Specification |
|---|---|
| Password Hashing | bcrypt, salt rounds ≥ 10 |
| Session Expiry | 30 days (sliding) |
| JWT Secret | Min 32 chars, from `AUTH_SECRET` env |
| CSRF Protection | Next.js server actions + CSRF tokens on forms |
| XSS Prevention | DOMPurify for user-rendered content |
| Rate Limiting (API) | 100 requests/minute per IP |
| Rate Limiting (Login) | 5 failed attempts per 15 minutes per IP |
| Content Security Policy | Strict CSP headers via `next.config.js` |
| SQL Injection | Prisma parameterized queries only |
| File Upload Security | MIME validation + max size (10MB images, 100MB video) |
| HTTPS | Enforce redirect from HTTP |

### 26.4 Security Implementation
```ts
// Rate limiting middleware (middleware.ts)
import { Ratelimit } from '@upstash/ratelimit'
// Login: 5 per 15 min
// API: 100 per 1 min

// bcrypt usage
import bcrypt from 'bcrypt'
const SALT_ROUNDS = 10
const hash = await bcrypt.hash(password, SALT_ROUNDS)

// CSP Headers (next.config.js)
headers: [{
  key: 'Content-Security-Policy',
  value: "default-src 'self'; img-src 'self' data: https:; ..."
}]
```

---

## 27. ERROR HANDLING & UX FEEDBACK SYSTEM

### 27.1 Error Pages
| Page | Route | Component |
|---|---|---|
| 404 Not Found | `not-found.tsx` | Custom with nav links back |
| 500 Server Error | `error.tsx` + `global-error.tsx` | Friendly message + retry |
| Offline | Service Worker / `navigator.onLine` | Offline banner indicator |

### 27.2 Loading States
- **Skeleton Loaders** — `<Skeleton>` component for every content section
- All sections use `<Suspense fallback={<SectionSkeleton />}>` 
- Article pages show skeleton for body, sidebar, comments independently

### 27.3 Toast Notifications
```
Success: ✅ "कमेन्ट सफलतापूर्वक थपियो"
Error:   ❌ "कमेन्ट थप्न सकिएन, पुनः प्रयास गर्नुहोस्"
Info:    ℹ️ "लगइन आवश्यक छ"
```
- Position: top-right on desktop, top-center on mobile
- Auto-dismiss after 4 seconds
- Dismissible by click

### 27.4 Form Validation
- Inline error messages below each field
- Real-time validation on blur
- Submit button disabled until valid
- Nepali language error messages

### 27.5 Network/Offline Handling
- Detect `navigator.onLine` + `online/offline` events
- Show persistent banner: "इन्टरनेट जडान छैन"
- Retry button for failed API calls
- Optimistic updates for like/dislike with rollback on failure

---

## 28. ACCESSIBILITY COMPLIANCE (WCAG AA)

### 28.1 Requirements Summary
| Requirement | Implementation |
|---|---|
| Semantic HTML5 | `<article>`, `<nav>`, `<main>`, `<aside>`, `<header>`, `<footer>` |
| Alt text | All `<Image>` components require `alt` prop |
| Keyboard navigation | Tab order, Enter/Space for interactive elements |
| Focus indicators | Custom CSS: `outline: 2px solid #e53935; outline-offset: 2px` |
| Color contrast | Body text (#333 on #fff): 12.6:1 ✅; Tag badges: verify per color |
| ARIA labels | Icon buttons get `aria-label`, icon-only elements get `aria-hidden` |
| ARIA live regions | Breaking news ticker: `aria-live="polite"` |
| Skip links | `<a href="#main-content" className="sr-only focus:not-sr-only">` |
| Form labels | All inputs use `<label htmlFor>` or `aria-label` |
| Heading hierarchy | H1 (page title) → H2 (section) → H3 (card title) |
| Zoom support | Layout tested at 200% browser zoom, no horizontal overflow |
| Screen readers | Tested with NVDA/VoiceOver |

### 28.2 ARIA Patterns
```tsx
// Breaking news ticker
<div role="region" aria-label="ताजा खबर" aria-live="polite">
  <marquee>{text}</marquee>  // replaced with CSS animation
</div>

// Navigation
<nav aria-label="मुख्य नेभिगेसन">
  <ul role="menubar">...</ul>
</nav>

// Article card
<article aria-label={title}>
  <h2><a href={url}>{title}</a></h2>
  <time dateTime={isoDate}>{nepaliDate}</time>
</article>
```

---

## 29. API DESIGN SPECIFICATION

### 29.1 Versioned API Routes
All API routes use `/api/v1/` prefix:
| Method | Route | Auth | Description |
|---|---|---|---|
| GET | `/api/v1/articles` | Public | List articles (paginated, filtered) |
| GET | `/api/v1/articles/[id]` | Public | Get single article |
| POST | `/api/v1/articles` | Admin | Create article |
| PATCH | `/api/v1/articles/[id]` | Admin | Update article |
| DELETE | `/api/v1/articles/[id]` | Admin | Delete article |
| GET | `/api/v1/categories` | Public | List categories |
| GET | `/api/v1/search?q=` | Public | Search articles |
| POST | `/api/v1/comments` | User | Post comment |
| POST | `/api/v1/comments/[id]/vote` | User | Like/dislike comment |
| GET | `/api/v1/trending` | Public | Trending articles |
| POST | `/api/v1/views` | Public | Track page view |
| POST | `/api/v1/auth/login` | Public | Email login |
| POST | `/api/v1/auth/register` | Public | Register |
| GET | `/api/v1/auth/[...nextauth]` | Public | NextAuth handler |
| GET | `/api/v1/bookmarks` | User | Get user bookmarks |
| POST | `/api/v1/bookmarks` | User | Add bookmark |
| DELETE | `/api/v1/bookmarks/[id]` | User | Remove bookmark |
| POST | `/api/v1/upload` | Admin | Upload media file |
| GET | `/api/v1/ads/[position]` | Public | Get ad for position |
| GET | `/api/v1/sports/matches` | Public | Get match scores |
| GET | `/api/v1/feed` | Public | RSS feed |

### 29.2 Standard Response Format
```ts
// Success
{ success: true, data: T, meta?: { total, page, limit } }

// Error
{ success: false, error: { code: string, message: string } }
```

### 29.3 Pagination Parameters
```
GET /api/v1/articles?limit=15&offset=0&category=news&status=published&sort=published_at&order=desc
```

### 29.4 HTTP Status Codes
| Code | Usage |
|---|---|
| 200 | Successful GET, PATCH |
| 201 | Successful POST (resource created) |
| 204 | Successful DELETE (no content) |
| 400 | Validation error |
| 401 | Not authenticated |
| 403 | Not authorized (wrong role) |
| 404 | Resource not found |
| 429 | Rate limit exceeded |
| 500 | Internal server error |

---

## 30. TRENDING ALGORITHM SPECIFICATION

### 30.1 Algorithm
```
trending_score = (views_last_24h * 1.0) + (comments_last_24h * 5.0) + (bookmarks_last_24h * 3.0)
```
- **Update frequency:** Every 15 minutes (cron job or background task)
- **Exclusion:** Articles older than 7 days
- **Result:** Top 10 stored in `trending_cache` table
- **Display:** Top 5 in sidebar, full top 10 in trending page

### 30.2 Implementation
```ts
// lib/trending.ts
export async function updateTrendingCache() {
  const cutoff24h = new Date(Date.now() - 24 * 60 * 60 * 1000);
  const cutoff7d = new Date(Date.now() - 7 * 24 * 60 * 60 * 1000);
  
  const trending = await prisma.$queryRaw`
    SELECT a.id, a.title, a.slug,
      (COUNT(DISTINCT pv.id) * 1.0 + COUNT(DISTINCT c.id) * 5.0) as score
    FROM articles a
    LEFT JOIN page_views pv ON pv.article_id = a.id AND pv.created_at > ${cutoff24h}
    LEFT JOIN comments c ON c.article_id = a.id AND c.created_at > ${cutoff24h}
    WHERE a.published_at > ${cutoff7d} AND a.status = 'published'
    GROUP BY a.id
    ORDER BY score DESC
    LIMIT 10
  `;
  
  // Upsert into trending_cache
}
```

### 30.3 Caching Strategy
- Trending cache served from Redis or Next.js data cache
- `unstable_cache` with 15-minute TTL
- Background revalidation on cache miss

---

*End of Analysis — Version 2.0 — Updated with Gap Analysis & Full Requirements Cross-Reference*


---

## 31. DARK/LIGHT THEME SYSTEM

### 31.1 Theme Architecture
- **CSS Custom Properties** approach — all colors defined as variables on `:root` and `[data-theme="dark"]`
- **Default:** Light mode (`data-theme="light"` on `<html>`)
- **OS Preference Detection:** `window.matchMedia('(prefers-color-scheme: dark)')` on first load
- **Persistence:** `localStorage.getItem('theme')` — checked before paint to avoid FOUC

### 31.2 CSS Variable Map
```css
:root {
  /* Light Theme (default) */
  --bg-primary:   #ffffff;
  --bg-secondary: #f5f5f5;
  --bg-surface:   #ffffff;
  --bg-card:      #ffffff;
  --text-primary: #1a1a1a;
  --text-secondary: #555555;
  --text-muted:   #888888;
  --border-color: #e0e0e0;
  --accent-red:   #e53935;
  --accent-hover: #c62828;
  --link-color:   #1a0dab;
  --nav-bg:       #ffffff;
  --footer-bg:    #1a1a1a;
  --footer-text:  #cccccc;
  --shadow:       0 2px 8px rgba(0,0,0,0.1);
}

[data-theme="dark"] {
  --bg-primary:   #0f0f0f;
  --bg-secondary: #1a1a1a;
  --bg-surface:   #1e1e1e;
  --bg-card:      #242424;
  --text-primary: #f0f0f0;
  --text-secondary: #bbbbbb;
  --text-muted:   #777777;
  --border-color: #333333;
  --accent-red:   #ef5350;
  --accent-hover: #e53935;
  --link-color:   #8ab4f8;
  --nav-bg:       #1a1a1a;
  --footer-bg:    #111111;
  --footer-text:  #aaaaaa;
  --shadow:       0 2px 8px rgba(0,0,0,0.4);
}
```

### 31.3 No-FOUC Script (inject in `<head>` before any CSS)
```html
<script>
  (function() {
    var saved = localStorage.getItem('theme');
    var prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    document.documentElement.setAttribute(
      'data-theme',
      saved || (prefersDark ? 'dark' : 'light')
    );
  })();
</script>
```

### 31.4 Theme Toggle Component
```tsx
// components/ui/ThemeToggle.tsx
'use client'
import { useTheme } from '@/hooks/useTheme'
export function ThemeToggle() {
  const { theme, toggle } = useTheme()
  return (
    <button
      onClick={toggle}
      aria-label={theme === 'dark' ? 'Switch to light mode' : 'Switch to dark mode'}
      className="theme-toggle-btn"
    >
      {theme === 'dark' ? <SunIcon /> : <MoonIcon />}
    </button>
  )
}
```

### 31.5 Database Field for Theme Preference
- Add `theme_preference VARCHAR(10) DEFAULT 'light'` to `users` table
- Sync to DB on login, load from DB on session restore

---

## 32. DYNAMIC SITE CONFIGURATION SYSTEM

### 32.1 Architecture
```
Admin Panel → site_settings table → API /api/v1/settings → SiteConfigContext → All Components
```

### 32.2 Configurable Settings Map
| Key | Type | Default | Used In |
|---|---|---|---|
| `site.name` | String | "अनलाइनखबर" | `<title>`, header, footer, SEO |
| `site.tagline` | String | "नेपालको नम्बर १ समाचार पोर्टल" | header subtitle, SEO |
| `site.logo_url` | String | `/images/logo.svg` | Header logo, footer logo |
| `site.favicon_url` | String | `/images/favicon.ico` | Browser tab |
| `site.brand_color` | HEX | `#e53935` | Tags, buttons, accents |
| `site.footer.org_name` | String | "Online Khabar Pvt. Ltd." | Footer |
| `site.footer.reg_no` | String | "२१४ / ०७३–७४" | Footer |
| `site.footer.phone` | String | "+977-1-4790176" | Footer, contact |
| `site.footer.email` | String | "news@example.com" | Footer, contact |
| `site.footer.address` | String | "काठमाडौं, नेपाल" | Footer |
| `site.footer.copyright` | String | "© २०२६" | Footer bottom |
| `site.social.facebook` | URL | "#" | Social links |
| `site.social.twitter` | URL | "#" | Social links |
| `site.social.youtube` | URL | "#" | Social links |
| `site.social.instagram` | URL | "#" | Social links |
| `site.social.tiktok` | URL | "#" | Social links |
| `site.homepage_sections` | JSON Array | Full list | Homepage section ordering/visibility |
| `site.nav_items` | JSON Array | Default nav | Main navigation |
| `site.features.comments` | Boolean | true | Enable/disable comments |
| `site.features.bookmarks` | Boolean | true | Enable/disable bookmarks |
| `site.features.reels` | Boolean | true | Enable/disable reels section |
| `site.features.dark_mode` | Boolean | true | Allow dark mode toggle |

### 32.3 React Context
```tsx
// contexts/SiteConfigContext.tsx
export const SiteConfigContext = createContext<SiteConfig>(defaultConfig)

export function SiteConfigProvider({ config, children }) {
  return (
    <SiteConfigContext.Provider value={config}>
      {children}
    </SiteConfigContext.Provider>
  )
}

export const useSiteConfig = () => useContext(SiteConfigContext)
```

### 32.4 Usage in Components
```tsx
// components/layout/Header.tsx
const { logo_url, name, brand_color } = useSiteConfig()
return <img src={logo_url} alt={name} />

// components/layout/Footer.tsx
const { footer, social, name } = useSiteConfig()
return <p>{footer.copyright} {name}</p>
```

---

## 33. i18n IMPLEMENTATION

### 33.1 Library: `next-intl`
- Package: `next-intl`
- Default locale: `ne` (Nepali)
- Secondary locale: `en` (English)
- URL strategy: Sub-path routing (`/ne/...` and `/en/...`) or cookie-based (no URL change)

### 33.2 Translation File Structure
```
messages/
  ne.json    ← Nepali (default)
  en.json    ← English
```

### 33.3 Key Nepali UI Strings (ne.json sample)
```json
{
  "nav": {
    "home": "होमपेज",
    "news": "समाचार",
    "business": "बिजनेस",
    "lifestyle": "जीवनशैली",
    "entertainment": "मनोरञ्जन",
    "opinion": "विचार",
    "sports": "खेलकुद",
    "other": "अन्य",
    "search": "सर्च गर्नुहोस्",
    "login": "लगइन",
    "register": "दर्ता"
  },
  "article": {
    "readTime": "{mins} मिनेट पढ्न समय",
    "views": "{count} पटक हेरियो",
    "comments": "{count} कमेन्ट",
    "share": "साझा गर्नुहोस्",
    "bookmark": "सुरक्षित गर्नुहोस्",
    "copyLink": "लिंक कपि गर्नुहोस्",
    "linkCopied": "लिंक कपि भयो!"
  },
  "auth": {
    "login": "लगइन गर्नुहोस्",
    "logout": "लगआउट",
    "register": "दर्ता गर्नुहोस्",
    "email": "इमेल",
    "password": "पासवर्ड",
    "forgotPassword": "पासवर्ड बिर्सनुभयो?",
    "loginWithGoogle": "गुगलबाट लगइन",
    "loginWithFacebook": "फेसबुकबाट लगइन",
    "loginWithTwitter": "ट्वीटरबाट लगइन"
  },
  "errors": {
    "notFound": "पृष्ठ फेला परेन",
    "serverError": "सर्भर त्रुटि भयो",
    "offline": "इन्टरनेट जडान छैन",
    "tryAgain": "पुनः प्रयास गर्नुहोस्"
  },
  "theme": {
    "light": "उज्यालो मोड",
    "dark": "अँध्यारो मोड"
  }
}
```

### 33.4 Nepali Font
- **Font:** `Noto Sans Devanagari` (Google Fonts)
- Load via `next/font/google`
- Apply to `<html lang="ne">` body

---

## 34. PLAYWRIGHT TEST SUITE

### 34.1 Test Categories
| Category | File | Tests |
|---|---|---|
| Homepage | `tests/homepage.spec.ts` | Sections render, nav works, breaking ticker |
| Article Page | `tests/article.spec.ts` | Content, AI summary, share, TOC, reading bar |
| Navigation | `tests/navigation.spec.ts` | All nav links, mega menu, mobile hamburger |
| Dark/Light Theme | `tests/theme.spec.ts` | Toggle applies, persists, no FOUC |
| Language Switch | `tests/i18n.spec.ts` | Nepali default, English switch, label change |
| Auth | `tests/auth.spec.ts` | Register, login, logout, forgot password |
| Comments | `tests/comments.spec.ts` | Post, like/dislike, requires login |
| Search | `tests/search.spec.ts` | Query, results, navigate to article |
| Category Archive | `tests/category.spec.ts` | Articles shown, pagination |
| Mobile Layout | `tests/mobile.spec.ts` | Hamburger, bottom nav, responsive |
| Admin Panel | `tests/admin.spec.ts` | Login, CRUD article, settings update |
| Bookmarks | `tests/bookmarks.spec.ts` | Add, view, remove bookmark |
| 404 / Error Pages | `tests/errors.spec.ts` | Custom 404, offline indicator |
| Dynamic Config | `tests/dynamic-config.spec.ts` | Logo/name change reflected |

### 34.2 Test Setup
```ts
// playwright.config.ts
export default defineConfig({
  testDir: './tests',
  use: {
    baseURL: 'http://localhost:3000',
    browserName: 'chromium',
    screenshot: 'only-on-failure',
    video: 'retain-on-failure',
  },
  reporter: [['html', { outputFolder: 'playwright-report' }]],
})
```

---

## 35. UPDATED DATABASE SCHEMA (ADDITIONS)

```sql
-- Add theme preference to users table
ALTER TABLE users ADD COLUMN theme_preference VARCHAR(10) DEFAULT 'light';
ALTER TABLE users ADD COLUMN lang_preference VARCHAR(5) DEFAULT 'ne';

-- Password reset tokens
CREATE TABLE password_reset_tokens (
    id SERIAL PRIMARY KEY,
    user_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
    token VARCHAR(255) UNIQUE NOT NULL,
    expires_at TIMESTAMPTZ NOT NULL,
    used BOOLEAN DEFAULT false,
    created_at TIMESTAMPTZ DEFAULT NOW()
);

-- Email verification tokens
CREATE TABLE email_verification_tokens (
    id SERIAL PRIMARY KEY,
    user_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
    token VARCHAR(255) UNIQUE NOT NULL,
    expires_at TIMESTAMPTZ NOT NULL,
    verified_at TIMESTAMPTZ,
    created_at TIMESTAMPTZ DEFAULT NOW()
);

-- Cookie consent tracking
CREATE TABLE cookie_consents (
    id SERIAL PRIMARY KEY,
    user_id INTEGER REFERENCES users(id) ON DELETE SET NULL,
    session_id VARCHAR(255),
    analytics_consent BOOLEAN DEFAULT false,
    advertising_consent BOOLEAN DEFAULT false,
    ip_address INET,
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW()
);

-- Navigation menu items (dynamic from admin)
CREATE TABLE nav_menu_items (
    id SERIAL PRIMARY KEY,
    label_ne VARCHAR(100) NOT NULL,
    label_en VARCHAR(100),
    url VARCHAR(255) NOT NULL,
    parent_id INTEGER REFERENCES nav_menu_items(id),
    sort_order INTEGER DEFAULT 0,
    is_active BOOLEAN DEFAULT true,
    has_megamenu BOOLEAN DEFAULT false,
    megamenu_category_id INTEGER REFERENCES categories(id)
);
```

---

## 36. COMPLETE REQUIREMENTS COMPLIANCE (v2 — 38 Requirements)

| Req | Title | Status |
|---|---|---|
| 1-30 | (See Section 23) | ✅ |
| 31 | Dark/Light Theme | ✅ Added — CSS vars, no-FOUC, toggle, persist |
| 32 | Nepali Default i18n | ✅ Added — `next-intl`, `ne.json`/`en.json`, Noto Sans Devanagari |
| 33 | Dynamic Site Config | ✅ Added — `site_settings` → API → React Context → all components |
| 34 | Playwright E2E Testing | ✅ Added — 14 test files, Chromium, HTML report |
| 35 | PWA / Offline Support | ✅ Added — manifest.json, service worker, offline cache |
| 36 | Enhanced Reading UX | ✅ Added — reading time, TOC, progress bar, back-to-top |
| 37 | Password Reset & Email Verify | ✅ Added — token tables, email flow, session restore |
| 38 | Cookie Consent / GDPR | ✅ Added — banner, consent table, privacy/TOS pages |

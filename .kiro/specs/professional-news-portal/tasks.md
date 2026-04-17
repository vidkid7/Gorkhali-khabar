# Implementation Plan: Professional News Portal

## Overview

This implementation plan covers the complete development of a professional news portal built with Next.js 14+, React 18+, TypeScript, PostgreSQL, and Prisma ORM. The system includes multi-language support (Nepali primary, English secondary), dynamic theming, comprehensive content management, user authentication, comment system, sports section with live scores, OK Reels video feature, photo galleries, advertisement management, analytics dashboard, and comprehensive security measures.

The implementation follows a logical progression from foundational setup through core features, advanced features, and finally testing and deployment. Each task builds incrementally to ensure a stable, working system at every checkpoint.

## Tasks

- [x] 1. Project Foundation and Core Setup
  - [x] 1.1 Initialize Next.js 14+ project with TypeScript and essential dependencies
    - Create Next.js project with App Router
    - Install TypeScript, Tailwind CSS, Prisma, NextAuth.js, Zod, bcryptjs, DOMPurify
    - Configure TypeScript strict mode and ESLint rules
    - Set up basic folder structure (app/, components/, lib/, types/, prisma/)
    - _Requirements: All system requirements depend on this foundation_

  - [x] 1.2 Set up PostgreSQL database and Prisma ORM
    - Configure PostgreSQL connection
    - Create comprehensive Prisma schema with all models (User, Author, Article, Category, Tag, Comment, etc.)
    - Set up database migrations and seed data
    - Configure Prisma Client with proper error handling
    - _Requirements: 26.1, 26.2, 26.3, 26.4, 26.5, 26.6, 26.7, 26.8, 26.9, 26.10, 26.11, 26.12_

  - [x] 1.3 Write property test for database schema integrity
    - **Property 5: Universal Slug Uniqueness**
    - **Validates: Requirements 1.10, 2.11**

  - [x] 1.4 Implement authentication system with NextAuth.js
    - Configure NextAuth.js with email/password and Google OAuth providers
    - Set up secure session management with HTTP-only cookies
    - Implement role-based access control (admin, editor, author, reader)
    - Create middleware for route protection
    - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5, 3.6, 3.7, 3.8, 3.9, 3.10, 3.11, 3.12_

  - [x] 1.5 Write property tests for authentication system
    - **Property 7: User Registration with Password Hashing**
    - **Validates: Requirements 3.1, 17.1**

  - [x] 1.6 Write property test for authentication session creation
    - **Property 8: Authentication Session Creation**
    - **Validates: Requirements 3.2**

  - [x] 1.7 Complete database schema with missing models and fix schema inconsistencies
    - Add `AuditLog` model (id, admin_id, action, entity, entity_id, old_value, new_value, ip_address, created_at) for Req 17.36
    - Add `PasswordResetToken` model (id, user_id, token_hash, expires_at, used, created_at) for Req 17.21, 37.2
    - Add `EmailVerificationToken` model (id, user_id, token_hash, expires_at, used, created_at) for Req 17.22, 37.5
    - Add `WebStory` model (id, title, slug, slides JSON, category_id, is_active, created_at) per glossary
    - Fix `Article` model: add `breaking_news BreakingNews[]` back-relation and `page_views PageView[]` back-relation
    - Fix `MediaFile` model: change `uploaded_by` to a proper `User` relation field; add `media_files MediaFile[]` to `User` model
    - Add database indexes on all frequently queried fields: `articles(category_id)`, `articles(author_id)`, `articles(published_at)`, `articles(status)`, `articles(slug)`, `comments(article_id)`, `page_views(article_id)`, `page_views(created_at)`
    - Ensure all TIMESTAMPTZ fields, NOT NULL constraints, and default values match Req 26
    - _Requirements: 26.1, 26.2, 26.3, 26.4, 26.5, 26.6, 26.7, 26.8, 26.9, 26.10, 26.11, 26.12, 17.36, 17.21, 17.22_

  - [x] 1.8 Implement email verification flow
    - Create `POST /api/auth/send-verification` endpoint to issue and email a time-limited token
    - Create `GET /api/auth/verify-email?token=` endpoint to verify and mark email as verified
    - Restrict unverified users from posting comments (check `email_verified` in comment API middleware)
    - Allow resending verification email from user profile page
    - Invalidate used/expired tokens; display Nepali-language success and error messages
    - _Requirements: 3.1, 17.22, 37.5, 37.6, 37.7, 32.7_

  - [x] 1.9 Implement password reset flow
    - Create `POST /api/auth/forgot-password` endpoint: validate email, generate secure token (crypto.randomBytes), store hashed token in PasswordResetToken, send reset email
    - Create `POST /api/auth/reset-password` endpoint: validate token (not expired, not used), hash new password with bcrypt ≥ 10 rounds, invalidate all sessions, mark token used
    - Enforce password strength validation (min 8 chars, 1 uppercase, 1 lowercase, 1 number, 1 special char) via Zod
    - Redirect to login after successful reset; restore intended page after session expiry redirect
    - Display Nepali-language messages for all auth states
    - _Requirements: 17.18, 17.20, 17.21, 37.1, 37.2, 37.3, 37.4, 37.8, 37.9_

  - [x] 1.10 Implement email service and bilingual email templates
    - Configure SMTP/email service provider via environment variables (SMTP_HOST, SMTP_PORT, SMTP_USER, SMTP_PASS)
    - Create reusable email sending utility (`lib/email.ts`) wrapping Nodemailer or Resend
    - Build email templates in both Nepali and English: email verification, password reset, welcome email
    - Use HTML email with inline styles and plain-text fallback; include site logo from dynamic config
    - Test email delivery with mock in test environment
    - _Requirements: 37.5, 37.2, 32.15, 17.22_

- [x] 2. Multi-language and Theme System Implementation
  - [x] 2.1 Implement multi-language support system
    - Create language context and provider for Nepali (primary) and English (secondary)
    - Set up translation files and language switching functionality
    - Implement Nepali calendar and date formatting
    - Configure Devanagari font support and Unicode handling
    - _Requirements: 6.1, 6.2, 6.3, 6.4, 6.5, 6.6, 6.7, 6.8, 6.9, 6.10, 6.11, 6.12, 6.13, 6.14, 6.15_

  - [x] 2.2 Write property test for language switching completeness
    - **Property 19: Language Switching Completeness**
    - **Validates: Requirements 32.3**

  - [x] 2.3 Implement dynamic theme system (light/dark mode)
    - Create theme context and provider with CSS variables
    - Implement theme toggle functionality with smooth transitions
    - Set up theme persistence in browser storage
    - Configure light theme (default) and dark theme colors
    - _Requirements: 31.1, 31.2, 31.3, 31.4, 31.5, 31.6, 31.7, 31.8, 31.9, 31.10, 31.11, 31.12, 31.13, 31.14, 31.15, 31.16, 31.17, 31.18_

  - [x] 2.4 Write property tests for theme system
    - **Property 17: Theme Application Consistency**
    - **Validates: Requirements 31.3**

  - [x] 2.5 Write property test for theme preference persistence
    - **Property 18: Theme Preference Persistence**
    - **Validates: Requirements 31.4**

  - [x] 2.6 Build Design System and Tailwind configuration
    - Configure Tailwind `tailwind.config.ts` with full design token system: brand colors (#c62828 primary), semantic palette (background, foreground, muted, border, accent), typography scale, spacing
    - Define all CSS custom properties in `globals.css` for both `[data-theme='light']` and `[data-theme='dark']` using the exact color values from Req 31.7, 31.8 and the Requirement 31 dark theme spec (#0f0f0f background, #1a1a1a surface, #f0f0f0 text)
    - Load Noto Sans Devanagari and Mukta via Google Fonts with `display: swap`; set proper font-family fallback chain: `'Noto Sans Devanagari', 'Mukta', sans-serif`; define separate `font-latin` for English content
    - Implement category color system: each Category has a `color` field; define a CSS variable `--category-color` per category and apply it to all category tag badges, article card borders, and breadcrumbs. Implement a `CategoryColorProvider` or utility function to inject the variable.
    - Create shared component classes in globals.css: `.btn-primary`, `.btn-secondary`, `.card`, `.badge`, `.skeleton` using the design tokens
    - Verify color contrast ≥ 4.5:1 in both themes using automated tooling
    - _Requirements: 2.4, 5.10, 29.5, 29.6, 31.6, 31.7, 31.8, 31.9, 31.13, 31.15, 31.17, 32.6, 33.9_

  - [x] 2.7 Implement FOUC prevention and admin panel independent theme
    - Add an inline `<script>` in the root `layout.tsx` `<head>` that reads `localStorage.theme` before React hydration and sets `document.documentElement.dataset.theme` synchronously to eliminate FOUC
    - Fall back to `prefers-color-scheme` media query if no saved preference exists
    - When a user is logged in, sync theme preference to `users.theme` column via `PATCH /api/users/me/preferences`
    - Implement a **separate** admin panel theme toggle (stored in `localStorage.adminTheme`) that controls only the admin UI (`/admin/**` routes) without affecting the public site — per Req 31.10
    - Add theme toggle button with sun/moon icon to admin sidebar, independent of the public header toggle
    - _Requirements: 31.2, 31.4, 31.5, 31.12, 31.14, 31.15, 31.10, 31.11_

- [x] 3. Checkpoint - Foundation Complete
  - Ensure all tests pass, ask the user if questions arise.

- [x] 4. Core Content Management System
  - [x] 4.1 Implement category and tag management system
    - Create Category model with hierarchical structure support
    - Create Tag model with many-to-many article relationships
    - Implement category CRUD operations with color and sort order
    - Build admin interface for category and tag management
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 2.6, 2.7, 2.8, 2.9, 2.10, 2.11_

  - [x] 4.2 Write property test for category creation data persistence
    - **Property 6: Category Creation Data Persistence**
    - **Validates: Requirements 2.1**

  - [x] 4.3 Implement comprehensive article management system
    - Create Article model with all required fields (title, content, excerpt, featured image, etc.)
    - Implement rich text editor with Tiptap for article content
    - Build article CRUD operations with draft/published status
    - Create article publishing workflow with timestamp management
    - Auto-calculate `reading_time` (word count ÷ 200 wpm, rounded up) and store `word_count` on save
    - Support `ai_summary` field display above article body when present
    - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5, 1.6, 1.7, 1.8, 1.9, 1.10, 1.11, 1.12, 8.15, 36.1, 36.8_

  - [x] 4.4 Write property tests for article management
    - **Property 1: Article Creation Data Persistence**
    - **Validates: Requirements 1.1**

  - [x] 4.5 Write property test for article update preservation
    - **Property 2: Article Update Preservation**
    - **Validates: Requirements 1.2**

  - [x] 4.6 Write property test for article publishing state transition
    - **Property 3: Article Publishing State Transition**
    - **Validates: Requirements 1.3**

  - [x] 4.7 Implement media upload and management system
    - Create MediaFile model with variants support and proper User relation
    - Implement secure file upload with validation (type, size, magic bytes)
    - Build image optimization with responsive variants generation
    - Create media library interface for admin panel with search, filter, and delete
    - Expose `alt_text` field as an editable field in the media library UI; persist to database
    - Implement drag-and-drop file upload with progress indicator
    - _Requirements: 27.1, 27.2, 27.3, 27.4, 27.5, 27.6, 27.7, 27.8, 27.9, 27.10, 27.11, 27.12, 27.13, 16.11_

  - [x] 4.8 Write property test for image variant generation
    - **Property 4: Image Variant Generation**
    - **Validates: Requirements 1.4**

- [x] 5. User Interaction and Comment System
  - [x] 5.1 Implement comment system with voting
    - Create Comment model with nested replies support
    - Implement comment CRUD operations with moderation workflow
    - Build comment voting system (like/dislike) with vote toggling
    - Create comment display with proper nesting and pagination
    - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5, 4.6, 4.7, 4.8, 4.9, 4.10, 4.11, 4.12_

  - [x] 5.2 Write property tests for comment system
    - **Property 9: Comment Creation Data Persistence**
    - **Validates: Requirements 4.1**

  - [x] 5.3 Write property test for comment vote counting
    - **Property 10: Comment Vote Counting**
    - **Validates: Requirements 4.3**

  - [x] 5.4 Write property test for comment vote toggling
    - **Property 11: Comment Vote Toggling**
    - **Validates: Requirements 4.4**

  - [x] 5.5 Implement user profile and bookmark system
    - Create user profile pages with editable information (name, avatar, password change)
    - Implement bookmark functionality: `POST /api/bookmarks` (add), `DELETE /api/bookmarks/[articleId]` (remove), `GET /api/bookmarks` (list with pagination)
    - Build user dashboard with bookmarked articles list, bookmark count, and remove button
    - Display filled bookmark icon on bookmarked articles; toggle bookmark status optimistically on the frontend
    - Create user settings for theme and language preferences (persist to database when logged in)
    - _Requirements: 23.1, 23.2, 23.3, 23.4, 23.5, 23.6, 23.7, 23.8, 23.9, 23.10, 23.11_

  - [x] 5.6 Implement enhanced reading experience features
    - Display estimated reading time (e.g. "ີ ມິນິດ ປະເວາ") and word count below the article title, calculated from stored `reading_time` and `word_count` fields
    - Generate Table of Contents (TOC) from H2/H3 headings for articles > 800 words; render as a sticky sidebar panel on desktop and a collapsible panel on mobile; implement smooth-scroll on TOC link click
    - Add reading progress bar fixed at the top of the viewport that fills proportionally as the user scrolls through the article body
    - Show a "Back to Top" button (scroll icon) that appears after scrolling 300px down and smooth-scrolls to the top on click
    - Add a "Print" button that opens `window.print()` with a print-specific CSS stylesheet hiding header, sidebar, ads, and nav
    - Add a "Copy Link" button that writes `window.location.href` to clipboard and shows a toast confirmation
    - Persist scroll position per article in `sessionStorage`; restore position when user returns to the same article URL in the same session
    - When a user selects/highlights text in the article body, show a mini floating share menu with "Share Quote" options (copy, Twitter)
    - _Requirements: 36.1, 36.2, 36.3, 36.4, 36.5, 36.6, 36.7, 36.8, 36.9, 36.10_

- [x] 6. Checkpoint - Core Features Complete
  - Ensure all tests pass, ask the user if questions arise.

- [x] 7. Frontend Layout and Navigation System
  - [x] 7.1 Build responsive header and navigation
    - Create responsive header with logo (loaded from site config), navigation menu, and user controls
    - Implement Mega Menu: on hover over a top-level category item, display a full-width dropdown with 4–6 featured article thumbnail cards (image, title, category badge) plus a sidebar listing all subcategory links
    - Build mobile hamburger menu (slide-out drawer, z-index above content, swipe-to-close on touch) with all categories and sub-menus
    - Add search bar (with Nepali/English placeholder), language switcher (NE/EN toggle), and theme toggle (sun/moon icon) all visible in the header on every page
    - Display a top bar above the header with current date (Bikram Sambat format), social media icon links (dynamically loaded from site config), and an RSS feed link
    - Highlight the active navigation item using the current route
    - _Requirements: 9.1, 9.2, 9.3, 9.4, 9.5, 9.6, 9.7, 9.8, 9.9, 9.10, 9.11, 9.12, 28.4, 33.8_

  - [x] 7.2 Implement homepage layout with all sections
    - Create breaking news ticker with auto-scroll (ARIA live region, pause on hover)
    - Build hero section with 1–2 featured articles with large images
    - Implement all named news sections: समाचार, फिचर, कभर स्टोरी, सप्ताहान्त, प्रविधि, अन्तर्वार्ता, छुटाउनुभयो कि ?
    - Add sports section, video section, and OK Reels horizontal scrolling carousel
    - Create right sidebar on desktop with trending articles (fire icon badge), "Most Read" (by total views), and "Most Commented" (by comment count) sections, plus advertisement slots
    - Add trending indicator badge (fire icon / "trending" text) on article cards that qualify as trending (in last 24 hours top views per Req 24.1)
    - Display advertisement slots between content sections at designated positions
    - Load homepage sections progressively (React Suspense + loading skeletons per section)
    - _Requirements: 7.1, 7.2, 7.3, 7.4, 7.5, 7.6, 7.7, 7.8, 7.9, 7.10, 7.11, 7.12, 7.13, 7.14, 7.15, 7.16, 7.17, 7.18, 24.2, 24.3, 24.5, 24.6, 24.7, 24.8_

  - [x] 7.3 Build article display page with full functionality
    - Create article page layout with breadcrumbs (Home > Category > Title) and metadata header
    - Implement article content rendering with rich Tiptap HTML formatting (code blocks, embeds, images)
    - Display reading time and word count below the article title
    - Add social share buttons (Facebook, Twitter/X, Copy Link) and view counter
    - Display AI summary above the article body when the `ai_summary` field is populated
    - Build related articles section and integrate comment section (EnhancedReadingExperience from 5.6)
    - Display advertisement slots within and around the article (header, in-article, footer positions)
    - _Requirements: 8.1, 8.2, 8.3, 8.4, 8.5, 8.6, 8.7, 8.8, 8.9, 8.10, 8.11, 8.12, 8.13, 8.14, 8.15, 8.16, 36.1, 36.8_

  - [x] 7.4 Implement category archive and search pages
    - Build category archive pages with article listings
    - Create search functionality with full-text search
    - Implement pagination and infinite scroll options
    - Add filtering and sorting capabilities
    - _Requirements: 22.1, 22.2, 22.3, 22.4, 22.5, 22.6, 22.7, 22.8, 22.9, 22.10, 22.11, 10.1, 10.2, 10.3, 10.4, 10.5, 10.6, 10.7, 10.8, 10.9, 10.10, 10.11_

  - [x] 7.5 Write property tests for search functionality
    - **Property 12: Search Query Matching**
    - **Validates: Requirements 10.1**

  - [x] 7.6 Write property test for search result ranking
    - **Property 13: Search Result Ranking**
    - **Validates: Requirements 10.3**

  - [x] 7.7 Build Footer component
    - Create a full `Footer` component that reads all content from `SiteConfigContext` (no hardcoded values)
    - Sections: organization name, tagline, registration number, phone, email, address (from site config Req 33.7); social media icon links (Facebook, Twitter/X, YouTube, Instagram, TikTok) with dynamic URLs from site config Req 33.8; quick links column; category links column (top-level categories from DB); copyright text with dynamic year and text from site config Req 33.13
    - Display links to Privacy Policy, Terms of Service, and Cookie Policy pages
    - Display RSS feed link and newsletter signup button (if configured)
    - Fully responsive: single-column on mobile, multi-column grid on tablet/desktop
    - Apply current theme and pass accessibility requirements (ARIA roles, link focus)
    - _Requirements: 9.11, 28.3, 28.4, 28.5, 33.7, 33.8, 33.13, 33.15, 38.5, 38.6_

  - [x] 7.8 Build static informational pages
    - Create `/privacy-policy` page with Privacy Policy content (editable from admin rich text or static markdown)
    - Create `/terms-of-service` page with Terms of Service content
    - Create `/cookie-policy` page explaining cookie usage categories (essential, analytics, advertising)
    - Create `/about` page loading content from site config or a dedicated DB field
    - All pages: proper SEO metadata, breadcrumb navigation, Nepali/English language support, themed styling
    - Link all pages from the footer component
    - _Requirements: 38.5, 38.6, 16.1, 9.11_

- [x] 8. Advanced Features Implementation
  - [x] 8.1 Implement sports section with live scores
    - Create Tournament and Match models with team support
    - Build live score display with match cards and status
    - Implement tournament management with tab navigation
    - Create admin interface for score updates and team management
    - _Requirements: 11.1, 11.2, 11.3, 11.4, 11.5, 11.6, 11.7, 11.8, 11.9, 11.10, 11.11, 11.12, 11.13_

  - [x] 8.2 Implement OK Reels video feature
    - Create Reel model with video URL and metadata
    - Build reel display with horizontal scrolling carousel
    - Implement reel page with video player and related content
    - Create admin interface for reel management
    - _Requirements: 12.1, 12.2, 12.3, 12.4, 12.5, 12.6, 12.7, 12.8, 12.9, 12.10, 12.11_

  - [x] 8.3 Implement photo gallery feature
    - Create Gallery and GalleryImage models
    - Build gallery display with grid layout and lightbox viewer
    - Implement gallery navigation and image captions
    - Create admin interface for gallery management
    - _Requirements: 13.1, 13.2, 13.3, 13.4, 13.5, 13.6, 13.7, 13.8, 13.9, 13.10, 13.11_

  - [x] 8.4 Implement advertisement management system
    - Create AdPosition and Advertisement models
    - Build ad display system with position-based rendering
    - Implement ad tracking (impressions, clicks, CTR)
    - Create admin interface for ad management and analytics
    - _Requirements: 14.1, 14.2, 14.3, 14.4, 14.5, 14.6, 14.7, 14.8, 14.9, 14.10, 14.11, 14.12, 14.13_

  - [x] 8.5 Implement breaking news management
    - Create BreakingNews model with expiry support and add `breaking_news BreakingNews[]` back-relation on Article model
    - Build breaking news ticker with ARIA live region (`aria-live="polite"`) and continuous CSS marquee scroll (pause on hover)
    - Implement admin CRUD interface: list active/expired items, create with title + optional article link + expiry datetime, manual activate/deactivate toggle
    - Auto-expire: a cron job or ISR revalidation removes expired items from the ticker without a page restart
    - Ticker visible on all pages (in the shared layout)
    - _Requirements: 20.1, 20.2, 20.3, 20.4, 20.5, 20.6, 20.7, 20.8, 20.9, 20.10, 29.8_

  - [x] 8.6 Implement AI Summary stub and display
    - Add `ai_summary` field to Article model schema (already present in design) and expose it in the article editor as an optional textarea labeled "AI Summary"
    - When `ai_summary` is present, render it above the article body in a visually distinct card (e.g., highlighted border, lightning bolt icon, "AI-generated summary" label) in both Nepali and English
    - Create a placeholder `POST /api/articles/[id]/generate-summary` endpoint that returns a 501 Not Implemented with a `future_enhancement: true` flag — enabling integration of a real LLM later without UI changes
    - _Requirements: 8.15, 1.11_

- [x] 9. Checkpoint - Advanced Features Complete
  - Ensure all tests pass, ask the user if questions arise.

- [x] 10. Admin Panel and Management Interface
  - [x] 10.1 Build comprehensive admin dashboard
    - Create admin layout with sidebar navigation listing all sections: Dashboard, Articles, Categories, Tags, Authors, Comments, Ads, Sports, Reels, Galleries, Analytics, Settings, Users, Breaking News, Audit Log
    - Implement dashboard with quick stats (articles today, total views, comments pending, active ads) and recent activity feed
    - Build data tables with sorting, filtering, and pagination for all entity types
    - Add bulk operations (select all, publish, archive, delete) and inline editing for simple fields
    - Display confirmation modal dialogs for all destructive actions (delete, bulk delete)
    - Display success/error toast notifications for all operations
    - _Requirements: 19.1, 19.2, 19.3, 19.4, 19.5, 19.6, 19.7, 19.8, 19.9, 19.10, 19.11, 19.12_

  - [x] 10.2 Implement author management and profiles
    - Create Author model with profile information
    - Build author profile pages with article listings
    - Implement admin interface for author management
    - Add author performance metrics and social links
    - _Requirements: 21.1, 21.2, 21.3, 21.4, 21.5, 21.6, 21.7, 21.8, 21.9, 21.10_

  - [x] 10.3 Build analytics dashboard
    - Implement page view tracking and analytics collection
    - Create analytics dashboard with charts and metrics
    - Build performance reports (top articles, traffic sources, device breakdown)
    - Add real-time user tracking and engagement metrics
    - _Requirements: 15.1, 15.2, 15.3, 15.4, 15.5, 15.6, 15.7, 15.8, 15.9, 15.10, 15.11, 15.12_

  - [x] 10.4 Implement dynamic site configuration system
    - Create SiteSettings model with key-value JSON storage; seed default values on first run
    - Build settings management interface with sections: Branding (logo, site name, tagline, favicon), Contact (phone, email, address, registration number), Social Media (Facebook, Twitter/X, YouTube, Instagram, TikTok), SEO Defaults, Feature Toggles (enable/disable comments, bookmarks, reels, galleries), Breaking Ticker Settings
    - Implement **brand color picker**: admin can set the primary brand color (stored as `primary_color` setting); apply it via CSS variable `--color-primary` globally so all tags, accents, and buttons update immediately
    - Implement **drag-and-drop homepage section reordering** using `@hello-pangea/dnd` or similar; save section order as a JSON array in site settings; homepage reads this order to render sections
    - Implement **settings preview**: show a live preview panel of header/footer before saving
    - Implement **settings history/changelog**: on every settings save, append a record to an `AuditLog` entry (admin_id, timestamp, changed keys, old/new JSON diff); display last 10 changes in a "Settings History" tab with rollback button
    - Implement settings cache: load settings on startup into a module-level cache; invalidate and reload within 30 seconds when settings are updated via `revalidatePath` or a Redis pub/sub event
    - All site components (logo, site name, footer content, navigation) read exclusively from `SiteConfigContext`, never from hardcoded values
    - _Requirements: 28.1, 28.2, 28.3, 28.4, 28.5, 28.6, 28.7, 28.8, 28.9, 28.10, 28.11, 28.12, 28.13, 28.14, 28.15, 28.16, 28.17, 28.18, 28.19, 33.1, 33.2, 33.3, 33.4, 33.5, 33.6, 33.7, 33.8, 33.9, 33.10, 33.11, 33.12, 33.13, 33.14, 33.15, 33.16, 33.17_

  - [x] 10.5 Write property test for dynamic configuration propagation
    - **Property 20: Dynamic Configuration Propagation**
    - **Validates: Requirements 33.15**

  - [x] 10.6 Implement admin panel independent dark/light theme
    - Implement an independent theme toggle in the admin sidebar (independent from the public site's theme)
    - Store admin theme in `localStorage.adminTheme`; theme only applies to routes under `/admin/**`
    - Implement admin-specific CSS variable overrides in a scoped `[data-admin-theme='dark']` selector
    - Sync admin theme preference to `users.admin_theme` column if the user is logged in
    - Verify the admin theme does not pollute or override the public site theme variable scope
    - _Requirements: 31.10, 31.11, 31.13_

  - [x] 10.7 Implement audit log system
    - Create `AuditLog` model (added in task 1.7) with fields: id, admin_id, action (CREATE/UPDATE/DELETE/PUBLISH/SETTINGS_CHANGE), entity (e.g. 'article'), entity_id, old_value (JSON), new_value (JSON), ip_address, created_at
    - Write an `auditLog(action, entity, entityId, oldValue, newValue)` utility that inserts a record inside the same Prisma transaction as every admin CRUD operation
    - Wrap all admin API routes (articles, categories, authors, comments, settings, users) with the audit utility
    - Build an Audit Log admin page: searchable/filterable table with admin name, action type, entity, timestamp, and expandable old/new JSON diff view
    - Ensure sensitive fields (password_hash, tokens) are never written to audit logs
    - _Requirements: 17.36, 17.35, 19.1_

- [x] 11. SEO and Performance Optimization
  - [x] 11.1 Implement comprehensive SEO system
    - Create SEO metadata generation for all pages
    - Implement Open Graph and Twitter Card tags
    - Build XML sitemap and robots.txt generation
    - Add structured data (JSON-LD) for articles
    - _Requirements: 16.1, 16.2, 16.3, 16.4, 16.5, 16.6, 16.7, 16.8, 16.9, 16.10, 16.11, 16.12, 16.13_

  - [x] 11.2 Write property tests for SEO system
    - **Property 14: SEO Metadata Uniqueness**
    - **Validates: Requirements 16.1**

  - [x] 11.3 Write property test for structured data completeness
    - **Property 15: Structured Data Completeness**
    - **Validates: Requirements 16.5**

  - [x] 11.4 Implement performance optimizations
    - Configure ISR and SSR for optimal page loading
    - Implement image optimization with modern formats
    - Add lazy loading and code splitting
    - Optimize bundle size and implement caching strategies
    - _Requirements: 18.1, 18.2, 18.3, 18.4, 18.5, 18.6, 18.7, 18.8, 18.9, 18.10, 18.11, 18.12, 18.13, 18.14, 18.15, 18.16_

  - [x] 11.5 Implement trending and popular content system
    - Create trending algorithm: query articles with most `PageView` records in the last 24 hours, recalculated every 15 minutes via a cron or Next.js revalidation tag
    - Cache trending results in Redis (or in-memory) to avoid repeated heavy queries
    - Expose `GET /api/trending` returning top 5 trending articles with thumbnails and titles
    - Build "Most Read" section: articles ordered by `view_count DESC` all-time
    - Build "Most Commented" section: articles ordered by `comment_count DESC`
    - Add trending indicator (fire icon badge) on article cards that appear in the trending list
    - Exclude articles older than 7 days from trending calculation
    - _Requirements: 24.1, 24.2, 24.3, 24.4, 24.5, 24.6, 24.7, 24.8, 24.9, 24.10_

  - [x] 11.6 Implement RSS Feed endpoint
    - Create a `GET /rss.xml` route (or `/feed.xml`) generating a valid RSS 2.0 XML feed of the last 50 published articles
    - Include per-item: `<title>`, `<link>`, `<description>` (excerpt), `<pubDate>`, `<author>`, `<category>`, `<enclosure>` (featured image), `<guid>`
    - Add appropriate `Content-Type: application/rss+xml` response header and caching (ISR 60s)
    - Also create a `GET /api/comments/rss.xml` per Req 16.13 for article comments feed
    - Link RSS feed in `<head>` with `<link rel="alternate" type="application/rss+xml">` on all pages
    - _Requirements: 16.13, 30.1_

  - [x] 11.7 Implement dedicated View Counter API endpoint
    - Create `POST /api/articles/[id]/view` endpoint that increments `articles.view_count` in the database and inserts a `PageView` record (page_url, article_id, user_id if logged in, ip_address, user_agent, referrer)
    - Call this endpoint from the article page client component on first mount using a `useEffect` (debounced, called only once per visit via sessionStorage flag)
    - Rate-limit this endpoint per IP to prevent view inflation (max 1 increment per article per 10 minutes per IP)
    - Return the updated view count so the UI can display it without a page reload
    - _Requirements: 8.13, 26.7, 17.6_

  - [x] 11.8 Implement analytics event collection pipeline
    - Create an analytics middleware or `POST /api/analytics/event` endpoint to log: page_url, article_id, event_type (page_view, scroll_depth, share_click, comment_submit), user_id, device_type (from UA parsing), referrer source (direct/search/social/referral)
    - Create a `TrafficSource` utility that parses `Referer` header into categories for the analytics dashboard device breakdown and traffic source charts
    - Ensure the collection pipeline only runs after cookie consent is granted for analytics (check `localStorage.consentAnalytics`) — do not track users who opted out
    - _Requirements: 15.5, 15.6, 15.7, 38.3, 38.7_

- [x] 12. Security Implementation
  - [x] 12.1 Implement comprehensive security measures
    - Add CSRF protection with tokens for all state-changing operations
    - Implement XSS filtering with DOMPurify for user content
    - Configure rate limiting for API endpoints and login attempts
    - Set up security headers and Content Security Policy
    - _Requirements: 17.1, 17.2, 17.3, 17.4, 17.5, 17.6, 17.7, 17.8, 17.9, 17.10, 17.11, 17.12, 17.13, 17.14, 17.15, 17.16, 17.17, 17.18, 17.19, 17.20, 17.21, 17.22, 17.23, 17.24, 17.25, 17.26, 17.27, 17.28, 17.29, 17.30, 17.31, 17.32, 17.33, 17.34, 17.35, 17.36, 17.37, 17.38, 17.39, 17.40_

  - [x] 12.2 Write property test for XSS content sanitization
    - **Property 16: XSS Content Sanitization**
    - **Validates: Requirements 17.4**

  - [x] 12.3 Implement advanced security features
    - Add **two-factor authentication (2FA) for admin accounts** using `otplib` (TOTP) and `qrcode` packages: generate a TOTP secret on admin account setup, display QR code for authenticator app pairing, verify 6-digit code on every admin login, allow recovery codes, store encrypted secret in the `User` model
    - Implement IP whitelisting for admin panel access (optional, configurable via env var `ADMIN_IP_WHITELIST`)
    - Configure database backup encryption and secure storage (document backup script in deployment guide)
    - Set up security monitoring: integrate Sentry for error tracking; log all auth events and security violations to a structured log file
    - _Requirements: 17.38, 17.37, 17.36, 17.35, 17.39_

  - [x] 12.4 Implement security headers and CSP middleware
    - Create a Next.js middleware file (`middleware.ts`) that sets all required security response headers on every response:
      - `X-Frame-Options: DENY`
      - `X-Content-Type-Options: nosniff`
      - `Strict-Transport-Security: max-age=31536000; includeSubDomains`
      - `Referrer-Policy: strict-origin-when-cross-origin`
      - `Permissions-Policy: camera=(), microphone=(), geolocation=()`
      - `Content-Security-Policy: default-src 'self'; ...(properly configured per used CDNs and fonts)`
    - Implement CSRF token generation and validation middleware for all non-GET API routes: generate a secure random CSRF token, store in HTTP-only cookie, require matching `X-CSRF-Token` header on mutations
    - Implement HTTPS redirect: if `X-Forwarded-Proto !== 'https'` in production, redirect to HTTPS
    - Add `X-Frame-Options` and `frame-ancestors 'none'` CSP directive for clickjacking protection (Req 17.32)
    - Validate and sanitize all environment variables on startup; throw on missing required vars
    - _Requirements: 17.2, 17.3, 17.10, 17.11, 17.14, 17.15, 17.17, 17.30, 17.31, 17.32, 17.33_

  - [x] 12.5 Implement rate limiting middleware
    - Implement layered rate limiting as Next.js middleware using an in-memory store (or Redis if available):
      - General API: 100 requests per minute per IP
      - Auth endpoints (`/api/auth/**`): 5 requests per 15 minutes per IP
      - Comment posting (`POST /api/comments`): 5 comments per minute per authenticated user
      - Search endpoint (`GET /api/search`): 20 requests per minute per IP
      - View counter (`POST /api/articles/*/view`): 1 per 10 minutes per IP per article
    - Return HTTP 429 with `Retry-After` header when a limit is exceeded
    - Log rate limit violations to the security audit log
    - _Requirements: 17.6, 17.7, 3.9, 4.11_

  - [x] 12.6 Implement API versioning
    - Prefix all API routes with `/api/v1/` (e.g., `/api/v1/articles`, `/api/v1/categories`)
    - Create a Next.js route group `app/api/v1/` and move all existing API route files there
    - Add backward-compat redirect middleware: requests to old `/api/` paths (without version) get a permanent redirect or passthrough to v1 during transition
    - Update all frontend fetch calls and SWR keys to use `/api/v1/` paths
    - Document versioning strategy in the API documentation (task 13.3)
    - _Requirements: 30.12_

- [x] 13. Error Handling and User Experience
  - [x] 13.1 Implement comprehensive error handling
    - Create custom 404 page (`not-found.tsx`) with site navigation and suggested links
    - Create custom 500 page (`error.tsx`) with friendly message, a "Try again" button, and a link to the homepage
    - Build a global `ErrorBoundary` React class component that catches React render errors and displays a fallback UI
    - Build a reusable `SkeletonLoader` component system: implement skeleton variants for ArticleCard, ArticleList, Sidebar, HeroSection, CommentList, and AdminTable; render them via `loading.tsx` in every route segment
    - Add placeholder image component shown when an `<img>` fails to load (via `onError` fallback)
    - Implement **offline indicator**: listen to `window.addEventListener('online'/'offline')` events; display a dismissible top banner "You are offline. Some content may not be available." in both Nepali and English
    - _Requirements: 25.1, 25.2, 25.3, 25.4, 25.5, 25.6, 25.7, 25.8, 25.9, 25.10, 25.11, 25.12, 35.7_

  - [x] 13.2 Implement accessibility compliance
    - Add semantic HTML5 elements and ARIA labels
    - Ensure keyboard navigation and screen reader support
    - Implement proper color contrast and focus indicators
    - Add skip-to-content links and proper heading hierarchy
    - _Requirements: 29.1, 29.2, 29.3, 29.4, 29.5, 29.6, 29.7, 29.8, 29.9, 29.10, 29.11, 29.12, 29.13_

  - [x] 13.3 Build API documentation and design
    - Implement RESTful API with proper HTTP status codes
    - Create consistent JSON response format
    - Add API validation and error handling
    - Build API documentation with examples
    - _Requirements: 30.1, 30.2, 30.3, 30.4, 30.5, 30.6, 30.7, 30.8, 30.9, 30.10, 30.11, 30.12_

  - [x] 13.4 Implement Cookie Consent and Privacy Compliance
    - Show a cookie consent banner on first visit (before any non-essential cookies are set) with three options: "Accept All", "Reject Non-Essential", "Manage Preferences"
    - "Manage Preferences" opens a modal where the user can individually toggle: Analytics cookies, Advertising cookies, Functional cookies (language/theme preferences = always on)
    - Persist consent choices in `localStorage.cookieConsent` as a JSON object `{ analytics: bool, advertising: bool }`
    - Gate the analytics pipeline (task 11.8) and advertisement tracking (task 8.4 clicks/impressions) behind the consent flags; if consent is rejected, skip tracking calls
    - Banner must be keyboard-navigable and have proper ARIA roles (`role="dialog"`, `aria-labelledby`, focus trap)
    - Provide a "Cookie Preferences" link in the footer that reopens the preference modal at any time
    - _Requirements: 38.1, 38.2, 38.3, 38.4, 38.7, 38.8, 38.9, 29.3_

  - [x] 13.5 Implement Progressive Web App (PWA) support
    - Create `/public/manifest.json` with name, short_name, icons (192x192, 512x512), theme_color (from site config primary color), background_color, display: standalone; link in `layout.tsx` `<head>`
    - Register a service worker (`/public/sw.js`) using Next.js PWA plugin (`next-pwa`) or Workbox:
      - Cache static assets (JS, CSS, fonts) on install using a precache strategy
      - Cache the last 20 viewed article pages in a runtime cache using stale-while-revalidate
      - When the network is unavailable, serve cached articles; if not cached, show the offline page
    - Add an "Install App" A2HS prompt: listen to `beforeinstallprompt` event, show a custom install banner in the header (dismissible), trigger `prompt()` on user click
    - Implement Web Push for breaking news: request notification permission when user opts in from profile settings; subscribe to push service; admin can trigger a push notification when publishing a Breaking News item; send via a `POST /api/push/send` endpoint
    - Provide separate icon sets for light and dark theme in the manifest per Req 35.9
    - _Requirements: 35.1, 35.2, 35.3, 35.4, 35.5, 35.6, 35.7, 35.8, 35.9_

- [x] 14. Checkpoint - Security and UX Complete
  - Ensure all tests pass, ask the user if questions arise.

- [x] 15. Comprehensive Testing Implementation
  - [x] 15.1 Set up testing framework and configuration
    - Configure Vitest for unit and integration tests
    - Set up Playwright for end-to-end testing
    - Create test database and seed data for testing
    - Configure test coverage reporting and CI/CD pipeline
    - _Requirements: 32.36, 32.37, 32.38_

  - [x] 15.2 Write comprehensive unit tests
    - Test all utility functions and helper modules
    - Test React components with React Testing Library
    - Test API endpoints with request/response validation
    - Test database operations and Prisma queries
    - _Requirements: 32.1, 32.25_

  - [x] 15.3 Write integration tests
    - Test authentication flows: register, login (email/password), Google OAuth, logout
    - Test email verification: token generation, correct verification, expired token rejection, restrict-comment enforcement
    - Test password reset flow: forgot-password email, valid token accepts new password, expired/used token rejected, session invalidation on reset
    - Test CRUD operations for all entities (articles, categories, tags, authors, comments, galleries, reels, ads)
    - Test comment system: create, nested reply, like/dislike toggle, moderation workflow
    - Test search and filtering with Nepali and English queries
    - Test bookmark add/remove/list with pagination
    - Test analytics event collection gated by cookie consent flag
    - _Requirements: 32.2, 32.6, 32.7, 32.8, 32.9, 32.10, 37.2, 37.3, 37.4, 37.5, 37.6_

  - [x] 15.4 Write end-to-end tests with Playwright
    - Configure Playwright with Chromium (primary), Firefox, and Safari (WebKit) projects per the design.md config
    - Cover all Requirement 34 acceptance criteria:
      - Homepage: all sections render, nav links navigate, breaking ticker displays and scrolls
      - Article page: content renders, AI summary visible if present, share buttons clickable, comment form accessible, TOC generated for long articles, reading progress bar fills on scroll
      - Navigation: all top-level links navigate correctly, mega menu opens on hover with featured article cards
      - Theme toggle: switching to dark applies dark background and light text; switching back restores light
      - Language switcher: switching to English changes all UI labels; switching back restores Nepali
      - Auth flow: registration, email login, logout; verify restricted comment form for unverified email
      - Comment flow: submit comment, verify it appears, like/dislike a comment, reply to comment
      - Search: enter Nepali query, results load, click result navigates to article
      - Category archive: correct articles listed, pagination works
      - Responsive layouts: test at viewports 320px, 768px, 1024px, 1440px, 1920px — hamburger menu shows below 768px
      - Admin panel: login as admin, create article, publish, verify it appears on homepage
      - Bookmark: bookmark an article while logged in, verify it appears in profile bookmarks
      - 404 page: navigate to unknown URL, custom 404 shown with navigation links
      - Ad slots: designated ad position containers render in the DOM
      - Cookie consent banner: appears on first visit; "Accept All" dismisses it; preferences persist on reload
      - PWA: manifest link present in head; service worker registers; install prompt banner appears
    - _Requirements: 32.3, 32.4, 32.5, 32.11, 32.12, 32.13, 32.19, 32.27, 34.1, 34.2, 34.3, 34.4, 34.5, 34.6, 34.7, 34.8, 34.9, 34.10, 34.11, 34.12, 34.13, 34.14, 34.15, 34.16, 34.17, 34.18_

  - [x] 15.5 Write performance and accessibility tests
    - Test page load times and Core Web Vitals
    - Test keyboard navigation and screen reader compatibility
    - Test cross-browser compatibility
    - Test mobile device experience
    - _Requirements: 32.23, 32.24, 32.34, 32.35_

  - [x] 15.6 Write security and load tests
    - Test security features (CSRF token required on mutations, XSS sanitized in comments, rate limiting returns 429)
    - Perform load testing for concurrent users using k6 or Artillery: simulate 100 concurrent users on article pages, verify response times stay within acceptable range
    - Test file upload security: reject files with wrong magic bytes, reject executables (.exe, .php), enforce size limits
    - Test database query performance: ensure all list endpoints respond within 500ms under test load
    - _Requirements: 32.21, 32.32, 32.14, 32.25_

  - [x] 15.7 Implement visual regression tests
    - Use Playwright's built-in screenshot comparison or `@percy/playwright` to capture screenshots of key pages: homepage, article page, category page, admin dashboard, login page
    - Capture screenshots in both light and dark themes, at 1440px desktop and 375px mobile viewports
    - Run baseline screenshot generation on first run; subsequent runs compare and fail on > 0.1% pixel difference
    - Include visual tests in the CI/CD pipeline to catch unintended UI changes before merge
    - _Requirements: 32.31_

  - [x] 15.8 Implement load and stress tests
    - Write k6 or Artillery load test scripts simulating 100 concurrent virtual users on the homepage and article pages
    - Simulate a spike of 500 requests/second for 30 seconds to verify rate limiting and graceful degradation
    - Measure and assert: P95 response time < 2 seconds, zero 5xx errors under normal load, 429 returned correctly under rate-limit conditions
    - Test database connection pool behavior under high concurrency; verify no connection exhaustion
    - _Requirements: 32.32_

- [x] 16. Final Integration and Deployment Preparation
  - [x] 16.1 Complete system integration and testing
    - Integrate all components and test end-to-end workflows
    - Verify all property-based tests pass with 100+ iterations
    - Test all admin panel features including audit log, drag-and-drop section reordering, brand color picker, settings history and rollback, and 2FA for admin accounts
    - Validate multi-language support (Nepali default, English switch) and theme consistency (light/dark including admin panel independent theme)
    - Validate FOUC prevention: no theme flash on initial page load
    - Test PWA: manifest loads, service worker registers, offline mode serves cached articles, install banner appears
    - Test Cookie Consent: banner appears on first visit, analytics and ad tracking are gated behind consent, preferences persist
    - Test enhanced reading experience: TOC, reading progress bar, back-to-top, print view, reading time, text highlight share, scroll position persistence
    - Test RSS feed valid XML at `/rss.xml`; verify structured data (JSON-LD) passes Google Rich Results Test
    - Run Lighthouse on homepage and article page; verify scores ≥ 90 desktop, ≥ 80 mobile
    - Confirm all API endpoints use `/api/v1/` prefix and return `{ success, data, error }` format
    - _Requirements: All requirements integration_

  - [x] 16.2 Prepare production deployment configuration
    - Configure environment variables for production
    - Set up database migrations and production seeds
    - Configure CDN and static asset optimization
    - Set up monitoring, logging, and error tracking
    - _Requirements: Production deployment readiness_

  - [x] 16.3 Final quality assurance and documentation
    - Perform comprehensive manual testing in Chromium
    - Verify all acceptance criteria are met
    - Create deployment documentation and user guides
    - Ensure all security measures are properly configured
    - _Requirements: 32.39, All acceptance criteria validation_

- [x] 17. Final Checkpoint - System Complete
  - Ensure all tests pass, ask the user if questions arise.

## Notes

- All tasks are required for complete system implementation
- Each task references specific requirements for traceability and validation
- Property-based tests validate universal correctness properties with minimum 100 iterations
- Checkpoints ensure incremental validation and provide opportunities for user feedback
- The implementation uses TypeScript throughout for type safety and better developer experience
- All security measures are implemented according to enterprise-grade standards
- The system supports both Nepali and English languages with proper Unicode handling
- Dynamic configuration allows all site elements to be managed from the admin panel
- Comprehensive testing ensures reliability across all browsers and devices

## Gap Fixes Applied (vs. Original Tasks)

### Database Schema Fixes
- Added tasks 1.7 to define missing models: `AuditLog`, `PasswordResetToken`, `EmailVerificationToken`, `WebStory`
- Fixed `Article` model: added `breaking_news` and `page_views` back-relations
- Fixed `MediaFile` model: corrected `uploaded_by` to a proper `User` relation; added `media_files` relation on `User`
- Added explicit database index requirements for frequently queried fields

### Auth / Email Flows
- Added tasks 1.8 (email verification), 1.9 (password reset), and 1.10 (email templates) covering Requirements 37 and 17.21–17.22 which were entirely absent

### Design System and Theme Consistency
- Added task 2.6 (Design System: Tailwind config, CSS variables, Devanagari font, category color system) addressing Req 2.4, 31.6–31.9, 33.9
- Added task 2.7 (FOUC prevention + admin independent theme) addressing Req 31.14, 31.10 which were not covered

### Frontend/UI Gaps
- Expanded task 7.1 to include full Mega Menu spec, top bar with social links and date
- Expanded task 7.2 to include "Most Read", "Most Commented" sections (Req 24.5–24.6) and trending badges (Req 24.8)
- Expanded task 7.3 to include reading time, word count, and AI summary display
- Added task 7.7 (complete Footer component reading from SiteConfigContext) — previously no dedicated task existed
- Added task 7.8 (static pages: Privacy Policy, ToS, Cookie Policy, About) — previously absent
- Expanded task 5.5 with explicit bookmark API endpoints
- Added task 5.6 (Enhanced Reading Experience: TOC, reading progress bar, back-to-top, print, reading time, text highlight share, scroll persistence) covering all of Requirement 36

### Backend/API Gaps
- Added task 8.6 (AI Summary display and stub endpoint)
- Added task 11.6 (RSS Feed endpoint) covering Req 16.13
- Added task 11.7 (dedicated View Counter API) covering Req 8.13 properly
- Added task 11.8 (Analytics event collection pipeline, consent-gated) covering Req 15.5–15.7, 38.3
- Expanded task 10.7 (Audit Log system) with full model, utility, and admin UI
- Expanded task 10.4 to include drag-and-drop section reordering, brand color picker, settings history/rollback, and preview

### Security
- Expanded task 12.3 to include 2FA implementation detail with otplib and QR pairing
- Added task 12.4 (Security Headers and CSP Middleware) covering Req 17.10, 17.14, 17.15, 17.30–17.33
- Added task 12.5 (Rate Limiting Middleware) covering layered rate limits per Req 17.6–17.7, 3.9, 4.11
- Added task 12.6 (API Versioning) covering Req 30.12

### New Requirements (34–38) — Previously Entirely Absent
- Requirement 34 (Playwright E2E Tests): fully expanded in task 15.4
- Requirement 35 (PWA): fully covered in task 13.5
- Requirement 36 (Enhanced Reading Experience): fully covered in task 5.6
- Requirement 37 (Password Reset / Email Verification): fully covered in tasks 1.8, 1.9, 1.10
- Requirement 38 (Cookie Consent / Privacy): fully covered in task 13.4

### Testing Gaps
- Expanded task 15.3 integration tests to include email verification and password reset flows
- Expanded task 15.4 E2E tests to fully cover all 18 Requirement 34 acceptance criteria
- Added task 15.7 (Visual Regression Tests) covering Req 32.31
- Added task 15.8 (Load and Stress Tests) covering Req 32.32
- Expanded task 16.1 to validate all new features in final integration
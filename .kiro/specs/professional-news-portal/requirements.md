# Requirements Document

## Introduction

This document specifies the requirements for a professional news portal website inspired by OnlineKhabar.com. The system will be a modern, responsive news platform built with Next.js 14+, React 18+, TypeScript, PostgreSQL, and Prisma ORM. The portal will support multi-language content (Nepali primary/default, English secondary), light/dark theme modes (light mode default), comprehensive content management, user engagement features, and a professional admin panel. All site elements (logo, footer, site name, etc.) will be dynamically configurable from the admin panel without code changes. The design will be more polished and consistent than the reference site while maintaining professional news website standards across all devices. Comprehensive testing will be performed in Chromium browser to ensure all buttons, pages, functions, and functionalities work correctly.

## Glossary

- **News_Portal**: The complete web application system including frontend, backend, and database
- **Content_Management_System**: The admin panel for managing articles, categories, users, and site content
- **Article**: A news story or editorial content piece with title, body, images, and metadata
- **Category**: A hierarchical classification for organizing articles (e.g., Politics, Sports, Entertainment)
- **Tag**: A keyword label for cross-referencing articles
- **Author**: A content creator who writes articles
- **User**: A registered site visitor who can comment and interact with content
- **Admin**: A privileged user with access to the Content_Management_System
- **Comment_System**: The feature allowing users to post comments and vote on them
- **Breaking_News_Ticker**: A horizontal scrolling banner displaying urgent news headlines
- **OK_Reels**: Short-form video content feature similar to social media reels
- **Live_Score_System**: Real-time sports match score display and management
- **Photo_Gallery**: A collection of images organized as a viewable gallery
- **Advertisement_System**: The system for managing and displaying ads across the site
- **Rich_Text_Editor**: The WYSIWYG editor for composing article content
- **OAuth_Provider**: External authentication service (Google) for user login
- **SEO_System**: Search engine optimization features including meta tags and structured data
- **Analytics_Dashboard**: The admin interface displaying site metrics and statistics
- **Responsive_Design**: UI that adapts to different screen sizes (mobile, tablet, desktop)
- **API_Route**: Next.js server-side endpoint for handling data operations
- **Prisma_Client**: The ORM interface for database operations
- **NextAuth**: The authentication library for handling user sessions
- **Mega_Menu**: Large dropdown navigation menu with featured content
- **View_Counter**: System tracking article page views
- **RSS_Feed**: XML feed of recent articles for syndication
- **Rate_Limiter**: Security mechanism preventing excessive API requests
- **CSRF_Token**: Security token preventing cross-site request forgery
- **XSS_Filter**: Security mechanism preventing cross-site scripting attacks
- **File_Upload_Validator**: Security component validating uploaded media files
- **Session_Manager**: Component managing user authentication sessions
- **Database_Connection_Pool**: PostgreSQL connection management system
- **Image_Optimizer**: Next.js component for optimizing and serving images
- **ISR**: Incremental Static Regeneration for cached page updates
- **SSR**: Server-Side Rendering for dynamic page generation
- **Nepali_Calendar**: Date display system using Nepali calendar format
- **AI_Summary_Generator**: Feature generating article summaries (future enhancement)
- **Search_Engine**: Full-text search system for articles
- **Bookmark_System**: Feature allowing users to save articles
- **Trending_Algorithm**: System determining popular content
- **Web_Story**: Interactive story format with slides
- **Interactive_Story**: Rich media story with embedded interactive elements
- **Tournament**: Sports competition with multiple matches
- **Match**: Individual sports game with teams and scores
- **Ad_Position**: Designated location on pages for displaying advertisements
- **Impression**: Single display of an advertisement
- **Click_Through**: User click on an advertisement
- **Breadcrumb**: Navigation trail showing page hierarchy
- **Pagination**: System for dividing content lists into pages
- **Infinite_Scroll**: Feature loading more content as user scrolls
- **Carousel**: Rotating display of multiple items
- **Modal**: Overlay dialog box for focused interactions
- **Toast**: Temporary notification message
- **Skeleton_Loader**: Placeholder UI shown while content loads
- **Meta_Tag**: HTML metadata for SEO and social sharing
- **OG_Tag**: Open Graph metadata for social media previews
- **Canonical_URL**: Preferred URL for duplicate content
- **Sitemap**: XML file listing all site pages for search engines
- **Robots_File**: File instructing search engine crawlers
- **JSON_LD**: Structured data format for rich search results
- **Middleware**: Next.js request interceptor for auth and routing
- **Environment_Variable**: Secure configuration value stored outside code
- **Database_Migration**: Versioned database schema change
- **Seed_Data**: Initial database content for development/testing
- **Error_Boundary**: React component catching and handling errors
- **Loading_State**: UI displayed while data is being fetched
- **404_Page**: Page displayed when content is not found
- **500_Page**: Page displayed when server error occurs
- **Theme_System**: Feature allowing users to switch between light and dark visual themes
- **Light_Theme**: Visual theme with light background and dark text (default)
- **Dark_Theme**: Visual theme with dark background and light text
- **Theme_Toggle**: UI control for switching between light and dark themes
- **CSS_Variable**: Custom CSS property for dynamic theming
- **Dynamic_Configuration**: System allowing all site settings to be changed from admin panel without code deployment
- **Site_Logo**: Branding image displayed in header and footer
- **Favicon**: Small icon displayed in browser tab
- **Settings_Cache**: In-memory storage of site configuration for fast access
- **Test_Suite**: Collection of automated tests verifying system functionality
- **Unit_Test**: Test verifying individual function or component behavior
- **Integration_Test**: Test verifying interaction between system components
- **E2E_Test**: End-to-end test simulating complete user workflows
- **Playwright**: Browser automation tool for testing
- **Cypress**: JavaScript testing framework for web applications
- **Test_Coverage**: Percentage of code executed by tests
- **Visual_Regression_Test**: Test detecting unintended UI changes
- **Load_Test**: Test verifying performance under concurrent users
- **Penetration_Test**: Security test attempting to exploit vulnerabilities
- **CI_CD_Pipeline**: Automated build, test, and deployment workflow
- **Chromium**: Open-source browser engine used for testing
- **Viewport**: Visible area of web page in browser
- **Core_Web_Vitals**: Google's metrics for page performance (LCP, FID, CLS)
- **Devanagari**: Script used for writing Nepali language
- **Unicode**: Character encoding standard supporting international text
- **Font_Fallback**: Alternative font used when primary font is unavailable
- **Two_Factor_Authentication**: Additional security layer requiring second verification method (2FA)
- **JWT**: JSON Web Token for secure authentication
- **Bcrypt**: Password hashing algorithm with salt
- **DOMPurify**: Library for sanitizing HTML to prevent XSS attacks
- **Zod**: TypeScript-first schema validation library
- **Magic_Bytes**: File signature bytes identifying true file type
- **Directory_Traversal**: Attack attempting to access files outside intended directory
- **Account_Lockout**: Security measure blocking account after failed login attempts
- **Session_Fixation**: Attack hijacking user session
- **Subresource_Integrity**: Security feature verifying external resource integrity (SRI)
- **Clickjacking**: Attack tricking users into clicking hidden elements
- **Open_Redirect**: Vulnerability allowing redirection to malicious sites
- **Audit_Log**: Record of all system actions for security monitoring
- **IP_Whitelisting**: Restricting access to specific IP addresses
- **AES_256**: Advanced Encryption Standard with 256-bit key
- **SSL_TLS**: Secure Sockets Layer / Transport Layer Security for encrypted connections
- **Redis**: In-memory data store for caching and rate limiting
- **ClamAV**: Open-source antivirus engine for malware scanning
- **Sentry**: Error monitoring and tracking platform
- **Vercel_Analytics**: Performance monitoring service
- **Lighthouse**: Google's tool for measuring web page quality
- **NVDA**: Screen reader software for accessibility testing
- **JAWS**: Screen reader software for accessibility testing
- **VoiceOver**: Apple's screen reader for accessibility testing
- **Noto_Sans_Devanagari**: Google Font supporting Nepali script
- **Mukta**: Google Font supporting Devanagari script


## Requirements

### Requirement 1: Article Management System

**User Story:** As an admin, I want to create, edit, publish, and manage news articles with rich content, so that I can maintain the news portal's content effectively.

#### Acceptance Criteria

1. WHEN an admin creates a new article, THE Content_Management_System SHALL save the article with title, content, excerpt, featured image, category, tags, author, and publication status
2. WHEN an admin edits an existing article, THE Content_Management_System SHALL update the article and preserve the original creation timestamp
3. WHEN an admin publishes an article, THE Content_Management_System SHALL set the status to published and record the publication timestamp
4. WHEN an admin uploads a featured image, THE Image_Optimizer SHALL compress and generate responsive variants (thumbnail, medium, large)
5. THE Rich_Text_Editor SHALL support formatted text, headings, lists, links, embedded images, and embedded videos
6. WHEN an article is saved as draft, THE Content_Management_System SHALL make it visible only to admins
7. WHEN an article is published, THE News_Portal SHALL display it on the homepage and category pages according to publication date
8. THE Content_Management_System SHALL support bulk operations (publish, archive, delete) on multiple articles
9. WHEN an admin assigns categories and tags, THE Content_Management_System SHALL create the associations in the database
10. FOR ALL articles, the article slug SHALL be unique and URL-safe
11. WHEN an admin provides SEO metadata, THE Content_Management_System SHALL store seo_title, seo_description, and og_image
12. THE Content_Management_System SHALL provide article preview before publishing



### Requirement 2: Category and Tag System

**User Story:** As an admin, I want to organize articles into hierarchical categories and tag them with keywords, so that users can discover related content easily.

#### Acceptance Criteria

1. WHEN an admin creates a category, THE Content_Management_System SHALL save it with name (Nepali), name_en (English), slug, description, color, and sort_order
2. WHEN an admin creates a subcategory, THE Content_Management_System SHALL link it to a parent category via parent_id
3. THE Content_Management_System SHALL support unlimited category nesting depth
4. WHEN an admin assigns a color to a category, THE News_Portal SHALL display category tags in that color
5. WHEN an admin sets sort_order, THE News_Portal SHALL display categories in that order in navigation
6. WHEN an admin creates a tag, THE Content_Management_System SHALL save it with name and unique slug
7. THE Content_Management_System SHALL support many-to-many relationships between articles and tags
8. WHEN a user clicks a category, THE News_Portal SHALL display all articles in that category and its subcategories
9. WHEN a user clicks a tag, THE News_Portal SHALL display all articles with that tag
10. THE Content_Management_System SHALL prevent deletion of categories that have articles
11. FOR ALL category slugs and tag slugs, they SHALL be unique across the system



### Requirement 3: User Authentication and Authorization

**User Story:** As a user, I want to register and log in securely using email/password or Google OAuth, so that I can comment on articles and access personalized features.

#### Acceptance Criteria

1. WHEN a user registers with email and password, THE Session_Manager SHALL create a user account with hashed password
2. WHEN a user logs in with valid credentials, THE Session_Manager SHALL create an authenticated session
3. WHEN a user logs in with Google OAuth, THE OAuth_Provider SHALL authenticate and THE Session_Manager SHALL create or update the user account
4. WHEN a user logs out, THE Session_Manager SHALL terminate the session
5. THE Session_Manager SHALL support role-based access control (admin, editor, author, reader)
6. WHEN an unauthenticated user attempts to comment, THE News_Portal SHALL redirect to login page
7. WHEN an authenticated user accesses admin routes, THE Middleware SHALL verify admin role before allowing access
8. THE Session_Manager SHALL use secure HTTP-only cookies for session tokens
9. WHEN a user fails login three times within 15 minutes, THE Rate_Limiter SHALL temporarily block further attempts
10. THE Session_Manager SHALL expire sessions after 30 days of inactivity
11. WHEN a user changes password, THE Session_Manager SHALL invalidate all existing sessions except the current one
12. THE OAuth_Provider SHALL securely store OAuth tokens and refresh them when expired



### Requirement 4: Comment System with Voting

**User Story:** As a user, I want to post comments on articles and vote on other comments, so that I can engage in discussions about news content.

#### Acceptance Criteria

1. WHEN an authenticated user submits a comment, THE Comment_System SHALL save it with article_id, user_id, content, and timestamp
2. WHEN a user submits a reply to a comment, THE Comment_System SHALL save it with parent_id linking to the original comment
3. WHEN a user votes on a comment, THE Comment_System SHALL increment either likes or dislikes count
4. WHEN a user votes on a comment they already voted on, THE Comment_System SHALL toggle the vote (remove or change)
5. THE Comment_System SHALL display comments in chronological order with newest first
6. THE Comment_System SHALL display nested replies indented under parent comments
7. WHEN an admin moderates comments, THE Content_Management_System SHALL allow approve, reject, or mark as spam
8. WHEN a comment is pending, THE News_Portal SHALL display it only to the author and admins
9. WHEN a comment is approved, THE News_Portal SHALL display it publicly
10. THE Comment_System SHALL sanitize comment content to prevent XSS attacks
11. WHEN a user posts more than 5 comments within 1 minute, THE Rate_Limiter SHALL reject further comments temporarily
12. THE Comment_System SHALL display commenter name, avatar, and timestamp with each comment



### Requirement 5: Responsive Design System

**User Story:** As a user, I want the website to work seamlessly on my mobile phone, tablet, and desktop computer, so that I can read news on any device.

#### Acceptance Criteria

1. THE Responsive_Design SHALL adapt layout for mobile (<768px), tablet (768px-1199px), and desktop (≥1200px) breakpoints
2. WHEN viewed on mobile, THE News_Portal SHALL display a hamburger menu instead of full navigation
3. WHEN viewed on mobile, THE News_Portal SHALL stack content in single-column layout
4. WHEN viewed on tablet, THE News_Portal SHALL display two-column grid layouts
5. WHEN viewed on desktop, THE News_Portal SHALL display multi-column layouts with sidebar
6. THE Image_Optimizer SHALL serve appropriately sized images based on device screen width
7. THE News_Portal SHALL use touch-friendly tap targets (minimum 44×44 pixels) on mobile
8. WHEN a user rotates their device, THE Responsive_Design SHALL adjust layout to new orientation
9. THE News_Portal SHALL load mobile-specific ad slots on mobile devices
10. THE News_Portal SHALL maintain consistent typography scale across all breakpoints
11. THE Responsive_Design SHALL ensure all interactive elements are accessible via touch and mouse
12. FOR ALL pages, the design SHALL be consistent and professional across all device sizes



### Requirement 6: Multi-Language Support

**User Story:** As a user, I want to read content in Nepali or English, so that I can consume news in my preferred language.

#### Acceptance Criteria

1. THE News_Portal SHALL support Nepali as the primary language and English as the secondary language
2. THE News_Portal SHALL default to Nepali language on first visit
3. WHEN a user selects a language, THE News_Portal SHALL display all UI elements in that language
4. THE News_Portal SHALL persist language preference in browser storage
5. WHEN an admin creates content, THE Content_Management_System SHALL allow separate Nepali and English versions
6. THE Nepali_Calendar SHALL display dates in Nepali format (e.g., "२०८२ वैशाख १३, शनिबार")
7. THE News_Portal SHALL display relative timestamps in the selected language (e.g., "२ घण्टा अगाडि" or "2 hours ago")
8. WHEN a user switches language, THE News_Portal SHALL reload the current page in the new language
9. THE News_Portal SHALL use Devanagari numerals for Nepali content
10. THE News_Portal SHALL support proper Nepali Unicode rendering with correct font fallbacks
11. THE News_Portal SHALL display all navigation menus, buttons, labels, and error messages in the selected language
12. THE News_Portal SHALL support Nepali text input in forms, search, and comments
13. THE SEO_System SHALL generate language-specific meta tags and hreflang attributes
14. THE News_Portal SHALL display category names in the selected language
15. THE News_Portal SHALL maintain language consistency across all pages and components



### Requirement 7: Homepage Layout and Sections

**User Story:** As a user, I want to see a well-organized homepage with featured articles, breaking news, and categorized content sections, so that I can quickly find interesting news.

#### Acceptance Criteria

1. THE News_Portal SHALL display a Breaking_News_Ticker at the top showing latest urgent news
2. THE News_Portal SHALL display a hero section with 1-2 featured articles with large images
3. THE News_Portal SHALL display a news section (समाचार) with lead article and grid of recent articles
4. THE News_Portal SHALL display a feature section (फिचर) with curated long-form content
5. THE News_Portal SHALL display a cover story section (कभर स्टोरी) with in-depth reporting
6. THE News_Portal SHALL display a weekend section (सप्ताहान्त) with lifestyle content
7. THE News_Portal SHALL display a technology section (प्रविधि) with tech news
8. THE News_Portal SHALL display an interview section (अन्तर्वार्ता) with featured interviews
9. THE News_Portal SHALL display a sports section with live scores and sports news
10. THE News_Portal SHALL display an international section (अन्तर्राष्ट्रिय) with world news
11. THE News_Portal SHALL display a video section with embedded videos
12. THE News_Portal SHALL display an OK_Reels section with horizontal scrolling video thumbnails
13. THE News_Portal SHALL display a photo gallery section with image collections
14. THE News_Portal SHALL display a "missed section" (छुटाउनुभयो कि ?) with older popular articles
15. THE News_Portal SHALL display advertisement slots between content sections
16. THE News_Portal SHALL display a right sidebar with trending articles, recent articles, and ads on desktop
17. WHEN an admin marks an article as featured, THE News_Portal SHALL display it in the hero section
18. THE News_Portal SHALL load homepage sections progressively for better performance



### Requirement 8: Article Display Page

**User Story:** As a user, I want to read full articles with rich formatting, images, author information, and related content, so that I can consume news effectively.

#### Acceptance Criteria

1. WHEN a user visits an article URL, THE News_Portal SHALL display the article with title, featured image, author, date, and content
2. THE News_Portal SHALL display a breadcrumb navigation showing Home > Category > Article Title
3. THE News_Portal SHALL display category tag badge with the category's assigned color
4. THE News_Portal SHALL display author avatar, name, and link to author profile
5. THE News_Portal SHALL display publication date in Nepali calendar format
6. THE News_Portal SHALL display view count for the article
7. THE News_Portal SHALL display social share buttons (Facebook, Twitter, Copy Link)
8. THE News_Portal SHALL render article content with proper formatting, images, and embedded media
9. THE News_Portal SHALL display article tags as clickable links
10. THE News_Portal SHALL display a comment section below the article
11. THE News_Portal SHALL display related articles at the bottom
12. THE News_Portal SHALL display a sidebar with trending and recent articles on desktop
13. WHEN a user views an article, THE View_Counter SHALL increment the view count
14. THE News_Portal SHALL display article metadata (author, date, views) in a consistent format
15. WHERE an AI summary exists, THE News_Portal SHALL display it above the article body
16. THE News_Portal SHALL display advertisement slots within and around the article



### Requirement 9: Navigation System

**User Story:** As a user, I want to navigate through different sections and categories easily, so that I can find the content I'm interested in.

#### Acceptance Criteria

1. THE News_Portal SHALL display a primary navigation menu with main categories (समाचार, राजनीति, अर्थ/व्यापार, खेलकुद, मनोरञ्जन, जीवनशैली, प्रविधि, विचार, अन्तर्राष्ट्रिय, अन्य)
2. WHEN a user hovers over a navigation item, THE Mega_Menu SHALL display a dropdown with featured articles and subcategories
3. THE Mega_Menu SHALL display 4-6 featured article thumbnails with titles and category tags
4. THE Mega_Menu SHALL display a sidebar with subcategory links
5. WHEN viewed on mobile, THE News_Portal SHALL display a hamburger menu icon
6. WHEN a user taps the hamburger menu, THE News_Portal SHALL display a slide-out navigation panel
7. THE News_Portal SHALL display a search bar in the header
8. THE News_Portal SHALL highlight the active navigation item based on current page
9. THE News_Portal SHALL display a logo linking to the homepage
10. THE News_Portal SHALL display a top bar with date, language switcher, and social media links
11. THE News_Portal SHALL display a footer with quick links, category links, contact information, and social media icons
12. THE News_Portal SHALL display consistent navigation across all pages



### Requirement 10: Search Functionality

**User Story:** As a user, I want to search for articles by keywords, so that I can find specific news content quickly.

#### Acceptance Criteria

1. WHEN a user enters a search query, THE Search_Engine SHALL return articles matching the query in title, content, or tags
2. THE Search_Engine SHALL support full-text search in both Nepali and English
3. THE Search_Engine SHALL rank results by relevance (title matches higher than content matches)
4. THE Search_Engine SHALL display search results with article thumbnail, title, excerpt, category, and date
5. THE Search_Engine SHALL support pagination of search results
6. WHEN no results are found, THE News_Portal SHALL display a "no results" message with suggestions
7. THE Search_Engine SHALL highlight search terms in result snippets
8. THE Search_Engine SHALL support filtering results by category and date range
9. WHEN a user searches, THE Search_Engine SHALL return results within 500 milliseconds for typical queries
10. THE Search_Engine SHALL sanitize search queries to prevent SQL injection
11. THE News_Portal SHALL display recent popular searches as suggestions



### Requirement 11: Sports Section with Live Scores

**User Story:** As a sports fan, I want to see live match scores, league tables, and sports news, so that I can stay updated on sports events.

#### Acceptance Criteria

1. THE Live_Score_System SHALL display match cards with team names, logos, scores, and match status
2. THE Live_Score_System SHALL display match date and time in Nepali calendar format
3. THE Live_Score_System SHALL support multiple tournaments with tab navigation
4. WHEN an admin updates a match score, THE Live_Score_System SHALL reflect the change immediately
5. THE Live_Score_System SHALL display match status (upcoming, live, completed)
6. THE Live_Score_System SHALL display result indicators (WON, LOST, DRAW)
7. THE Live_Score_System SHALL display matches in a horizontal scrolling carousel
8. THE News_Portal SHALL display league tables for cricket and football
9. THE News_Portal SHALL display sports news articles below the live scores
10. WHEN a user clicks a match card, THE News_Portal SHALL display detailed match information
11. THE Content_Management_System SHALL allow admins to create and manage tournaments
12. THE Content_Management_System SHALL allow admins to add and update match scores
13. THE Content_Management_System SHALL allow admins to upload team logos



### Requirement 12: OK Reels Video Feature

**User Story:** As a user, I want to watch short-form video content (reels), so that I can consume news in video format.

#### Acceptance Criteria

1. WHEN an admin uploads a reel, THE Content_Management_System SHALL save it with title, slug, thumbnail, video_url, and duration
2. THE News_Portal SHALL display reels in a horizontal scrolling section on the homepage
3. WHEN a user clicks a reel thumbnail, THE News_Portal SHALL navigate to the reel page
4. THE News_Portal SHALL display a video player with the reel content
5. THE News_Portal SHALL display reel title, view count, and publication date
6. WHEN a user views a reel, THE View_Counter SHALL increment the view count
7. THE News_Portal SHALL display related reels below the current reel
8. THE Content_Management_System SHALL allow admins to activate or deactivate reels
9. THE News_Portal SHALL display only active reels to users
10. THE News_Portal SHALL support video formats (MP4, WebM)
11. THE News_Portal SHALL display a play indicator icon on reel thumbnails



### Requirement 13: Photo Gallery Feature

**User Story:** As a user, I want to view photo galleries with multiple images, so that I can see visual news stories.

#### Acceptance Criteria

1. WHEN an admin creates a gallery, THE Content_Management_System SHALL save it with title, description, category, and images array
2. THE Content_Management_System SHALL allow admins to upload multiple images to a gallery
3. THE News_Portal SHALL display gallery thumbnails on the homepage and category pages
4. WHEN a user clicks a gallery, THE News_Portal SHALL display the gallery page with all images
5. THE News_Portal SHALL display images in a grid layout
6. WHEN a user clicks an image, THE News_Portal SHALL open a lightbox viewer
7. THE News_Portal SHALL support navigation between images in the lightbox (previous/next)
8. THE News_Portal SHALL display image captions in the lightbox
9. THE News_Portal SHALL display gallery title, description, and publication date
10. THE Image_Optimizer SHALL generate thumbnails and optimized versions of gallery images
11. THE News_Portal SHALL support lazy loading of gallery images



### Requirement 14: Advertisement Management System

**User Story:** As an admin, I want to manage advertisements across the site, so that I can monetize the news portal effectively.

#### Acceptance Criteria

1. WHEN an admin creates an ad position, THE Advertisement_System SHALL save it with alias, description, page, and device_type
2. WHEN an admin creates an advertisement, THE Advertisement_System SHALL save it with position_id, content_type, content, target_url, start_date, end_date, and priority
3. THE Advertisement_System SHALL support image ads, HTML ads, and script-based ads
4. THE Advertisement_System SHALL display ads only within their active date range
5. WHEN multiple ads are assigned to the same position, THE Advertisement_System SHALL display the highest priority ad
6. THE Advertisement_System SHALL track impressions when an ad is displayed
7. WHEN a user clicks an ad, THE Advertisement_System SHALL track the click and redirect to target_url
8. THE Advertisement_System SHALL display desktop ads only on desktop devices
9. THE Advertisement_System SHALL display mobile ads only on mobile devices
10. THE Advertisement_System SHALL display ads at designated positions (header, sidebar, in-article, footer)
11. THE Content_Management_System SHALL display ad performance metrics (impressions, clicks, CTR)
12. THE Advertisement_System SHALL support ad rotation when multiple ads have the same priority
13. THE Advertisement_System SHALL lazy load ads below the fold for better performance



### Requirement 15: Analytics Dashboard

**User Story:** As an admin, I want to view site analytics and performance metrics, so that I can understand user engagement and content performance.

#### Acceptance Criteria

1. THE Analytics_Dashboard SHALL display total articles, total views, and total comments for today
2. THE Analytics_Dashboard SHALL display a chart of page views over the last 30 days
3. THE Analytics_Dashboard SHALL display top 10 articles by views
4. THE Analytics_Dashboard SHALL display top 10 articles by comments
5. THE Analytics_Dashboard SHALL display traffic sources (direct, search, social, referral)
6. THE Analytics_Dashboard SHALL display device breakdown (mobile, tablet, desktop)
7. THE Analytics_Dashboard SHALL display user engagement metrics (average time on page, bounce rate)
8. THE Analytics_Dashboard SHALL allow filtering metrics by date range
9. THE Analytics_Dashboard SHALL display real-time active users count
10. THE Analytics_Dashboard SHALL display category performance (views per category)
11. THE Analytics_Dashboard SHALL display author performance (views per author)
12. WHEN an admin views the dashboard, THE Analytics_Dashboard SHALL load metrics within 2 seconds



### Requirement 16: SEO Optimization

**User Story:** As a site owner, I want the website to be optimized for search engines, so that articles rank well and attract organic traffic.

#### Acceptance Criteria

1. THE SEO_System SHALL generate unique meta title and description for each page
2. THE SEO_System SHALL generate Open Graph tags (og:title, og:description, og:image, og:url, og:type) for social sharing
3. THE SEO_System SHALL generate Twitter Card tags for Twitter sharing
4. THE SEO_System SHALL generate canonical URLs for all pages
5. THE SEO_System SHALL generate structured data (JSON-LD) for articles with author, date, image, and publisher
6. THE SEO_System SHALL generate an XML sitemap listing all published articles and pages
7. THE SEO_System SHALL generate a robots.txt file with crawl directives
8. THE SEO_System SHALL generate hreflang tags for multi-language content
9. THE News_Portal SHALL use semantic HTML5 elements (article, section, nav, header, footer)
10. THE News_Portal SHALL generate SEO-friendly URLs with slugs instead of IDs
11. THE Image_Optimizer SHALL add alt text to all images
12. THE News_Portal SHALL implement proper heading hierarchy (h1, h2, h3)
13. THE SEO_System SHALL generate RSS feeds for articles and comments



### Requirement 17: Security Implementation

**User Story:** As a site owner, I want the website to be secure against common web vulnerabilities, so that user data and site integrity are protected.

#### Acceptance Criteria

1. THE Session_Manager SHALL hash all passwords using bcrypt with salt rounds ≥ 10
2. THE Session_Manager SHALL use HTTP-only, secure, SameSite cookies for session tokens
3. THE News_Portal SHALL implement CSRF protection on all state-changing API endpoints using tokens
4. THE XSS_Filter SHALL sanitize all user-generated content (comments, search queries) before rendering using DOMPurify or similar
5. THE Prisma_Client SHALL use parameterized queries to prevent SQL injection (built-in protection)
6. THE Rate_Limiter SHALL limit API requests to 100 per minute per IP address
7. THE Rate_Limiter SHALL limit login attempts to 5 per 15 minutes per IP address
8. THE File_Upload_Validator SHALL validate file types, sizes, and content before accepting uploads
9. THE File_Upload_Validator SHALL reject executable files and scripts (.exe, .sh, .bat, .js, .php)
10. THE News_Portal SHALL set security headers (X-Frame-Options: DENY, X-Content-Type-Options: nosniff, Strict-Transport-Security: max-age=31536000)
11. THE News_Portal SHALL validate and sanitize all environment variables on startup
12. THE Middleware SHALL verify authentication tokens on protected routes using JWT or session validation
13. THE News_Portal SHALL log security events (failed logins, rate limit violations, suspicious activities) for monitoring
14. THE Database_Connection_Pool SHALL use encrypted connections to PostgreSQL (SSL/TLS)
15. THE News_Portal SHALL implement Content Security Policy headers to prevent XSS and data injection
16. THE News_Portal SHALL implement input validation on all API endpoints using Zod or Yup schemas
17. THE News_Portal SHALL implement output encoding for all dynamic content rendering
18. THE News_Portal SHALL use secure random token generation for password resets and email verification
19. THE News_Portal SHALL implement account lockout after 10 failed login attempts within 1 hour
20. THE News_Portal SHALL require strong passwords (minimum 8 characters, uppercase, lowercase, number, special character)
21. THE News_Portal SHALL implement password reset with time-limited tokens (valid for 1 hour)
22. THE News_Portal SHALL implement email verification for new user registrations
23. THE News_Portal SHALL sanitize file names to prevent directory traversal attacks
24. THE News_Portal SHALL implement secure file storage with randomized file names
25. THE News_Portal SHALL validate image files by checking magic bytes, not just extensions
26. THE News_Portal SHALL implement API authentication using Bearer tokens or API keys for external integrations
27. THE News_Portal SHALL implement role-based access control (RBAC) with principle of least privilege
28. THE News_Portal SHALL encrypt sensitive data at rest (user emails, OAuth tokens) using AES-256
29. THE News_Portal SHALL implement secure session management with automatic timeout after 30 minutes of inactivity
30. THE News_Portal SHALL implement HTTPS redirect for all HTTP requests
31. THE News_Portal SHALL implement subresource integrity (SRI) for external scripts and stylesheets
32. THE News_Portal SHALL implement clickjacking protection using X-Frame-Options and CSP frame-ancestors
33. THE News_Portal SHALL implement protection against open redirect vulnerabilities
34. THE News_Portal SHALL implement secure cookie attributes (HttpOnly, Secure, SameSite=Strict)
35. THE News_Portal SHALL implement database backup encryption and secure storage
36. THE News_Portal SHALL implement audit logging for all admin actions (create, update, delete)
37. THE News_Portal SHALL implement IP whitelisting for admin panel access (optional configuration)
38. THE News_Portal SHALL implement two-factor authentication (2FA) for admin accounts
39. THE News_Portal SHALL implement security monitoring and alerting for suspicious activities
40. THE News_Portal SHALL regularly update dependencies to patch security vulnerabilities



### Requirement 18: Performance Optimization

**User Story:** As a user, I want the website to load quickly and respond smoothly, so that I have a good browsing experience.

#### Acceptance Criteria

1. THE News_Portal SHALL achieve a Lighthouse performance score ≥ 90 on desktop
2. THE News_Portal SHALL achieve a Lighthouse performance score ≥ 80 on mobile
3. THE News_Portal SHALL load the homepage within 2 seconds on 4G connection
4. THE News_Portal SHALL load article pages within 1.5 seconds on 4G connection
5. THE Image_Optimizer SHALL serve images in modern formats (WebP, AVIF) with fallbacks
6. THE Image_Optimizer SHALL implement responsive images with srcset and sizes attributes
7. THE News_Portal SHALL implement lazy loading for images below the fold
8. THE News_Portal SHALL use ISR (Incremental Static Regeneration) for article pages with 60-second revalidation
9. THE News_Portal SHALL use SSR (Server-Side Rendering) for the homepage
10. THE News_Portal SHALL implement code splitting per route
11. THE News_Portal SHALL prefetch critical resources and preload above-the-fold images
12. THE News_Portal SHALL minimize JavaScript bundle size to < 200KB (gzipped)
13. THE News_Portal SHALL implement database query optimization with proper indexes
14. THE Database_Connection_Pool SHALL reuse connections efficiently
15. THE News_Portal SHALL implement caching headers for static assets (1 year)
16. THE News_Portal SHALL compress responses with gzip or brotli



### Requirement 19: Admin Panel Dashboard

**User Story:** As an admin, I want a comprehensive dashboard to manage all aspects of the news portal, so that I can efficiently operate the website.

#### Acceptance Criteria

1. THE Content_Management_System SHALL display a sidebar navigation with sections (Dashboard, Articles, Categories, Authors, Comments, Ads, Sports, Reels, Galleries, Analytics, Settings, Users, Breaking News)
2. THE Content_Management_System SHALL display the dashboard with quick stats and recent activity
3. THE Content_Management_System SHALL provide quick actions (New Article, Add Breaking News)
4. THE Content_Management_System SHALL display a data table for articles with sorting, filtering, and pagination
5. THE Content_Management_System SHALL support bulk operations on selected items
6. THE Content_Management_System SHALL provide inline editing for simple fields
7. THE Content_Management_System SHALL display confirmation dialogs for destructive actions
8. THE Content_Management_System SHALL display success and error notifications using toasts
9. THE Content_Management_System SHALL maintain consistent design language across all admin pages
10. THE Content_Management_System SHALL be responsive and usable on tablets
11. THE Content_Management_System SHALL display loading states during data operations
12. THE Content_Management_System SHALL validate form inputs before submission



### Requirement 20: Breaking News Management

**User Story:** As an admin, I want to quickly publish breaking news to the ticker, so that users see urgent news immediately.

#### Acceptance Criteria

1. WHEN an admin creates breaking news, THE Content_Management_System SHALL save it with title, optional article_id, and expiry time
2. THE Breaking_News_Ticker SHALL display active breaking news in a horizontal scrolling banner
3. THE Breaking_News_Ticker SHALL auto-scroll continuously from right to left
4. WHEN a user clicks breaking news text, THE News_Portal SHALL navigate to the linked article
5. WHEN breaking news expires, THE Breaking_News_Ticker SHALL stop displaying it
6. THE Content_Management_System SHALL allow admins to activate or deactivate breaking news manually
7. THE Breaking_News_Ticker SHALL display multiple breaking news items separated by a separator icon
8. THE Content_Management_System SHALL display a list of active and expired breaking news
9. THE Content_Management_System SHALL allow admins to set expiry time when creating breaking news
10. THE Breaking_News_Ticker SHALL be visible on all pages



### Requirement 21: Author Management and Profiles

**User Story:** As an admin, I want to manage author profiles, so that articles display proper author attribution and users can view author pages.

#### Acceptance Criteria

1. WHEN an admin creates an author, THE Content_Management_System SHALL save it with name, email, avatar, bio, role, and social links
2. THE Content_Management_System SHALL support author roles (editor, reporter, columnist)
3. WHEN a user clicks an author name, THE News_Portal SHALL display the author profile page
4. THE News_Portal SHALL display author avatar, name, bio, and social media links on the profile page
5. THE News_Portal SHALL display all articles by the author on the profile page
6. THE News_Portal SHALL display article count and total views for the author
7. THE Content_Management_System SHALL allow admins to activate or deactivate authors
8. THE News_Portal SHALL display only active authors in article bylines
9. THE Content_Management_System SHALL prevent deletion of authors who have published articles
10. THE News_Portal SHALL display author information consistently across article cards and article pages



### Requirement 22: Category Archive Pages

**User Story:** As a user, I want to view all articles in a specific category, so that I can browse news by topic.

#### Acceptance Criteria

1. WHEN a user navigates to a category URL, THE News_Portal SHALL display the category archive page
2. THE News_Portal SHALL display the category name as the page heading
3. THE News_Portal SHALL display articles in the category sorted by publication date (newest first)
4. THE News_Portal SHALL display 15 articles per page with pagination
5. THE News_Portal SHALL display article cards with thumbnail, title, excerpt, author, and date
6. THE News_Portal SHALL include articles from subcategories in the category archive
7. THE News_Portal SHALL display a sidebar with trending articles and ads on desktop
8. THE News_Portal SHALL display breadcrumb navigation (Home > Category Name)
9. THE News_Portal SHALL display category description if available
10. WHEN no articles exist in a category, THE News_Portal SHALL display a "no articles" message
11. THE News_Portal SHALL support infinite scroll as an alternative to pagination



### Requirement 23: User Profile and Bookmarks

**User Story:** As a registered user, I want to manage my profile and bookmark articles, so that I can save articles for later reading.

#### Acceptance Criteria

1. WHEN a user accesses their profile, THE News_Portal SHALL display their name, email, avatar, and registration date
2. THE News_Portal SHALL allow users to update their name and avatar
3. THE News_Portal SHALL allow users to change their password
4. WHEN a user bookmarks an article, THE Bookmark_System SHALL save the association
5. WHEN a user unbookmarks an article, THE Bookmark_System SHALL remove the association
6. THE News_Portal SHALL display a bookmark icon on article pages
7. THE News_Portal SHALL indicate bookmarked status with a filled icon
8. THE News_Portal SHALL display all bookmarked articles on the user profile page
9. THE News_Portal SHALL display bookmark count on the user profile
10. THE News_Portal SHALL allow users to remove bookmarks from their profile page
11. THE Bookmark_System SHALL support pagination for users with many bookmarks



### Requirement 24: Trending and Popular Content

**User Story:** As a user, I want to see trending and popular articles, so that I can discover what others are reading.

#### Acceptance Criteria

1. THE Trending_Algorithm SHALL calculate trending articles based on views in the last 24 hours
2. THE News_Portal SHALL display trending articles in the sidebar on desktop
3. THE News_Portal SHALL display top 5 trending articles with thumbnails and titles
4. THE Trending_Algorithm SHALL update trending articles every 15 minutes
5. THE News_Portal SHALL display "most read" articles based on total view count
6. THE News_Portal SHALL display "most commented" articles based on comment count
7. THE News_Portal SHALL display trending articles section on the homepage
8. THE News_Portal SHALL display trending indicator (fire icon or "trending" badge) on trending articles
9. THE Trending_Algorithm SHALL exclude articles older than 7 days from trending calculation
10. THE News_Portal SHALL display trending articles in a consistent card format



### Requirement 25: Error Handling and User Feedback

**User Story:** As a user, I want clear error messages and feedback when something goes wrong, so that I understand what happened and what to do next.

#### Acceptance Criteria

1. WHEN a page is not found, THE News_Portal SHALL display a custom 404_Page with navigation links
2. WHEN a server error occurs, THE News_Portal SHALL display a custom 500_Page with a friendly message
3. WHEN an API request fails, THE News_Portal SHALL display an error toast notification
4. WHEN a form submission succeeds, THE News_Portal SHALL display a success toast notification
5. WHEN a form has validation errors, THE News_Portal SHALL display inline error messages below each field
6. THE Error_Boundary SHALL catch React errors and display a fallback UI
7. THE News_Portal SHALL log errors to the server for debugging
8. WHEN content is loading, THE News_Portal SHALL display Skeleton_Loader placeholders
9. WHEN an image fails to load, THE News_Portal SHALL display a placeholder image
10. THE News_Portal SHALL display user-friendly error messages (not technical stack traces)
11. THE News_Portal SHALL provide actionable next steps in error messages (e.g., "Try again" button)
12. WHEN a user loses internet connection, THE News_Portal SHALL display an offline indicator



### Requirement 26: Database Schema and Data Integrity

**User Story:** As a developer, I want a well-designed database schema with proper relationships and constraints, so that data integrity is maintained.

#### Acceptance Criteria

1. THE Prisma_Client SHALL define all tables with primary keys (id SERIAL PRIMARY KEY)
2. THE Prisma_Client SHALL define foreign key relationships with appropriate ON DELETE actions
3. THE Prisma_Client SHALL define unique constraints on slug fields (articles, categories, tags)
4. THE Prisma_Client SHALL define unique constraints on email fields (users, authors)
5. THE Prisma_Client SHALL define NOT NULL constraints on required fields
6. THE Prisma_Client SHALL define default values for status, timestamps, and counters
7. THE Prisma_Client SHALL create indexes on frequently queried fields (category_id, author_id, published_at, slug)
8. THE Prisma_Client SHALL support many-to-many relationships (articles-tags) with junction tables
9. THE Prisma_Client SHALL use TIMESTAMPTZ for all timestamp fields
10. THE Prisma_Client SHALL use JSONB for flexible data structures (ai_summary, social_links, images)
11. THE Database_Migration SHALL be versioned and reversible
12. THE Prisma_Client SHALL validate data types before database operations



### Requirement 27: Media Upload and Management

**User Story:** As an admin, I want to upload and manage images and videos, so that I can include media in articles and galleries.

#### Acceptance Criteria

1. WHEN an admin uploads an image, THE File_Upload_Validator SHALL verify the file type is image/jpeg, image/png, image/webp, or image/gif
2. WHEN an admin uploads an image, THE File_Upload_Validator SHALL verify the file size is ≤ 10MB
3. WHEN an admin uploads a video, THE File_Upload_Validator SHALL verify the file type is video/mp4 or video/webm
4. WHEN an admin uploads a video, THE File_Upload_Validator SHALL verify the file size is ≤ 100MB
5. THE Content_Management_System SHALL store uploaded files with unique filenames to prevent collisions
6. THE Image_Optimizer SHALL generate multiple sizes (thumbnail, medium, large) for uploaded images
7. THE Content_Management_System SHALL display a media library with all uploaded files
8. THE Content_Management_System SHALL allow admins to search and filter media files
9. THE Content_Management_System SHALL allow admins to delete unused media files
10. THE Content_Management_System SHALL display file metadata (size, dimensions, upload date)
11. THE Content_Management_System SHALL support drag-and-drop file upload
12. THE Content_Management_System SHALL display upload progress during file upload
13. THE File_Upload_Validator SHALL scan uploaded files for malware signatures



### Requirement 28: Dynamic Site Configuration Management

**User Story:** As an admin, I want to configure all site-wide settings dynamically without code changes, so that I can customize the portal's branding, content, and behavior from the admin panel.

#### Acceptance Criteria

1. THE Content_Management_System SHALL allow admins to upload and update the site logo (header logo, mobile logo, favicon)
2. THE Content_Management_System SHALL allow admins to update site name and tagline in both Nepali and English
3. THE Content_Management_System SHALL allow admins to update contact information (phone, email, address, registration number)
4. THE Content_Management_System SHALL allow admins to update social media links (Facebook, Twitter, YouTube, Instagram)
5. THE Content_Management_System SHALL allow admins to update footer content including copyright text, chairman name, chief editor name
6. THE Content_Management_System SHALL allow admins to configure homepage section order via drag-and-drop
7. THE Content_Management_System SHALL allow admins to set default SEO metadata (title template, description, OG image)
8. THE Content_Management_System SHALL allow admins to configure items per page for listings
9. THE Content_Management_System SHALL allow admins to enable or disable features (comments, bookmarks, reels, galleries)
10. THE Content_Management_System SHALL allow admins to configure breaking news ticker speed and behavior
11. THE Content_Management_System SHALL allow admins to set default language (Nepali or English)
12. THE Content_Management_System SHALL allow admins to set default theme (light or dark)
13. THE Content_Management_System SHALL store settings in the site_settings table as key-value pairs
14. THE News_Portal SHALL load settings on startup and cache them in memory
15. WHEN an admin updates settings, THE News_Portal SHALL apply changes immediately without restart
16. THE News_Portal SHALL dynamically render logo, site name, footer content, and all configurable elements from database
17. THE Content_Management_System SHALL validate uploaded logos for file type (PNG, SVG, JPG) and size (≤ 2MB)
18. THE Content_Management_System SHALL provide preview of settings changes before saving
19. THE Content_Management_System SHALL maintain settings history for rollback capability



### Requirement 29: Accessibility Compliance

**User Story:** As a user with disabilities, I want the website to be accessible, so that I can consume news content regardless of my abilities.

#### Acceptance Criteria

1. THE News_Portal SHALL use semantic HTML5 elements for proper document structure
2. THE News_Portal SHALL provide alt text for all meaningful images
3. THE News_Portal SHALL support keyboard navigation for all interactive elements
4. THE News_Portal SHALL display visible focus indicators on interactive elements
5. THE News_Portal SHALL maintain color contrast ratio ≥ 4.5:1 for normal text
6. THE News_Portal SHALL maintain color contrast ratio ≥ 3:1 for large text
7. THE News_Portal SHALL use ARIA labels for icon-only buttons
8. THE News_Portal SHALL use ARIA live regions for dynamic content updates (breaking news ticker)
9. THE News_Portal SHALL support screen reader navigation with proper heading hierarchy
10. THE News_Portal SHALL provide skip-to-content links
11. THE News_Portal SHALL ensure form inputs have associated labels
12. THE News_Portal SHALL not rely solely on color to convey information
13. THE News_Portal SHALL support browser zoom up to 200% without breaking layout



### Requirement 30: API Design and Documentation

**User Story:** As a developer, I want well-designed RESTful APIs with clear documentation, so that I can integrate with the news portal or build additional features.

#### Acceptance Criteria

1. THE API_Route SHALL follow REST conventions (GET for read, POST for create, PUT/PATCH for update, DELETE for delete)
2. THE API_Route SHALL return appropriate HTTP status codes (200, 201, 400, 401, 403, 404, 500)
3. THE API_Route SHALL return JSON responses with consistent structure { success, data, error }
4. THE API_Route SHALL validate request bodies and return detailed validation errors
5. THE API_Route SHALL require authentication for protected endpoints
6. THE API_Route SHALL implement pagination for list endpoints with limit and offset parameters
7. THE API_Route SHALL support filtering and sorting via query parameters
8. THE API_Route SHALL include CORS headers for allowed origins
9. THE API_Route SHALL implement rate limiting per endpoint
10. THE API_Route SHALL log all requests with timestamp, method, path, status, and duration
11. THE News_Portal SHALL provide API documentation with endpoint descriptions, parameters, and examples
12. THE API_Route SHALL version endpoints (e.g., /api/v1/articles) for backward compatibility


### Requirement 31: Theme System (Light/Dark Mode)

**User Story:** As a user, I want to switch between light and dark themes, so that I can read comfortably in different lighting conditions.

#### Acceptance Criteria

1. THE News_Portal SHALL support light theme and dark theme
2. THE News_Portal SHALL default to light theme on first visit
3. WHEN a user toggles theme, THE News_Portal SHALL switch between light and dark mode
4. THE News_Portal SHALL persist theme preference in browser storage
5. THE News_Portal SHALL apply theme consistently across all pages and components
6. THE News_Portal SHALL use CSS variables for theme colors (background, text, borders, shadows)
7. THE News_Portal SHALL define light theme colors: white background (#ffffff), dark text (#1a1a1a), light gray borders (#e5e5e5)
8. THE News_Portal SHALL define dark theme colors: dark background (#1a1a1a), light text (#f5f5f5), dark gray borders (#333333)
9. THE News_Portal SHALL maintain proper contrast ratios (≥ 4.5:1) in both themes for accessibility
10. THE News_Portal SHALL smoothly transition between themes with CSS transitions (200ms)
11. THE News_Portal SHALL display a theme toggle button in the header (sun/moon icon)
12. THE News_Portal SHALL respect user's system theme preference (prefers-color-scheme) if no preference is saved
13. THE News_Portal SHALL apply theme to all UI elements (cards, buttons, forms, modals, navigation, footer)
14. THE News_Portal SHALL adjust image brightness/opacity in dark mode for better readability
15. THE News_Portal SHALL maintain brand colors (category tags, primary buttons) in both themes
16. THE Content_Management_System SHALL preview both themes when designing layouts
17. THE News_Portal SHALL ensure all text remains readable in both themes
18. THE News_Portal SHALL apply theme to syntax highlighting in code blocks (if applicable)


### Requirement 32: Comprehensive Testing and Quality Assurance

**User Story:** As a developer, I want comprehensive automated and manual testing for all features, so that the news portal is reliable and bug-free.

#### Acceptance Criteria

1. THE News_Portal SHALL have unit tests for all utility functions and helper modules with ≥ 80% code coverage
2. THE News_Portal SHALL have integration tests for all API endpoints verifying request/response behavior
3. THE News_Portal SHALL have end-to-end tests using Playwright or Cypress for critical user flows
4. THE News_Portal SHALL test all pages in Chromium browser (desktop, tablet, mobile viewports)
5. THE News_Portal SHALL test all interactive elements (buttons, links, forms, modals, dropdowns)
6. THE News_Portal SHALL test all CRUD operations (create, read, update, delete) for each entity
7. THE News_Portal SHALL test authentication flows (register, login, logout, OAuth, password reset)
8. THE News_Portal SHALL test authorization (role-based access control for admin, editor, author, reader)
9. THE News_Portal SHALL test comment system (post, reply, vote, moderate)
10. THE News_Portal SHALL test search functionality with various queries in Nepali and English
11. THE News_Portal SHALL test multi-language switching and content display in both languages
12. THE News_Portal SHALL test theme switching (light/dark) and persistence
13. THE News_Portal SHALL test responsive design at breakpoints (320px, 768px, 1024px, 1440px, 1920px)
14. THE News_Portal SHALL test image upload, optimization, and responsive image serving
15. THE News_Portal SHALL test video upload and playback for OK Reels
16. THE News_Portal SHALL test live score updates and sports section functionality
17. THE News_Portal SHALL test advertisement display, impression tracking, and click tracking
18. THE News_Portal SHALL test breaking news ticker scrolling and expiry
19. THE News_Portal SHALL test navigation (mega menu, mobile menu, breadcrumbs, pagination)
20. THE News_Portal SHALL test SEO metadata generation (meta tags, OG tags, JSON-LD, sitemap)
21. THE News_Portal SHALL test security features (CSRF protection, XSS prevention, rate limiting)
22. THE News_Portal SHALL test error handling (404, 500, network errors, validation errors)
23. THE News_Portal SHALL test performance (page load time, Lighthouse scores, Core Web Vitals)
24. THE News_Portal SHALL test accessibility (keyboard navigation, screen reader compatibility, ARIA labels)
25. THE News_Portal SHALL test database operations (queries, migrations, transactions, rollbacks)
26. THE News_Portal SHALL test dynamic site configuration (logo upload, footer update, settings changes)
27. THE News_Portal SHALL test all admin panel features (dashboard, article editor, category management, user management)
28. THE News_Portal SHALL test bookmark functionality (add, remove, list)
29. THE News_Portal SHALL test trending algorithm and popular content display
30. THE News_Portal SHALL test RSS feed generation and validity
31. THE News_Portal SHALL perform visual regression testing to detect unintended UI changes
32. THE News_Portal SHALL perform load testing to verify performance under concurrent users
33. THE News_Portal SHALL perform security testing (penetration testing, vulnerability scanning)
34. THE News_Portal SHALL test cross-browser compatibility (Chrome, Firefox, Safari, Edge)
35. THE News_Portal SHALL test on real devices (iOS, Android) for mobile experience
36. THE News_Portal SHALL maintain a test suite that runs on every commit (CI/CD pipeline)
37. THE News_Portal SHALL generate test coverage reports and maintain coverage ≥ 80%
38. THE News_Portal SHALL document all test cases with clear descriptions and expected outcomes
39. THE News_Portal SHALL perform manual exploratory testing for each major release
40. THE News_Portal SHALL maintain a bug tracking system and verify all bugs are fixed before release



### Requirement 31: Dark Mode and Light Mode Theme System

**User Story:** As a user, I want to switch between light and dark themes, so that I can read comfortably in different lighting conditions.

#### Acceptance Criteria

1. THE News_Portal SHALL support a Light_Theme (default) and a Dark_Theme
2. WHEN the portal first loads, THE Theme_System SHALL default to Light_Theme unless the user's OS prefers dark (`prefers-color-scheme: dark`)
3. WHEN a user toggles the theme, THE Theme_System SHALL immediately apply the selected theme site-wide
4. THE Theme_System SHALL persist the user's theme preference in `localStorage` key `theme`
5. WHEN a returning user loads the page, THE Theme_System SHALL restore their saved theme preference
6. THE Theme_System SHALL use CSS custom properties (variables) for all colors, backgrounds, and borders
7. THE Light_Theme SHALL use white/light-gray backgrounds with dark text (primary bg: `#ffffff`, text: `#1a1a1a`)
8. THE Dark_Theme SHALL use dark backgrounds with light text (primary bg: `#0f0f0f`, surface: `#1a1a1a`, text: `#f0f0f0`)
9. THE Theme_System SHALL apply themes consistently across every page — homepage, article, category, admin, auth
10. THE Admin_Panel SHALL have its own dark/light theme toggle, independent of the public site
11. THE Theme_Toggle_Button SHALL be visible in the top navigation bar on all screen sizes
12. WHEN in Dark_Theme, all images SHALL maintain visibility without color inversion
13. THE Theme_System SHALL apply smooth CSS transitions (150ms) when switching themes
14. THE News_Portal SHALL NOT flash the wrong theme on initial load (no FOUC — Flash of Unstyled Content)
15. WHEN a user is logged in, THE Theme_System SHALL sync theme preference to their account in the database
16. THE Dark_Theme SHALL maintain WCAG AA color contrast ratios (≥ 4.5:1 for normal text)



### Requirement 32: Nepali Language as Default with Proper i18n

**User Story:** As a Nepali user, I want the portal to display in Nepali by default, so that I can read content in my native language without configuration.

#### Acceptance Criteria

1. THE News_Portal SHALL default to Nepali (`ne`) as the primary locale on all pages
2. ALL UI labels, buttons, placeholders, error messages, and navigation items SHALL be in Nepali by default
3. THE i18n_System SHALL use translation files (`ne.json` and `en.json`) for all static UI strings
4. WHEN displaying dates, THE Nepali_Calendar SHALL show BS (Bikram Sambat) dates in Devanagari numerals by default
5. THE Nepali_Calendar SHALL convert between AD and BS dates bidirectionally
6. ALL Nepali text SHALL use a Nepali-compatible font (e.g., Noto Sans Devanagari or similar Google Font)
7. THE News_Portal SHALL display Nepali numerals (१, २, ३...) for counts, page numbers, and stats in Nepali mode
8. WHEN a user switches to English, ALL UI strings SHALL switch to English immediately
9. THE i18n_System SHALL persist language preference in `localStorage` key `lang` and in user account when logged in
10. THE i18n_System SHALL provide a language switcher button visible in the top bar on all devices
11. THE Search_Engine SHALL tokenize and index Nepali Unicode text correctly (Devanagari script)
12. THE News_Portal SHALL validate and accept Nepali Unicode input in comment and search forms
13. WHEN an admin creates content, THE Content_Management_System SHALL support Nepali Unicode in all text fields
14. THE Error_Messages SHALL be displayed in the user's selected language
15. THE Email_Templates SHALL be available in both Nepali and English



### Requirement 33: Fully Dynamic Site Configuration

**User Story:** As an admin, I want every visible piece of the site identity — logo, site name, colors, footer, navigation — to be configurable from the admin panel without touching code.

#### Acceptance Criteria

1. THE Site_Config_System SHALL store all configurable values in the `site_settings` database table as key-value pairs
2. THE News_Portal SHALL load site configuration at build time via ISR and cache it globally
3. WHEN an admin updates any setting, THE Site_Config_System SHALL invalidate the cache and apply changes within 30 seconds
4. THE Site_Config_System SHALL allow admins to upload and change the site logo (SVG/PNG, shown in header and favicon)
5. THE Site_Config_System SHALL allow admins to update the site name (displayed in header, footer, browser tab, SEO)
6. THE Site_Config_System SHALL allow admins to update the site tagline
7. THE Site_Config_System SHALL allow admins to configure the footer: organization name, registration number, phone, email, address
8. THE Site_Config_System SHALL allow admins to set social media links (Facebook, Twitter/X, YouTube, Instagram, TikTok)
9. THE Site_Config_System SHALL allow admins to configure the primary brand color (used for tags, accents, buttons)
10. THE Site_Config_System SHALL allow admins to enable or disable each homepage section (e.g., hide Sports, show Blog)
11. THE Site_Config_System SHALL allow admins to reorder homepage sections via drag-and-drop
12. THE Site_Config_System SHALL allow admins to manage the main navigation menu items and their order
13. THE Site_Config_System SHALL allow admins to configure the copyright year and text in the footer
14. THE Site_Config_System SHALL allow admins to upload a favicon separately from the main logo
15. ALL components that display site identity data (logo, name, footer, nav) SHALL read from the Site_Config_Context (React context) and NOT from hardcoded values
16. THE Site_Config_System SHALL provide a preview of changes before publishing
17. THE Site_Config_System SHALL maintain a settings history/changelog



### Requirement 34: End-to-End Testing with Playwright

**User Story:** As a developer, I want comprehensive automated tests covering all user-facing features, so that I can confidently deploy changes without regressions.

#### Acceptance Criteria

1. THE Test_Suite SHALL use Playwright with Chromium as the primary browser engine
2. THE Test_Suite SHALL test the homepage: all sections render, nav links work, breaking ticker displays
3. THE Test_Suite SHALL test article page: content renders, AI summary shows, share buttons work, comment form accessible
4. THE Test_Suite SHALL test navigation: all top-level nav links navigate to correct pages, mega menus open on hover
5. THE Test_Suite SHALL test the theme toggle: switching to dark mode applies dark background, switching back restores light
6. THE Test_Suite SHALL test the language switcher: switching to English changes UI labels, switching back restores Nepali
7. THE Test_Suite SHALL test user authentication: registration, login (email), logout flow
8. THE Test_Suite SHALL test comment submission: post a comment, verify it appears, like/dislike a comment
9. THE Test_Suite SHALL test search: enter a query, results appear, clicking result navigates to article
10. THE Test_Suite SHALL test category archive pages: correct articles shown, pagination works
11. THE Test_Suite SHALL test mobile responsive layout: viewport <768px shows hamburger, bottom nav appears
12. THE Test_Suite SHALL test the admin panel: login as admin, create article, verify it appears on homepage
13. THE Test_Suite SHALL test bookmark functionality: bookmark an article, verify it appears in profile
14. THE Test_Suite SHALL test 404 page: navigating to unknown URL shows custom 404 with navigation
15. THE Test_Suite SHALL test ad slots: designated ad positions render the ad container element
16. THE Test_Suite SHALL run in CI with `npx playwright test` command
17. THE Test_Suite SHALL generate an HTML report of test results
18. ALL critical user paths SHALL have test coverage (homepage → article → comment = one critical path)



### Requirement 35: Progressive Web App (PWA) Support

**User Story:** As a mobile user, I want to install the news portal as an app and read articles offline, so that I can access news without an internet connection.

#### Acceptance Criteria

1. THE News_Portal SHALL include a valid Web App Manifest (`manifest.json`) with name, icons, theme_color, and display mode
2. THE PWA_Service_Worker SHALL cache static assets (JS, CSS, fonts, images) on install
3. THE PWA_Service_Worker SHALL cache the last 20 viewed articles for offline reading
4. WHEN a user is offline, THE News_Portal SHALL display cached articles instead of an error
5. THE News_Portal SHALL display an "Install App" prompt on supported browsers
6. THE PWA_Service_Worker SHALL show a push notification for breaking news (if user grants permission)
7. THE News_Portal SHALL display an offline indicator banner when network is unavailable
8. THE PWA_Service_Worker SHALL use a stale-while-revalidate strategy for article pages
9. THE Web_App_Manifest SHALL specify separate icons for light and dark themes



### Requirement 36: Enhanced Reading Experience

**User Story:** As a reader, I want helpful reading tools on article pages, so that I can navigate long articles efficiently.

#### Acceptance Criteria

1. THE Article_Page SHALL display an estimated reading time (e.g., "५ मिनेट पढ्न समय") calculated from word count (200 wpm)
2. THE Article_Page SHALL display a Table of Contents (TOC) generated from H2/H3 headings for articles > 800 words
3. THE TOC SHALL use smooth scroll to navigate to sections when clicked
4. THE Article_Page SHALL display a reading progress bar at the top of the viewport that fills as the user scrolls
5. THE Article_Page SHALL display a "Back to Top" button that appears after scrolling 300px down
6. THE Article_Page SHALL display a "Copy Link" button that copies the article URL to clipboard and shows a confirmation
7. THE Article_Page SHALL display a "Print" button that opens a print-friendly version of the article
8. THE Article_Page SHALL display article word count
9. WHEN a user highlights text, THE Article_Page SHALL show a mini share menu (share highlighted quote)
10. THE Article_Page SHALL persist scroll position so returning users resume where they left off



### Requirement 37: Authentication Extras — Password Reset and Email Verification

**User Story:** As a user, I want to reset my forgotten password and verify my email, so that I can recover my account securely.

#### Acceptance Criteria

1. THE Auth_System SHALL provide a "Forgot Password" link on the login page
2. WHEN a user submits their email on the Forgot_Password page, THE Auth_System SHALL send a password reset email with a time-limited token (expires in 1 hour)
3. WHEN a user clicks the reset link, THE Auth_System SHALL show a form to set a new password
4. THE Auth_System SHALL validate the new password (min 8 chars, at least 1 number)
5. WHEN a new user registers with email, THE Auth_System SHALL send a verification email
6. UNTIL a user verifies their email, THE Auth_System SHALL restrict them from commenting (but allow reading)
7. THE Auth_System SHALL allow resending the verification email from the profile page
8. WHEN a user's session expires, THE Auth_System SHALL redirect to login and restore the intended page after login
9. THE Auth_System SHALL display clear, Nepali-language messages for all auth states



### Requirement 38: Cookie Consent and Privacy Compliance

**User Story:** As a user, I want to know what cookies and tracking are used, so that I can make an informed choice about my privacy.

#### Acceptance Criteria

1. THE News_Portal SHALL display a cookie consent banner on first visit before setting any non-essential cookies
2. THE Cookie_Consent_Banner SHALL offer "Accept All", "Reject Non-Essential", and "Manage Preferences" options
3. THE News_Portal SHALL NOT set analytics or advertising cookies until consent is given
4. THE Cookie_Consent_System SHALL persist consent choices in `localStorage`
5. THE News_Portal SHALL provide a Privacy Policy page accessible from the footer
6. THE News_Portal SHALL provide a Terms of Service page accessible from the footer
7. WHEN a user rejects non-essential cookies, THE News_Portal SHALL disable Google Analytics and ad tracking
8. THE Cookie_Consent_Banner SHALL be accessible (keyboard navigable, ARIA labeled)
9. THE News_Portal SHALL allow users to update their consent preferences at any time via a footer link


---

## AI Implementation Notes

**CRITICAL: This section provides essential guidance for AI agents (Claude Opus 4.6 or similar) implementing this system. Follow these instructions precisely to avoid hallucinations and ensure top-notch quality.**

### General Implementation Principles

1. **NO HALLUCINATIONS**: Do not invent libraries, APIs, or features that don't exist. Verify all package names, versions, and APIs before using them.

2. **VERIFY BEFORE IMPLEMENTING**: Always check official documentation for Next.js, React, Prisma, PostgreSQL, and all dependencies before writing code.

3. **USE ESTABLISHED PATTERNS**: Follow Next.js 14+ App Router conventions, React best practices, and TypeScript strict mode.

4. **NO SHORTCUTS**: Implement all security features completely. Do not skip CSRF protection, input validation, or sanitization.

5. **TEST EVERYTHING**: Write tests for every feature. Do not assume code works without testing.

6. **FOLLOW REQUIREMENTS EXACTLY**: Implement every acceptance criterion. Do not skip or modify requirements.

### Technology Stack - VERIFIED PACKAGES

**CRITICAL: Use only these verified packages. Do not invent package names.**

#### Core Framework
- `next@14.2.0` or later (App Router)
- `react@18.3.0` or later
- `react-dom@18.3.0` or later
- `typescript@5.4.0` or later

#### Database & ORM
- `@prisma/client@5.14.0` or later
- `prisma@5.14.0` or later (dev dependency)
- `pg@8.11.0` or later (PostgreSQL driver)

#### Authentication
- `next-auth@4.24.0` or later (or `@auth/nextjs` for Auth.js v5)
- `bcryptjs@2.4.3` or later
- `jsonwebtoken@9.0.2` or later

#### Validation & Security
- `zod@3.23.0` or later (input validation)
- `dompurify@3.1.0` or later (XSS prevention)
- `isomorphic-dompurify@2.11.0` (for SSR)
- `express-rate-limit@7.2.0` or later (rate limiting)
- `helmet@7.1.0` or later (security headers)
- `csurf@1.11.0` or later (CSRF protection)

#### UI & Styling
- `tailwindcss@3.4.0` or later
- `@tailwindcss/typography@0.5.13` or later
- `autoprefixer@10.4.19` or later
- `postcss@8.4.38` or later
- `clsx@2.1.1` or later (conditional classes)
- `tailwind-merge@2.3.0` or later

#### Rich Text Editor
- `@tiptap/react@2.3.0` or later
- `@tiptap/starter-kit@2.3.0` or later
- `@tiptap/extension-image@2.3.0` or later
- `@tiptap/extension-link@2.3.0` or later

#### Image Handling
- `sharp@0.33.0` or later (image optimization)
- `next/image` (built-in Next.js component)

#### Date & Time
- `date-fns@3.6.0` or later
- `nepali-date-converter@3.0.0` or later (Nepali calendar)

#### Testing
- `@playwright/test@1.44.0` or later (E2E testing)
- `vitest@1.6.0` or later (unit testing)
- `@testing-library/react@15.0.0` or later
- `@testing-library/jest-dom@6.4.0` or later

#### Utilities
- `axios@1.7.0` or later (HTTP client)
- `swr@2.2.5` or later (data fetching)
- `react-hot-toast@2.4.1` or later (notifications)
- `react-icons@5.2.0` or later (icons)
- `embla-carousel-react@8.1.0` or later (carousels)

### Security Implementation Checklist

**CRITICAL: Implement ALL of these. No exceptions.**

#### Authentication Security
- [ ] Hash passwords with bcrypt (salt rounds ≥ 10)
- [ ] Use HTTP-only, Secure, SameSite=Strict cookies
- [ ] Implement JWT with short expiration (15 minutes access, 7 days refresh)
- [ ] Implement account lockout after failed attempts
- [ ] Implement password strength validation
- [ ] Implement email verification for new accounts
- [ ] Implement secure password reset with time-limited tokens
- [ ] Implement 2FA for admin accounts using `speakeasy` or `otplib`

#### Input Validation & Sanitization
- [ ] Validate ALL API inputs using Zod schemas
- [ ] Sanitize ALL user-generated content with DOMPurify
- [ ] Validate file uploads (type, size, magic bytes)
- [ ] Sanitize file names to prevent directory traversal
- [ ] Validate and sanitize search queries
- [ ] Validate and sanitize comment content
- [ ] Escape output in all dynamic content

#### CSRF & XSS Protection
- [ ] Implement CSRF tokens on all POST/PUT/DELETE endpoints
- [ ] Set Content-Security-Policy headers
- [ ] Use `dangerouslySetInnerHTML` only with sanitized content
- [ ] Implement Subresource Integrity (SRI) for external resources
- [ ] Set X-Content-Type-Options: nosniff
- [ ] Set X-Frame-Options: DENY

#### Rate Limiting
- [ ] Implement rate limiting on all API endpoints (100 req/min)
- [ ] Implement stricter rate limiting on auth endpoints (5 req/15min)
- [ ] Implement rate limiting on comment posting (5 comments/min)
- [ ] Implement rate limiting on search (20 req/min)
- [ ] Store rate limit data in Redis or in-memory cache

#### Database Security
- [ ] Use Prisma parameterized queries (automatic)
- [ ] Use SSL/TLS for database connections
- [ ] Encrypt sensitive data at rest (AES-256)
- [ ] Implement database connection pooling
- [ ] Use environment variables for credentials
- [ ] Never log sensitive data (passwords, tokens)

#### File Upload Security
- [ ] Validate file types by magic bytes, not extensions
- [ ] Limit file sizes (images: 10MB, videos: 100MB)
- [ ] Store files with randomized names
- [ ] Store files outside web root or use cloud storage
- [ ] Scan uploaded files for malware (optional: ClamAV)
- [ ] Reject executable files (.exe, .sh, .bat, .js, .php)

#### Session Management
- [ ] Implement secure session storage
- [ ] Set session timeout (30 minutes inactivity)
- [ ] Invalidate sessions on password change
- [ ] Implement session fixation protection
- [ ] Implement concurrent session limits

#### Logging & Monitoring
- [ ] Log all authentication events
- [ ] Log all admin actions (audit trail)
- [ ] Log security events (rate limits, failed logins)
- [ ] Never log sensitive data
- [ ] Implement log rotation
- [ ] Set up security alerts for suspicious activities

### Code Quality Standards

**CRITICAL: Follow these standards for every file.**

#### TypeScript
- [ ] Use strict mode (`"strict": true` in tsconfig.json)
- [ ] Define interfaces for all data structures
- [ ] Use type guards for runtime type checking
- [ ] Avoid `any` type (use `unknown` if necessary)
- [ ] Use enums for fixed sets of values
- [ ] Document complex types with JSDoc comments

#### React Components
- [ ] Use functional components with hooks
- [ ] Implement proper error boundaries
- [ ] Use React.memo for expensive components
- [ ] Implement proper loading states
- [ ] Implement proper error states
- [ ] Use Suspense for code splitting
- [ ] Avoid prop drilling (use Context or state management)

#### Next.js Best Practices
- [ ] Use App Router (not Pages Router)
- [ ] Use Server Components by default
- [ ] Use Client Components only when needed ('use client')
- [ ] Implement proper metadata for SEO
- [ ] Use dynamic imports for code splitting
- [ ] Implement ISR for article pages (revalidate: 60)
- [ ] Implement SSR for homepage
- [ ] Use Next.js Image component for all images
- [ ] Implement proper error.tsx and loading.tsx files

#### Database & Prisma
- [ ] Define complete Prisma schema with all relationships
- [ ] Use proper indexes on frequently queried fields
- [ ] Use transactions for multi-step operations
- [ ] Implement proper error handling for database operations
- [ ] Use connection pooling
- [ ] Implement database migrations properly
- [ ] Create seed data for development

#### API Routes
- [ ] Validate all inputs with Zod
- [ ] Return consistent response format: `{ success, data, error }`
- [ ] Use proper HTTP status codes
- [ ] Implement proper error handling
- [ ] Add rate limiting middleware
- [ ] Add authentication middleware for protected routes
- [ ] Add CORS headers if needed
- [ ] Log all requests

#### Styling
- [ ] Use Tailwind CSS utility classes
- [ ] Create reusable component classes in globals.css
- [ ] Use CSS variables for theme colors
- [ ] Implement mobile-first responsive design
- [ ] Test all breakpoints (320px, 768px, 1024px, 1440px)
- [ ] Ensure proper contrast ratios (≥ 4.5:1)
- [ ] Use semantic color names (not hardcoded hex values)

### Testing Requirements

**CRITICAL: Write tests for everything.**

#### Unit Tests (Vitest)
- [ ] Test all utility functions
- [ ] Test all helper modules
- [ ] Test all validation schemas
- [ ] Test all data transformations
- [ ] Achieve ≥ 80% code coverage

#### Integration Tests
- [ ] Test all API endpoints
- [ ] Test database operations
- [ ] Test authentication flows
- [ ] Test file upload functionality
- [ ] Test email sending (use mock)

#### E2E Tests (Playwright)
- [ ] Test user registration and login
- [ ] Test article creation and publishing
- [ ] Test comment posting and voting
- [ ] Test search functionality
- [ ] Test navigation and routing
- [ ] Test responsive design (mobile, tablet, desktop)
- [ ] Test theme switching
- [ ] Test language switching
- [ ] Test all admin panel features
- [ ] Test error scenarios (404, 500, network errors)

#### Browser Testing
- [ ] Test in Chromium (primary)
- [ ] Test in Firefox
- [ ] Test in Safari (WebKit)
- [ ] Test on real mobile devices (iOS, Android)

### Performance Optimization

**CRITICAL: Implement all performance optimizations.**

#### Image Optimization
- [ ] Use Next.js Image component with proper sizes
- [ ] Generate responsive image variants (thumbnail, medium, large)
- [ ] Serve images in WebP/AVIF with fallbacks
- [ ] Implement lazy loading for below-the-fold images
- [ ] Use blur placeholders for images
- [ ] Optimize image quality (80-85%)

#### Code Optimization
- [ ] Implement code splitting per route
- [ ] Use dynamic imports for heavy components
- [ ] Minimize JavaScript bundle size (< 200KB gzipped)
- [ ] Remove unused dependencies
- [ ] Tree-shake unused code
- [ ] Minify production builds

#### Caching
- [ ] Implement ISR for article pages (60s revalidation)
- [ ] Cache static assets (1 year)
- [ ] Cache API responses where appropriate
- [ ] Use SWR for client-side data fetching
- [ ] Implement Redis caching for frequently accessed data

#### Database Optimization
- [ ] Create indexes on frequently queried fields
- [ ] Use database connection pooling
- [ ] Optimize complex queries
- [ ] Use pagination for large result sets
- [ ] Implement query result caching

#### Monitoring
- [ ] Achieve Lighthouse score ≥ 90 (desktop)
- [ ] Achieve Lighthouse score ≥ 80 (mobile)
- [ ] Monitor Core Web Vitals (LCP, FID, CLS)
- [ ] Set up performance monitoring (Vercel Analytics or similar)

### Accessibility Requirements

**CRITICAL: Make the site accessible to all users.**

- [ ] Use semantic HTML5 elements
- [ ] Provide alt text for all images
- [ ] Implement keyboard navigation
- [ ] Add visible focus indicators
- [ ] Maintain proper color contrast (≥ 4.5:1)
- [ ] Use ARIA labels for icon-only buttons
- [ ] Use ARIA live regions for dynamic content
- [ ] Implement proper heading hierarchy (h1 → h2 → h3)
- [ ] Add skip-to-content links
- [ ] Test with screen readers (NVDA, JAWS, VoiceOver)
- [ ] Support browser zoom up to 200%

### Nepali Language Implementation

**CRITICAL: Implement proper Nepali support.**

#### Font Setup
- [ ] Use Google Fonts: Noto Sans Devanagari or Mukta
- [ ] Set proper font fallbacks: `'Noto Sans Devanagari', 'Mukta', sans-serif`
- [ ] Ensure proper Unicode rendering (UTF-8)
- [ ] Test Devanagari character rendering

#### Date & Time
- [ ] Use `nepali-date-converter` package
- [ ] Display dates in Nepali format: "२०८२ वैशाख १३, शनिबार"
- [ ] Use Devanagari numerals: ०१२३४५६७८९
- [ ] Implement relative time in Nepali: "२ घण्टा अगाडि"

#### Content
- [ ] Store Nepali content in UTF-8 encoding
- [ ] Support Nepali text input in all forms
- [ ] Implement Nepali search with proper tokenization
- [ ] Translate all UI elements to Nepali
- [ ] Test Nepali text rendering in all components

### Theme Implementation

**CRITICAL: Implement consistent theming.**

#### CSS Variables (globals.css)
```css
:root {
  /* Light Theme (Default) */
  --color-background: #ffffff;
  --color-foreground: #1a1a1a;
  --color-muted: #f5f5f5;
  --color-border: #e5e5e5;
  --color-primary: #c62828;
  --color-secondary: #666666;
}

[data-theme='dark'] {
  /* Dark Theme */
  --color-background: #1a1a1a;
  --color-foreground: #f5f5f5;
  --color-muted: #2a2a2a;
  --color-border: #333333;
  --color-primary: #e53935;
  --color-secondary: #aaaaaa;
}
```

#### Theme Toggle Implementation
- [ ] Create ThemeProvider context
- [ ] Store theme preference in localStorage
- [ ] Detect system preference (prefers-color-scheme)
- [ ] Apply theme on initial load (prevent flash)
- [ ] Add smooth transitions (200ms)
- [ ] Test theme consistency across all pages

### Dynamic Configuration Implementation

**CRITICAL: Make everything configurable.**

#### Database Schema
```prisma
model SiteSettings {
  key       String   @id
  value     Json
  updatedAt DateTime @updatedAt
}
```

#### Configurable Elements
- [ ] Site logo (header, mobile, favicon)
- [ ] Site name (Nepali and English)
- [ ] Site tagline (Nepali and English)
- [ ] Contact information (phone, email, address)
- [ ] Social media links (Facebook, Twitter, YouTube, Instagram)
- [ ] Footer content (chairman, chief editor, copyright)
- [ ] Homepage section order
- [ ] Default language (Nepali)
- [ ] Default theme (light)
- [ ] Items per page
- [ ] Feature toggles (comments, bookmarks, reels)

#### Implementation
- [ ] Create settings API endpoints
- [ ] Create settings admin UI
- [ ] Cache settings in memory
- [ ] Reload settings on update
- [ ] Provide settings preview
- [ ] Validate settings before saving

### Error Handling

**CRITICAL: Handle all errors gracefully.**

#### Error Types
- [ ] Network errors (timeout, connection refused)
- [ ] Validation errors (invalid input)
- [ ] Authentication errors (unauthorized, forbidden)
- [ ] Database errors (connection, query failure)
- [ ] File upload errors (size, type, corruption)
- [ ] Rate limit errors (too many requests)

#### Error Responses
- [ ] Return user-friendly error messages
- [ ] Log detailed errors server-side
- [ ] Never expose stack traces to users
- [ ] Provide actionable next steps
- [ ] Implement proper HTTP status codes

#### Error UI
- [ ] Create custom 404 page
- [ ] Create custom 500 page
- [ ] Implement error boundaries
- [ ] Show toast notifications for errors
- [ ] Display inline validation errors
- [ ] Provide retry mechanisms

### Deployment Checklist

**CRITICAL: Complete before deploying to production.**

- [ ] Set all environment variables
- [ ] Enable HTTPS (SSL/TLS certificate)
- [ ] Set up database backups
- [ ] Configure CDN for static assets
- [ ] Set up error monitoring (Sentry or similar)
- [ ] Set up performance monitoring
- [ ] Set up uptime monitoring
- [ ] Configure rate limiting
- [ ] Enable security headers
- [ ] Test all features in production environment
- [ ] Run security audit (npm audit, Snyk)
- [ ] Run performance audit (Lighthouse)
- [ ] Run accessibility audit (axe, WAVE)
- [ ] Set up CI/CD pipeline
- [ ] Document deployment process
- [ ] Create rollback plan

### Common Pitfalls to Avoid

**CRITICAL: Do not make these mistakes.**

1. **DO NOT** use `dangerouslySetInnerHTML` without sanitizing content
2. **DO NOT** store passwords in plain text
3. **DO NOT** expose API keys or secrets in client-side code
4. **DO NOT** trust user input (always validate and sanitize)
5. **DO NOT** use `eval()` or `Function()` constructor
6. **DO NOT** disable CORS without understanding implications
7. **DO NOT** skip input validation on API endpoints
8. **DO NOT** use `any` type in TypeScript
9. **DO NOT** commit `.env` files to version control
10. **DO NOT** use outdated or vulnerable dependencies
11. **DO NOT** implement custom crypto (use established libraries)
12. **DO NOT** skip error handling
13. **DO NOT** skip testing
14. **DO NOT** hardcode configuration values
15. **DO NOT** use synchronous operations in API routes
16. **DO NOT** fetch data in loops (use batch queries)
17. **DO NOT** skip database indexes
18. **DO NOT** skip image optimization
19. **DO NOT** skip accessibility features
20. **DO NOT** deploy without testing

### Final Verification Checklist

**CRITICAL: Verify before marking as complete.**

- [ ] All 32 requirements implemented
- [ ] All acceptance criteria met
- [ ] All security features implemented
- [ ] All tests passing (unit, integration, E2E)
- [ ] Code coverage ≥ 80%
- [ ] Lighthouse score ≥ 90 (desktop), ≥ 80 (mobile)
- [ ] No TypeScript errors
- [ ] No ESLint errors
- [ ] No console.log statements in production code
- [ ] All images optimized
- [ ] All dependencies up to date
- [ ] Security audit passed
- [ ] Accessibility audit passed
- [ ] Cross-browser testing completed
- [ ] Mobile device testing completed
- [ ] Documentation complete
- [ ] Deployment checklist complete

---

**END OF AI IMPLEMENTATION NOTES**

**Remember: Quality over speed. Implement everything correctly the first time. Do not skip steps. Do not hallucinate. Verify everything. Test everything. Build a top-notch system.**

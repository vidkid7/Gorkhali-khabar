<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class LegacyCompatibleSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $seedPassword = env('SEED_ADMIN_PASSWORD', 'Admin@12345');

        $admin = [
            'id' => (string) Str::ulid(),
            'name' => 'Gorkhali Admin',
            'email' => 'admin@gorkhali.com',
            'email_verified' => $now,
            'password_hash' => Hash::make($seedPassword),
            'role' => 'ADMIN',
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ];
        $userIdType = Schema::getColumnType('users', 'id');
        if ($userIdType !== 'integer' && $userIdType !== 'bigint') {
            DB::table('users')->insertOrIgnore($admin);
        } else {
            unset($admin['id']);
            DB::table('users')->insertOrIgnore($admin);
        }

        $this->insertDefaults('categories', 'slug', [
            ['name' => 'समाचार', 'name_en' => 'News', 'slug' => 'samachar', 'color' => '#c62828', 'sort_order' => 1],
            ['name' => 'राजनीति', 'name_en' => 'Politics', 'slug' => 'rajniti', 'color' => '#1565c0', 'sort_order' => 2],
            ['name' => 'अर्थतन्त्र', 'name_en' => 'Economy', 'slug' => 'arthatantra', 'color' => '#2e7d32', 'sort_order' => 3],
            ['name' => 'खेलकुद', 'name_en' => 'Sports', 'slug' => 'khelkud', 'color' => '#e65100', 'sort_order' => 4],
            ['name' => 'प्रविधि', 'name_en' => 'Technology', 'slug' => 'prabidhi', 'color' => '#6a1b9a', 'sort_order' => 5],
            ['name' => 'समाज', 'name_en' => 'Society', 'slug' => 'samaj', 'color' => '#2e7d32', 'sort_order' => 6],
            ['name' => 'मनोरञ्जन', 'name_en' => 'Entertainment', 'slug' => 'manoranjan', 'color' => '#6a1b9a', 'sort_order' => 7],
            ['name' => 'विश्व', 'name_en' => 'World', 'slug' => 'world', 'color' => '#37474f', 'sort_order' => 8],
            ['name' => 'स्वास्थ्य', 'name_en' => 'Health', 'slug' => 'swasthya', 'color' => '#c62828', 'sort_order' => 9],
            ['name' => 'शिक्षा', 'name_en' => 'Education', 'slug' => 'shiksha', 'color' => '#ad1457', 'sort_order' => 10],
            ['name' => 'विचार', 'name_en' => 'Opinion', 'slug' => 'bichar', 'color' => '#4e342e', 'sort_order' => 11],
            ['name' => 'अन्तर्राष्ट्रिय', 'name_en' => 'International', 'slug' => 'antarrashtriya', 'color' => '#0f4c81', 'sort_order' => 30],
            ['name' => 'फिचर', 'name_en' => 'Features', 'slug' => 'feature', 'color' => '#7c3aed', 'sort_order' => 31],
            ['name' => 'भिडियो', 'name_en' => 'Video', 'slug' => 'video', 'color' => '#dc2626', 'sort_order' => 32],
            ['name' => 'अनुसन्धान', 'name_en' => 'Investigations', 'slug' => 'anveshan', 'color' => '#7f1d1d', 'sort_order' => 33],
            ['name' => 'जलवायु र वातावरण', 'name_en' => 'Climate & Environment', 'slug' => 'jalbayu-paryawaran', 'color' => '#0f766e', 'sort_order' => 34],
            ['name' => 'कृषि', 'name_en' => 'Agriculture', 'slug' => 'krishi', 'color' => '#4d7c0f', 'sort_order' => 35],
            ['name' => 'पर्यटन', 'name_en' => 'Travel', 'slug' => 'paryatan', 'color' => '#0369a1', 'sort_order' => 36],
            ['name' => 'कला र संस्कृति', 'name_en' => 'Arts & Culture', 'slug' => 'kala-sanskriti', 'color' => '#9d174d', 'sort_order' => 37],
            ['name' => 'जीवनशैली', 'name_en' => 'Lifestyle', 'slug' => 'jivanshaili', 'color' => '#a16207', 'sort_order' => 38],
            ['name' => 'सुरक्षा र अपराध', 'name_en' => 'Security & Crime', 'slug' => 'surakshya-aparadh', 'color' => '#475569', 'sort_order' => 39],
            ['name' => 'रोजगारी', 'name_en' => 'Jobs & Careers', 'slug' => 'rojgari', 'color' => '#4338ca', 'sort_order' => 40],
            ['name' => 'प्रवास', 'name_en' => 'Diaspora', 'slug' => 'prabas', 'color' => '#be123c', 'sort_order' => 41],
            // Province categories
            ['name' => 'बागमती प्रदेश', 'name_en' => 'Bagmati Province', 'slug' => 'bagmati-pradesh', 'color' => '#c62828', 'sort_order' => 10],
            ['name' => 'कोशी प्रदेश', 'name_en' => 'Koshi Province', 'slug' => 'koshi-pradesh', 'color' => '#1565c0', 'sort_order' => 11],
            ['name' => 'मधेश प्रदेश', 'name_en' => 'Madhesh Province', 'slug' => 'madhesh-pradesh', 'color' => '#2e7d32', 'sort_order' => 12],
            ['name' => 'गण्डकी प्रदेश', 'name_en' => 'Gandaki Province', 'slug' => 'gandaki-pradesh', 'color' => '#e65100', 'sort_order' => 13],
            ['name' => 'लुम्बिनी प्रदेश', 'name_en' => 'Lumbini Province', 'slug' => 'lumbini-pradesh', 'color' => '#6a1b9a', 'sort_order' => 14],
            ['name' => 'कर्णाली प्रदेश', 'name_en' => 'Karnali Province', 'slug' => 'karnali-pradesh', 'color' => '#00838f', 'sort_order' => 15],
            ['name' => 'सुदूरपश्चिम प्रदेश', 'name_en' => 'Sudurpaschim Province', 'slug' => 'sudurpaschim-pradesh', 'color' => '#4e342e', 'sort_order' => 16],
        ], true);

        $politics = DB::table('categories')->where('slug', 'rajniti')->value('id');
        $sports = DB::table('categories')->where('slug', 'khelkud')->value('id');
        $this->insertDefaults('categories', 'slug', [
            ['name' => 'संसद', 'name_en' => 'Parliament', 'slug' => 'samsad', 'color' => '#8e0000', 'sort_order' => 20, 'parent_id' => $politics],
            ['name' => 'दलहरू', 'name_en' => 'Parties', 'slug' => 'dalharu', 'color' => '#8e0000', 'sort_order' => 21, 'parent_id' => $politics],
            ['name' => 'क्रिकेट', 'name_en' => 'Cricket', 'slug' => 'cricket', 'color' => '#e65100', 'sort_order' => 22, 'parent_id' => $sports],
            ['name' => 'फुटबल', 'name_en' => 'Football', 'slug' => 'football', 'color' => '#e65100', 'sort_order' => 23, 'parent_id' => $sports],
        ], true);

        $adminId = DB::table('users')->where('email', 'admin@gorkhali.com')->value('id');
        $newsCategory = DB::table('categories')->where('slug', 'samachar')->value('id');
        $opinionCategory = DB::table('categories')->where('slug', 'bichar')->value('id') ?? $newsCategory;
        $provinceCategory = DB::table('categories')->where('slug', 'bagmati-pradesh')->value('id') ?? $newsCategory;

        $this->insertDefaults('articles', 'slug', [
            [
                'slug' => 'seeded-editorial-lead',
                'title' => 'समुदाय, प्रविधि र नयाँ नेपालको कथा',
                'title_en' => 'Stories of community, technology, and a changing Nepal',
                'excerpt' => 'स्थानीय पहल, नयाँ प्रविधि र नागरिक सहभागिताले बदलिँदो नेपालको कथा देखाउँछन्।',
                'excerpt_en' => 'Local initiatives, new technology, and civic participation are shaping a changing Nepal.',
                'content' => '<p>गोर्खाली खबरको सम्पादकीय डेस्कबाट समुदाय र परिवर्तनका कथाहरू।</p>',
                'content_en' => '<p>Stories of community and change from the Gorkhali Khabar editorial desk.</p>',
                'status' => 'PUBLISHED',
                'is_featured' => true,
                'reading_time' => 4,
                'word_count' => 650,
                'published_at' => $now->copy()->subHours(2),
                'category_id' => $newsCategory,
                'author_id' => $adminId,
            ],
            [
                'slug' => 'seeded-opinion-nepal',
                'title' => 'नेपालको सार्वजनिक संवादलाई बलियो बनाउने समय',
                'title_en' => 'Time to strengthen Nepal’s public conversation',
                'excerpt' => 'नीति, नागरिक र स्थानीय आवाजबीचको संवादले लोकतन्त्रलाई अझ उत्तरदायी बनाउँछ।',
                'excerpt_en' => 'Dialogue between policy, citizens, and local voices can make democracy more accountable.',
                'content' => '<p>विचार डेस्कको नियमित स्तम्भ।</p>',
                'content_en' => '<p>A regular column from the Opinion Desk.</p>',
                'status' => 'PUBLISHED',
                'reading_time' => 6,
                'word_count' => 900,
                'published_at' => $now->copy()->subHours(5),
                'category_id' => $opinionCategory,
                'author_id' => $adminId,
            ],
            [
                'slug' => 'seeded-province-update',
                'title' => 'प्रदेशबाट आएका परिवर्तनका सात संकेत',
                'title_en' => 'Seven signals of change from the provinces',
                'excerpt' => 'प्रदेशका स्थानीय तहमा भइरहेका सात उल्लेखनीय काम र सिकाइ।',
                'excerpt_en' => 'Seven notable initiatives and lessons emerging from local governments.',
                'content' => '<p>प्रदेश समाचार डेस्कबाट विशेष रिपोर्ट।</p>',
                'content_en' => '<p>A special report from the provincial desk.</p>',
                'status' => 'PUBLISHED',
                'reading_time' => 5,
                'word_count' => 780,
                'published_at' => $now->copy()->subHours(8),
                'category_id' => $provinceCategory,
                'author_id' => $adminId,
            ],
        ], true);

        $editorialVerticals = [
            'antarrashtriya' => ['name' => 'अन्तर्राष्ट्रिय', 'name_en' => 'International'],
            'feature' => ['name' => 'फिचर', 'name_en' => 'Features'],
            'video' => ['name' => 'भिडियो', 'name_en' => 'Video'],
            'anveshan' => ['name' => 'अनुसन्धान', 'name_en' => 'Investigations'],
            'jalbayu-paryawaran' => ['name' => 'जलवायु र वातावरण', 'name_en' => 'Climate & Environment'],
            'krishi' => ['name' => 'कृषि', 'name_en' => 'Agriculture'],
            'paryatan' => ['name' => 'पर्यटन', 'name_en' => 'Travel'],
            'kala-sanskriti' => ['name' => 'कला र संस्कृति', 'name_en' => 'Arts & Culture'],
            'jivanshaili' => ['name' => 'जीवनशैली', 'name_en' => 'Lifestyle'],
            'surakshya-aparadh' => ['name' => 'सुरक्षा र अपराध', 'name_en' => 'Security & Crime'],
            'rojgari' => ['name' => 'रोजगारी', 'name_en' => 'Jobs & Careers'],
            'prabas' => ['name' => 'प्रवास', 'name_en' => 'Diaspora'],
        ];
        $editorialArticles = [];
        foreach ($editorialVerticals as $slug => $vertical) {
            $categoryId = DB::table('categories')->where('slug', $slug)->value('id');
            foreach ([1, 2, 3] as $index) {
                $editorialArticles[] = [
                    'slug' => "editorial-vertical-{$slug}-{$index}",
                    'title' => "नमूना: {$vertical['name']} विशेष रिपोर्ट {$index}",
                    'title_en' => "Sample: {$vertical['name_en']} report {$index}",
                    'excerpt' => 'यो प्रदर्शनका लागि तयार गरिएको नमूना सामग्री हो।',
                    'excerpt_en' => 'This is fictional demonstration content prepared for the portal.',
                    'content' => '<p>यो सामग्री परीक्षण र प्रदर्शनका लागि मात्र हो।</p>',
                    'content_en' => '<p>This story is fictional sample content for testing and demonstration.</p>',
                    'status' => 'PUBLISHED',
                    'reading_time' => 3 + $index,
                    'word_count' => 500 + ($index * 90),
                    'published_at' => $now->copy()->subHours(($index * 3) + 1),
                    'category_id' => $categoryId,
                    'author_id' => $adminId,
                ];
            }
        }
        $this->insertDefaults('articles', 'slug', $editorialArticles, true);

        $leadArticleId = DB::table('articles')->where('slug', 'seeded-editorial-lead')->value('id');
        $this->insertDefaults('breaking_news', 'title', [
            ['title' => 'गोर्खाली खबर विशेष अपडेट', 'article_id' => $leadArticleId, 'is_active' => true, 'expires_at' => $now->copy()->addDays(2)],
        ]);

        $this->insertDefaults('reels', 'slug', [
            ['slug' => 'seeded-community-story', 'title' => 'समुदायको छोटो कथा', 'title_en' => 'A short community story', 'video_url' => '/reels/community-story.mp4', 'thumbnail' => '/images/placeholder-news.svg', 'description' => 'समुदायमा भइरहेका सकारात्मक पहलहरू।', 'is_active' => true],
            ['slug' => 'seeded-nepal-update', 'title' => 'नेपाल अपडेट ६० सेकेन्डमा', 'title_en' => 'Nepal update in 60 seconds', 'video_url' => '/reels/nepal-update.mp4', 'thumbnail' => '/images/placeholder-news.svg', 'description' => 'दिनका मुख्य अपडेटहरू छोटकरीमा।', 'is_active' => true],
        ], true);

        $this->insertDefaults('galleries', 'slug', [
            ['slug' => 'seeded-nepal-in-focus', 'title' => 'नेपाल फोकसमा', 'title_en' => 'Nepal in Focus', 'description' => 'नेपालका विभिन्न ठाउँका दृश्यहरू।', 'cover_image' => '/images/placeholder-news.svg', 'is_active' => true],
        ], true);

        $this->insertDefaults('tags', 'slug', [
            ['name' => 'ब्रेकिङ', 'name_en' => 'Breaking', 'slug' => 'breaking'],
            ['name' => 'ट्रेन्डिङ', 'name_en' => 'Trending', 'slug' => 'trending'],
            ['name' => 'विश्लेषण', 'name_en' => 'Analysis', 'slug' => 'analysis'],
            ['name' => 'नेपाल', 'name_en' => 'Nepal', 'slug' => 'nepal'],
        ]);

        $this->insertDefaults('quick_links', 'slug', [
            ['slug' => 'patro', 'href' => '/patro', 'title_ne' => 'पात्रो', 'title_en' => 'Patro', 'description_ne' => 'नेपाली पात्रो र मिति', 'description_en' => 'Nepali calendar & dates', 'icon_key' => 'CalendarDays', 'accent_color' => '#c62828', 'sort_order' => 10],
            ['slug' => 'rashifal', 'href' => '/rashifal', 'title_ne' => 'राशिफल', 'title_en' => 'Horoscope', 'description_ne' => 'आजको राशिफल', 'description_en' => "Today's horoscope", 'icon_key' => 'Sparkles', 'accent_color' => '#6a1b9a', 'sort_order' => 20],
            ['slug' => 'share-market', 'href' => '/share-market', 'title_ne' => 'शेयर बजार', 'title_en' => 'Share Market', 'description_ne' => 'नेप्से परिसूचक र शेयर भाव', 'description_en' => 'NEPSE index & quotes', 'icon_key' => 'ChartNoAxesCombined', 'accent_color' => '#07579b', 'sort_order' => 30],
            ['slug' => 'finance', 'href' => '/finance', 'title_ne' => 'वित्त', 'title_en' => 'Finance', 'description_ne' => 'विदेशी मुद्रा र दर', 'description_en' => 'Exchange rates & finance', 'icon_key' => 'Landmark', 'accent_color' => '#2e7d32', 'sort_order' => 40],
            ['slug' => 'gold-silver', 'href' => '/patro/gold-silver', 'title_ne' => 'सुन-चाँदी', 'title_en' => 'Gold & Silver', 'description_ne' => 'आजको भाउ', 'description_en' => "Today's price", 'icon_key' => 'Coins', 'accent_color' => '#b45309', 'sort_order' => 50],
            ['slug' => 'weather', 'href' => '/#weather', 'title_ne' => 'मौसम', 'title_en' => 'Weather', 'description_ne' => 'ताजा मौसम अपडेट', 'description_en' => 'Latest weather', 'icon_key' => 'CloudSun', 'accent_color' => '#00838f', 'sort_order' => 60],
        ], true);

        $this->insertDefaults('ad_positions', 'name', [
            ['name' => 'header', 'type' => 'HEADER', 'width' => 728, 'height' => 90],
            ['name' => 'sidebar', 'type' => 'SIDEBAR', 'width' => 300, 'height' => 250],
            ['name' => 'in-article', 'type' => 'IN_ARTICLE', 'width' => 728, 'height' => 90],
            ['name' => 'footer', 'type' => 'FOOTER', 'width' => 728, 'height' => 90],
            ['name' => 'between-sections', 'type' => 'BETWEEN_SECTIONS', 'width' => 970, 'height' => 90],
        ]);
        $campaignByPosition = [
            'header' => ['id' => 'aashatech-header', 'image_url' => '/images/ads/aashatech/aashatech-leaderboard.webp'],
            'sidebar' => ['id' => 'aashatech-sidebar', 'image_url' => '/images/ads/aashatech/aashatech-sidebar.webp'],
            'in-article' => ['id' => 'aashatech-in-article', 'image_url' => '/images/ads/aashatech/aashatech-leaderboard.webp'],
            'footer' => ['id' => 'aashatech-footer', 'image_url' => '/images/ads/aashatech/aashatech-leaderboard.webp'],
            'between-sections' => ['id' => 'aashatech-between-sections', 'image_url' => '/images/ads/aashatech/aashatech-section-banner.webp'],
        ];
        foreach ($campaignByPosition as $positionName => $campaign) {
            $positionId = DB::table('ad_positions')->where('name', $positionName)->value('id');
            if (! $positionId) {
                continue;
            }

            DB::table('advertisements')->updateOrInsert(
                ['id' => $campaign['id']],
                [
                    'title' => 'AashaTech Digital Systems',
                    'image_url' => $campaign['image_url'],
                    'target_url' => 'https://www.aashatech.com/',
                    'position_id' => $positionId,
                    'is_active' => true,
                    'start_date' => null,
                    'end_date' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            );
        }

        $this->insertDefaults('content_pages', 'slug', [
            ['slug' => 'about', 'title' => 'हाम्रोबारे', 'title_en' => 'About Us', 'body' => '<p>गोर्खाली खबर विश्वसनीय नेपाली समाचारको डिजिटल प्लेटफर्म हो।</p>', 'body_en' => '<p>Gorkhali Khabar is a trusted Nepali digital news platform.</p>', 'is_published' => true],
            ['slug' => 'contact', 'title' => 'सम्पर्क', 'title_en' => 'Contact', 'body' => '<p>समाचार सुझाव र प्रतिक्रियाका लागि हामीलाई सम्पर्क गर्नुहोस्।</p>', 'body_en' => '<p>Contact us with news tips and feedback.</p>', 'is_published' => true],
            ['slug' => 'terms', 'title' => 'प्रयोगका सर्तहरू', 'title_en' => 'Terms of Use', 'body' => '<p>यो वेबसाइट प्रयोग गर्दा लागू हुने सामान्य सर्तहरू।</p>', 'body_en' => '<p>General terms that apply when using this website.</p>', 'is_published' => true],
            ['slug' => 'privacy', 'title' => 'गोपनीयता नीति', 'title_en' => 'Privacy Policy', 'body' => '<p>हामी प्रयोगकर्ताको गोपनीयता र सुरक्षालाई सम्मान गर्छौं।</p>', 'body_en' => '<p>We respect user privacy and security.</p>', 'is_published' => true],
        ]);

        $this->insertMenuDefaults([
            ['location' => 'header', 'label' => 'गृहपृष्ठ', 'label_en' => 'Home', 'href' => '/', 'sort_order' => 0],
            ['location' => 'header', 'label' => 'समाचार', 'label_en' => 'News', 'href' => '/categories/samachar', 'sort_order' => 10],
            ['location' => 'header', 'label' => 'राजनीति', 'label_en' => 'Politics', 'href' => '/categories/rajniti', 'sort_order' => 20],
            ['location' => 'header', 'label' => 'अर्थतन्त्र', 'label_en' => 'Business', 'href' => '/categories/arthatantra', 'sort_order' => 30],
            ['location' => 'header', 'label' => 'खेलकुद', 'label_en' => 'Sports', 'href' => '/categories/khelkud', 'sort_order' => 40],
            ['location' => 'header', 'label' => 'विचार', 'label_en' => 'Opinion', 'href' => '/categories/bichar', 'sort_order' => 50],
            ['location' => 'header', 'label' => 'प्रविधि', 'label_en' => 'Technology', 'href' => '/categories/prabidhi', 'sort_order' => 60],
            ['location' => 'header', 'label' => 'समाज', 'label_en' => 'Society', 'href' => '/categories/samaj', 'sort_order' => 70],
            ['location' => 'header', 'label' => 'मनोरञ्जन', 'label_en' => 'Entertainment', 'href' => '/categories/manoranjan', 'sort_order' => 80],
            ['location' => 'header', 'label' => 'विश्व', 'label_en' => 'World', 'href' => '/categories/world', 'sort_order' => 90],
            ['location' => 'header', 'label' => 'स्वास्थ्य', 'label_en' => 'Health', 'href' => '/categories/swasthya', 'sort_order' => 100],
            ['location' => 'header', 'label' => 'शिक्षा', 'label_en' => 'Education', 'href' => '/categories/shiksha', 'sort_order' => 110],
            ['location' => 'header', 'label' => 'अन्तर्राष्ट्रिय', 'label_en' => 'International', 'href' => '/categories/antarrashtriya', 'sort_order' => 120],
            ['location' => 'header', 'label' => 'फिचर', 'label_en' => 'Features', 'href' => '/categories/feature', 'sort_order' => 130],
            ['location' => 'header', 'label' => 'भिडियो', 'label_en' => 'Video', 'href' => '/categories/video', 'sort_order' => 140],
            ['location' => 'header', 'label' => 'फोटो ग्यालेरी', 'label_en' => 'Photo Gallery', 'href' => '/galleries', 'sort_order' => 150],
            ['location' => 'header', 'label' => 'अनुसन्धान', 'label_en' => 'Investigations', 'href' => '/categories/anveshan', 'sort_order' => 160],
            ['location' => 'header', 'label' => 'जलवायु र वातावरण', 'label_en' => 'Climate & Environment', 'href' => '/categories/jalbayu-paryawaran', 'sort_order' => 170],
            ['location' => 'header', 'label' => 'कृषि', 'label_en' => 'Agriculture', 'href' => '/categories/krishi', 'sort_order' => 180],
            ['location' => 'header', 'label' => 'पर्यटन', 'label_en' => 'Travel', 'href' => '/categories/paryatan', 'sort_order' => 190],
            ['location' => 'header', 'label' => 'कला र संस्कृति', 'label_en' => 'Arts & Culture', 'href' => '/categories/kala-sanskriti', 'sort_order' => 200],
            ['location' => 'header', 'label' => 'जीवनशैली', 'label_en' => 'Lifestyle', 'href' => '/categories/jivanshaili', 'sort_order' => 210],
            ['location' => 'header', 'label' => 'सुरक्षा र अपराध', 'label_en' => 'Security & Crime', 'href' => '/categories/surakshya-aparadh', 'sort_order' => 220],
            ['location' => 'header', 'label' => 'रोजगारी', 'label_en' => 'Jobs & Careers', 'href' => '/categories/rojgari', 'sort_order' => 230],
            ['location' => 'header', 'label' => 'प्रवास', 'label_en' => 'Diaspora', 'href' => '/categories/prabas', 'sort_order' => 240],

            ['location' => 'footer', 'label' => 'समाचार', 'label_en' => 'News', 'href' => '/categories/samachar', 'sort_order' => 10],
            ['location' => 'footer', 'label' => 'राजनीति', 'label_en' => 'Politics', 'href' => '/categories/rajniti', 'sort_order' => 20],
            ['location' => 'footer', 'label' => 'अर्थतन्त्र', 'label_en' => 'Business', 'href' => '/categories/arthatantra', 'sort_order' => 30],
            ['location' => 'footer', 'label' => 'खेलकुद', 'label_en' => 'Sports', 'href' => '/categories/khelkud', 'sort_order' => 40],
            ['location' => 'footer', 'label' => 'प्रविधि', 'label_en' => 'Technology', 'href' => '/categories/prabidhi', 'sort_order' => 50],
            ['location' => 'footer', 'label' => 'समाज', 'label_en' => 'Society', 'href' => '/categories/samaj', 'sort_order' => 60],
            ['location' => 'footer', 'label' => 'मनोरञ्जन', 'label_en' => 'Entertainment', 'href' => '/categories/manoranjan', 'sort_order' => 70],
            ['location' => 'footer', 'label' => 'विश्व', 'label_en' => 'World', 'href' => '/categories/world', 'sort_order' => 80],
            ['location' => 'footer', 'label' => 'अनुसन्धान', 'label_en' => 'Investigations', 'href' => '/categories/anveshan', 'sort_order' => 130],
            ['location' => 'footer', 'label' => 'जलवायु र वातावरण', 'label_en' => 'Climate & Environment', 'href' => '/categories/jalbayu-paryawaran', 'sort_order' => 140],
            ['location' => 'footer', 'label' => 'कृषि', 'label_en' => 'Agriculture', 'href' => '/categories/krishi', 'sort_order' => 150],
            ['location' => 'footer', 'label' => 'पर्यटन', 'label_en' => 'Travel', 'href' => '/categories/paryatan', 'sort_order' => 160],
            ['location' => 'footer', 'label' => 'कला र संस्कृति', 'label_en' => 'Arts & Culture', 'href' => '/categories/kala-sanskriti', 'sort_order' => 170],
            ['location' => 'footer', 'label' => 'जीवनशैली', 'label_en' => 'Lifestyle', 'href' => '/categories/jivanshaili', 'sort_order' => 180],
            ['location' => 'footer', 'label' => 'सुरक्षा र अपराध', 'label_en' => 'Security & Crime', 'href' => '/categories/surakshya-aparadh', 'sort_order' => 190],
            ['location' => 'footer', 'label' => 'रोजगारी', 'label_en' => 'Jobs & Careers', 'href' => '/categories/rojgari', 'sort_order' => 200],
            ['location' => 'footer', 'label' => 'प्रवास', 'label_en' => 'Diaspora', 'href' => '/categories/prabas', 'sort_order' => 210],
            ['location' => 'footer', 'label' => 'हाम्रोबारे', 'label_en' => 'About Us', 'href' => '/page/about', 'sort_order' => 90],
            ['location' => 'footer', 'label' => 'सम्पर्क', 'label_en' => 'Contact', 'href' => '/page/contact', 'sort_order' => 100],
            ['location' => 'footer', 'label' => 'गोपनीयता नीति', 'label_en' => 'Privacy Policy', 'href' => '/page/privacy', 'sort_order' => 110],
            ['location' => 'footer', 'label' => 'प्रयोगका सर्तहरू', 'label_en' => 'Terms of Use', 'href' => '/page/terms', 'sort_order' => 120],
        ]);

        $this->insertDefaults('homepage_sections', 'section_key', [
            ['section_key' => 'latest-news', 'title' => 'ताजा समाचार', 'title_en' => 'Latest News', 'category_slug' => 'samachar', 'layout' => 'featured', 'sort_order' => 10, 'is_active' => true],
            ['section_key' => 'politics', 'title' => 'राजनीति', 'title_en' => 'Politics', 'category_slug' => 'rajniti', 'layout' => 'grid', 'sort_order' => 20, 'is_active' => true],
            ['section_key' => 'sports', 'title' => 'खेलकुद', 'title_en' => 'Sports', 'category_slug' => 'khelkud', 'layout' => 'grid', 'sort_order' => 30, 'is_active' => true],
            ['section_key' => 'latest-updates', 'title' => 'पछिल्ला अपडेट', 'title_en' => 'Latest Updates', 'category_slug' => 'samachar', 'layout' => 'list', 'sort_order' => 40, 'is_active' => true],
            ['section_key' => 'editor-picks', 'title' => 'सम्पादकको छनोट', 'title_en' => 'Editor’s Picks', 'category_slug' => 'feature', 'layout' => 'featured', 'sort_order' => 50, 'is_active' => true],
            ['section_key' => 'opinion-desk', 'title' => 'विचार डेस्क', 'title_en' => 'Opinion Desk', 'category_slug' => 'bichar', 'layout' => 'grid', 'sort_order' => 60, 'is_active' => true],
            ['section_key' => 'media-highlights', 'title' => 'फोटो र भिडियो', 'title_en' => 'Photo & Video', 'category_slug' => null, 'layout' => 'grid', 'sort_order' => 70, 'is_active' => true],
        ]);
    }

    /** @param list<array<string, mixed>> $rows */
    private function insertDefaults(string $table, string $uniqueKey, array $rows, bool $withUpdatedAt = false): void
    {
        foreach ($rows as $row) {
            if (DB::table($table)->where($uniqueKey, $row[$uniqueKey])->exists()) {
                continue;
            }

            $row['id'] = (string) Str::ulid();
            $row['created_at'] = now();
            if ($withUpdatedAt) {
                $row['updated_at'] = now();
            }

            DB::table($table)->insertOrIgnore($row);
        }
    }

    /** @param list<array<string, mixed>> $rows */
    private function insertMenuDefaults(array $rows): void
    {
        foreach ($rows as $row) {
            $exists = DB::table('menus')
                ->where('location', $row['location'])
                ->where('href', $row['href'])
                ->exists();

            if ($exists) {
                continue;
            }

            DB::table('menus')->insertOrIgnore([
                ...$row,
                'id' => (string) Str::ulid(),
                'parent_id' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}

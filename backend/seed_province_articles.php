<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Str;
use Carbon\Carbon;

// Get the admin user
$admin = User::where('email', 'admin@gorkhali.com')->first();
if (!$admin) {
    echo "Admin user not found!" . PHP_EOL;
    exit(1);
}

$provinceCategories = [
    'bagmati-pradesh' => [
        'name' => 'बागमती प्रदेश',
        'articles' => [
            ['title' => 'बागमतीमा नयाँ विकास योजना सार्वजनिक', 'excerpt' => 'बागमती प्रदेश सरकारले नयाँ विकास योजना सार्वजनिक गरेको छ।', 'title_en' => 'Bagmati Announces New Development Plan', 'excerpt_en' => 'Bagmati Province government has announced a new development plan.'],
            ['title' => 'काठमाडौं उपत्यकामा वायु प्रदूषण बढेको', 'excerpt' => 'काठमाडौं उपत्यकामा वायु प्रदूषणको स्तर बढेको छ।', 'title_en' => 'Air Pollution Increases in Kathmandu Valley', 'excerpt_en' => 'Air pollution levels have increased in Kathmandu Valley.'],
        ]
    ],
    'koshi-pradesh' => [
        'name' => 'कोशी प्रदेश',
        'articles' => [
            ['title' => 'कोशी प्रदेशमा नयाँ सडक परियोजना', 'excerpt' => 'कोशी प्रदेशमा नयाँ सडक परियोजना सुरु भएको छ।', 'title_en' => 'New Road Project in Koshi Province', 'excerpt_en' => 'New road project started in Koshi Province.'],
            ['title' => 'ईटहरीमा नयाँ अस्पतालको शिलान्यास', 'excerpt' => 'ईटहरीमा नयाँ अस्पतालको शिलान्यास गरिएको छ।', 'title_en' => 'New Hospital Foundation Laid in Itahari', 'excerpt_en' => 'Foundation for new hospital laid in Itahari.'],
        ]
    ],
    'madhesh-pradesh' => [
        'name' => 'मधेश प्रदेश',
        'articles' => [
            ['title' => 'मधेश प्रदेशमा कृषि योजना ल्याउने', 'excerpt' => 'मधेश प्रदेश सरकारले किसानहरूका लागि नयाँ कृषि योजना ल्याएको छ।', 'title_en' => 'Madhesh Province Brings Agricultural Scheme', 'excerpt_en' => 'Madhesh Province government has brought new agricultural scheme for farmers.'],
        ]
    ],
    'gandaki-pradesh' => [
        'name' => 'गण्डकी प्रदेश',
        'articles' => [
            ['title' => 'पोखरामा पर्यटन बढेको', 'excerpt' => 'पोखरामा यस वर्ष पर्यटकहरूको संख्या बढेको छ।', 'title_en' => 'Tourism Increases in Pokhara', 'excerpt_en' => 'Number of tourists increased in Pokhara this year.'],
        ]
    ],
    'lumbini-pradesh' => [
        'name' => 'लुम्बिनी प्रदेश',
        'articles' => [
            ['title' => 'लुम्बिनीमा बुद्ध जयन्ती तयारी', 'excerpt' => 'लुम्बिनीमा बुद्ध जयन्तीको तयारी सुरु भएको छ।', 'title_en' => 'Buddha Jayanti Preparations in Lumbini', 'excerpt_en' => 'Preparations for Buddha Jayanti started in Lumbini.'],
        ]
    ],
    'karnali-pradesh' => [
        'name' => 'कर्णाली प्रदेश',
        'articles' => [
            ['title' => 'कर्णालीमा नयाँ विद्यालय भवन', 'excerpt' => 'कर्णाली प्रदेशमा नयाँ विद्यालय भवन निर्माण भएको छ।', 'title_en' => 'New School Building in Karnali', 'excerpt_en' => 'New school building constructed in Karnali Province.'],
        ]
    ],
    'sudurpaschim-pradesh' => [
        'name' => 'सुदूरपश्चिम प्रदेश',
        'articles' => [
            ['title' => 'सुदूरपश्चिममा सिँचाई परियोजना', 'excerpt' => 'सुदूरपश्चिम प्रदेशमा नयाँ सिँचाई परियोजना सुरु भएको छ।', 'title_en' => 'New Irrigation Project in Sudurpaschim', 'excerpt_en' => 'New irrigation project started in Sudurpaschim Province.'],
        ]
    ],
];

$created = 0;
foreach ($provinceCategories as $slug => $data) {
    $category = Category::where('slug', $slug)->first();
    if (!$category) {
        echo "Category not found: $slug" . PHP_EOL;
        continue;
    }
    
    foreach ($data['articles'] as $articleData) {
        $id = (string) Str::ulid();
        $articleSlug = Str::slug($articleData['title']) . '-' . substr($id, 0, 8);
        
        Article::create([
            'id' => $id,
            'title' => $articleData['title'],
            'title_en' => $articleData['title_en'] ?? null,
            'slug' => $articleSlug,
            'excerpt' => $articleData['excerpt'],
            'excerpt_en' => $articleData['excerpt_en'] ?? null,
            'content' => '<p>' . $articleData['excerpt'] . ' यसको बारेमा थप विवरण यहाँ उपलब्ध छ।</p>',
            'content_en' => '<p>' . ($articleData['excerpt_en'] ?? '') . ' More details available here.</p>',
            'featured_image' => 'https://picsum.photos/seed/' . $articleSlug . '/800/450',
            'status' => 'PUBLISHED',
            'category_id' => $category->id,
            'author_id' => $admin->id,
            'published_at' => Carbon::now()->subHours(rand(1, 48)),
            'view_count' => rand(100, 5000),
            'comment_count' => rand(0, 50),
            'reading_time' => rand(2, 8),
            'is_featured' => false,
        ]);
        
        $created++;
        echo "Created: {$articleData['title']} in {$data['name']}" . PHP_EOL;
    }
}

echo "Total articles created: $created" . PHP_EOL;
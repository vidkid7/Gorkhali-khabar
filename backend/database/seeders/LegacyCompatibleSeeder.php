<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class LegacyCompatibleSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $seedPassword = env('SEED_ADMIN_PASSWORD', 'Admin@12345');

        DB::table('users')->insertOrIgnore([
            'id' => (string) Str::ulid(),
            'name' => 'Gorkhali Admin',
            'email' => 'admin@gorkhali.com',
            'email_verified' => $now,
            'password_hash' => Hash::make($seedPassword),
            'role' => 'ADMIN',
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $this->insertDefaults('categories', 'slug', [
            ['name' => 'समाचार', 'name_en' => 'News', 'slug' => 'samachar', 'color' => '#c62828', 'sort_order' => 1],
            ['name' => 'राजनीति', 'name_en' => 'Politics', 'slug' => 'rajniti', 'color' => '#1565c0', 'sort_order' => 2],
            ['name' => 'अर्थतन्त्र', 'name_en' => 'Economy', 'slug' => 'arthatantra', 'color' => '#2e7d32', 'sort_order' => 3],
            ['name' => 'खेलकुद', 'name_en' => 'Sports', 'slug' => 'khelkud', 'color' => '#e65100', 'sort_order' => 4],
            ['name' => 'प्रविधि', 'name_en' => 'Technology', 'slug' => 'prabidhi', 'color' => '#6a1b9a', 'sort_order' => 5],
            // Province categories
            ['name' => 'बागमती प्रदेश', 'name_en' => 'Bagmati Province', 'slug' => 'bagmati-pradesh', 'color' => '#c62828', 'sort_order' => 10],
            ['name' => 'कोशी प्रदेश', 'name_en' => 'Koshi Province', 'slug' => 'koshi-pradesh', 'color' => '#1565c0', 'sort_order' => 11],
            ['name' => 'मधेश प्रदेश', 'name_en' => 'Madhesh Province', 'slug' => 'madhesh-pradesh', 'color' => '#2e7d32', 'sort_order' => 12],
            ['name' => 'गण्डकी प्रदेश', 'name_en' => 'Gandaki Province', 'slug' => 'gandaki-pradesh', 'color' => '#e65100', 'sort_order' => 13],
            ['name' => 'लुम्बिनी प्रदेश', 'name_en' => 'Lumbini Province', 'slug' => 'lumbini-pradesh', 'color' => '#6a1b9a', 'sort_order' => 14],
            ['name' => 'कर्णाली प्रदेश', 'name_en' => 'Karnali Province', 'slug' => 'karnali-pradesh', 'color' => '#00838f', 'sort_order' => 15],
            ['name' => 'सुदूरपश्चिम प्रदेश', 'name_en' => 'Sudurpaschim Province', 'slug' => 'sudurpaschim-pradesh', 'color' => '#4e342e', 'sort_order' => 16],
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
}
<?php

namespace Tests\Feature\Schema;

use Database\Seeders\LegacyCompatibleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class LegacyCompatibleSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_is_idempotent_and_preserves_existing_values(): void
    {
        DB::table('users')->insert([
            'id' => 'existing-admin',
            'name' => 'Existing Admin',
            'email' => 'admin@gorkhali.com',
            'password_hash' => 'existing-hash',
            'role' => 'ADMIN',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('categories')->insert([
            'id' => 'existing-category',
            'name' => 'Existing Editorial Name',
            'slug' => 'samachar',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->seed(LegacyCompatibleSeeder::class);
        $countsAfterFirstRun = [
            'users' => DB::table('users')->count(),
            'categories' => DB::table('categories')->count(),
            'tags' => DB::table('tags')->count(),
            'quick_links' => DB::table('quick_links')->count(),
            'ad_positions' => DB::table('ad_positions')->count(),
            'menus' => DB::table('menus')->count(),
            'articles' => DB::table('articles')->count(),
            'breaking_news' => DB::table('breaking_news')->count(),
            'reels' => DB::table('reels')->count(),
            'galleries' => DB::table('galleries')->count(),
        ];

        $this->seed(LegacyCompatibleSeeder::class);

        foreach ($countsAfterFirstRun as $table => $count) {
            $this->assertSame($count, DB::table($table)->count(), "{$table} row count changed");
        }

        $admin = DB::table('users')->where('email', 'admin@gorkhali.com')->first();
        $category = DB::table('categories')->where('slug', 'samachar')->first();

        $this->assertSame('existing-hash', $admin->password_hash);
        $this->assertSame('ADMIN', $admin->role);
        $this->assertSame('Existing Editorial Name', $category->name);
        $this->assertGreaterThan(0, DB::table('menus')->where('location', 'header')->count());
        $this->assertGreaterThan(0, DB::table('menus')->where('location', 'footer')->count());
        $this->assertGreaterThan(0, DB::table('homepage_sections')->whereIn('section_key', [
            'latest-updates',
            'editor-picks',
            'opinion-desk',
            'media-highlights',
        ])->count());
        $this->assertGreaterThan(0, DB::table('articles')->where('slug', 'seeded-editorial-lead')->count());
        $this->assertGreaterThan(0, DB::table('breaking_news')->where('title', 'गोर्खाली खबर विशेष अपडेट')->count());
    }

    public function test_it_creates_one_active_aashatech_advertisement_per_position(): void
    {
        $this->seed(LegacyCompatibleSeeder::class);
        $this->seed(LegacyCompatibleSeeder::class);

        $campaign = DB::table('advertisements')
            ->where('target_url', 'https://www.aashatech.com/')
            ->where('advertisements.is_active', true);

        $this->assertSame(5, $campaign->count());
        $this->assertSame(
            ['BETWEEN_SECTIONS', 'FOOTER', 'HEADER', 'IN_ARTICLE', 'SIDEBAR'],
            $campaign
                ->join('ad_positions', 'advertisements.position_id', '=', 'ad_positions.id')
                ->orderBy('ad_positions.type')
                ->pluck('ad_positions.type')
                ->all(),
        );
    }

    public function test_it_seeds_complete_editorial_verticals_with_fictional_sample_articles(): void
    {
        $verticals = [
            'antarrashtriya', 'feature', 'video', 'anveshan', 'jalbayu-paryawaran',
            'krishi', 'paryatan', 'kala-sanskriti', 'jivanshaili', 'surakshya-aparadh',
            'rojgari', 'prabas',
        ];

        $this->seed(LegacyCompatibleSeeder::class);
        $this->seed(LegacyCompatibleSeeder::class);

        $this->assertSame(count($verticals), DB::table('categories')->whereIn('slug', $verticals)->count());
        foreach ($verticals as $slug) {
            $this->assertSame(
                3,
                DB::table('articles')->where('slug', 'like', "editorial-vertical-{$slug}-%")->count(),
                "{$slug} did not receive three sample articles",
            );
        }
        $this->assertSame(
            count($verticals),
            DB::table('menus')
                ->where('location', 'header')
                ->whereIn('href', array_map(fn (string $slug): string => "/categories/{$slug}", $verticals))
                ->count(),
        );
        $this->assertSame(
            36,
            DB::table('articles')->where('excerpt_en', 'This is fictional demonstration content prepared for the portal.')->count(),
        );
    }
}

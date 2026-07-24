<?php

namespace Tests\Feature\Schema;

use App\Models\Article;
use App\Models\MatchRecord;
use App\Models\MediaFile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LegacyModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_models_use_legacy_tables_and_string_keys(): void
    {
        $user = new User;
        $match = new MatchRecord;

        $this->assertSame('users', $user->getTable());
        $this->assertSame('matches', $match->getTable());
        $this->assertSame('string', $user->getKeyType());
        $this->assertFalse($user->getIncrementing());
    }

    public function test_models_cast_legacy_json_and_timestamp_fields(): void
    {
        $article = new Article([
            'published_at' => '2026-07-23T00:00:00+00:00',
            'is_featured' => true,
        ]);
        $media = new MediaFile(['variants' => ['small' => '/small.jpg']]);

        $this->assertTrue($article->is_featured);
        $this->assertInstanceOf(\DateTimeInterface::class, $article->published_at);
        $this->assertSame(['small' => '/small.jpg'], $media->variants);
    }

    public function test_every_legacy_table_has_a_model_mapping(): void
    {
        $models = [
            'Account', 'Session', 'VerificationToken', 'PasswordResetToken',
            'EmailVerificationToken', 'Category', 'Tag', 'ArticleTag', 'Comment',
            'CommentVote', 'Bookmark', 'Tournament', 'Team', 'Reel', 'Gallery',
            'GalleryImage', 'AdPosition', 'Advertisement', 'BreakingNews',
            'WebStory', 'PageView', 'SiteSetting', 'NewsletterSubscription',
            'QuickLink', 'AuditLog', 'Holiday', 'PanchangData', 'GoldSilverPrice',
            'ForexRate', 'Rashifal',
        ];

        foreach ($models as $model) {
            $class = "App\\Models\\{$model}";
            $this->assertTrue(class_exists($class), "Missing model {$model}");
        }
    }
}
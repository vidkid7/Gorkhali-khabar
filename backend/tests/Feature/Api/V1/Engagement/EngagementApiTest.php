<?php

namespace Tests\Feature\Api\V1\Engagement;

use App\Models\Article;
use App\Models\Bookmark;
use App\Models\Category;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EngagementApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_comments_are_published_approved_and_nested(): void
    {
        [$article, $author] = $this->article();
        $commenter = $this->user('commenter', true);
        $top = Comment::query()->create(['id' => 'top-comment', 'content' => 'Top', 'status' => 'APPROVED', 'article_id' => $article->id, 'user_id' => $commenter->id]);
        Comment::query()->create(['id' => 'pending-comment', 'content' => 'Pending', 'status' => 'PENDING', 'article_id' => $article->id, 'user_id' => $commenter->id]);
        Comment::query()->create(['id' => 'child-comment', 'content' => 'Child', 'status' => 'APPROVED', 'article_id' => $article->id, 'user_id' => $commenter->id, 'parent_id' => $top->id]);

        $this->getJson('/api/v1/comments?article_id='.$article->id)
            ->assertOk()
            ->assertJsonPath('data.total', 1)
            ->assertJsonPath('data.data.0.id', $top->id)
            ->assertJsonPath('data.data.0.children.0.id', 'child-comment')
            ->assertJsonMissing(['id' => 'pending-comment']);
    }

    public function test_verified_users_can_post_sanitized_comments_and_unverified_users_cannot(): void
    {
        [$article] = $this->article();
        $verified = $this->user('verified-commenter', true);
        $unverified = $this->user('unverified-commenter', false);

        $this->actingAs($unverified)->postJson('/api/v1/comments', [
            'article_id' => $article->id,
            'content' => 'Nope',
        ])->assertStatus(403);

        $this->actingAs($verified)->postJson('/api/v1/comments', [
            'article_id' => $article->id,
            'content' => '<b>Hello</b><script>alert(1)</script>',
        ])->assertCreated()->assertJsonPath('data.status', 'PENDING');

        $this->assertDatabaseHas('comments', ['article_id' => $article->id, 'content' => 'Hello', 'status' => 'PENDING']);
        $this->assertDatabaseHas('articles', ['id' => $article->id, 'comment_count' => 1]);
    }

    public function test_editor_can_moderate_and_users_can_toggle_comment_votes(): void
    {
        [$article] = $this->article();
        $commenter = $this->user('voter', true);
        $editor = $this->user('moderator', true, 'EDITOR');
        $comment = Comment::query()->create(['id' => 'vote-comment', 'content' => 'Vote', 'status' => 'PENDING', 'article_id' => $article->id, 'user_id' => $commenter->id]);

        $this->actingAs($editor)->patchJson('/api/v1/comments/'.$comment->id, ['status' => 'APPROVED'])
            ->assertOk()->assertJsonPath('data.status', 'APPROVED');
        $this->actingAs($commenter)->postJson('/api/v1/comments/'.$comment->id.'/vote', ['is_like' => true])
            ->assertCreated()->assertJsonPath('data.action', 'created');
        $this->actingAs($commenter)->postJson('/api/v1/comments/'.$comment->id.'/vote', ['is_like' => true])
            ->assertOk()->assertJsonPath('data.action', 'removed');
        $this->assertDatabaseMissing('comment_votes', ['comment_id' => $comment->id, 'user_id' => $commenter->id]);
    }

    public function test_bookmarks_are_published_and_scoped_to_the_authenticated_user(): void
    {
        [$article] = $this->article();
        $otherArticle = $this->article('other-bookmark-article')[0];
        $user = $this->user('bookmark-user', true);
        $otherUser = $this->user('other-bookmark-user', true);
        Bookmark::query()->create(['id' => 'other-bookmark', 'user_id' => $otherUser->id, 'article_id' => $otherArticle->id]);

        $this->actingAs($user)->postJson('/api/v1/bookmarks', ['article_id' => $article->id])
            ->assertCreated()->assertJsonPath('data.article.id', $article->id);
        $this->actingAs($user)->postJson('/api/v1/bookmarks', ['article_id' => $article->id])->assertStatus(409);
        $this->actingAs($user)->getJson('/api/v1/bookmarks')->assertOk()->assertJsonPath('data.total', 1);
        $this->actingAs($user)->deleteJson('/api/v1/bookmarks/'.$article->id)->assertOk();
        $this->actingAs($user)->deleteJson('/api/v1/bookmarks/'.$otherArticle->id)->assertStatus(404);
    }

    /** @return array{0: Article, 1: User} */
    private function article(string $id = 'engagement-article'): array
    {
        $author = $this->user($id.'-author', true);
        $category = Category::query()->create(['id' => $id.'-category', 'name' => 'News', 'slug' => $id.'-category']);
        $article = Article::query()->create([
            'id' => $id,
            'title' => 'Engagement article',
            'slug' => $id,
            'content' => 'Article content',
            'status' => 'PUBLISHED',
            'published_at' => now(),
            'category_id' => $category->id,
            'author_id' => $author->id,
        ]);

        return [$article, $author];
    }

    private function user(string $id, bool $verified, string $role = 'READER'): User
    {
        return User::query()->create([
            'id' => $id,
            'name' => $id,
            'email' => $id.'@example.com',
            'role' => $role,
            'is_active' => true,
            'email_verified' => $verified ? now() : null,
        ]);
    }
}

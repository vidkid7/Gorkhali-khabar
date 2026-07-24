<?php

namespace Tests\Feature\Api\V1\Media;

use App\Models\NewsletterSubscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsletterApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_newsletter_subscription_normalizes_and_reactivates_an_address(): void
    {
        NewsletterSubscription::query()->create([
            'id' => 'newsletter-existing',
            'email' => 'person@example.com',
            'language' => 'ne',
            'source' => 'old',
            'is_active' => false,
        ]);

        $this->postJson('/api/v1/newsletter', ['email' => '  PERSON@Example.com ', 'language' => 'en'])
            ->assertCreated()
            ->assertJsonPath('data.email', 'person@example.com')
            ->assertJsonPath('data.is_active', true)
            ->assertJsonMissingPath('data.language');

        $this->assertDatabaseHas('newsletter_subscriptions', ['email' => 'person@example.com', 'language' => 'en', 'source' => 'footer', 'is_active' => true]);
    }

    public function test_newsletter_rejects_invalid_input(): void
    {
        $this->postJson('/api/v1/newsletter', ['email' => 'not-an-email'])->assertStatus(400);
        $this->postJson('/api/v1/newsletter', ['email' => 'person@example.com', 'language' => 'fr'])->assertStatus(400);
    }

    public function test_newsletter_allows_eight_requests_per_fifteen_minutes_per_ip(): void
    {
        for ($index = 1; $index <= 8; $index++) {
            $this->postJson('/api/v1/newsletter', ['email' => "subscriber{$index}@example.com"])->assertCreated();
        }

        $this->postJson('/api/v1/newsletter', ['email' => 'subscriber9@example.com'])
            ->assertStatus(429)
            ->assertJsonPath('success', false);
    }
}

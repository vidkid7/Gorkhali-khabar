<?php

namespace Tests\Feature\Api\V1;

use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Tests\TestCase;

class ExceptionResponseTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Route::post('/api/v1/testing/validation', static function (): never {
            throw ValidationException::withMessages(['email' => ['Invalid email']]);
        });
        Route::get('/api/v1/testing/failure', static function (): never {
            throw new RuntimeException('secret implementation detail');
        });
    }

    public function test_validation_exceptions_use_400_compatibility_output(): void
    {
        $this->postJson('/api/v1/testing/validation')
            ->assertStatus(400)
            ->assertExactJson([
                'success' => false,
                'error' => 'Validation failed',
                'errors' => ['email' => ['Invalid email']],
            ]);
    }

    public function test_unexpected_api_exceptions_do_not_leak_details(): void
    {
        $response = $this->getJson('/api/v1/testing/failure')
            ->assertStatus(500)
            ->assertExactJson(['success' => false, 'error' => 'Internal server error']);

        $this->assertStringNotContainsString('secret implementation detail', $response->getContent());
    }
}
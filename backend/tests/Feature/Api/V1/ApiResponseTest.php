<?php

namespace Tests\Feature\Api\V1;

use App\Support\ApiResponse;
use Tests\TestCase;

class ApiResponseTest extends TestCase
{
    public function test_success_omits_optional_keys_when_they_are_absent(): void
    {
        $this->assertSame([
            'success' => true,
            'data' => ['id' => '1'],
        ], ApiResponse::success(['id' => '1'])->getData(true));
    }

    public function test_error_contains_the_compatibility_envelope(): void
    {
        $this->assertSame([
            'success' => false,
            'error' => 'Forbidden',
        ], ApiResponse::error('Forbidden', 403)->getData(true));
    }

    public function test_error_includes_validation_details_only_when_provided(): void
    {
        $response = ApiResponse::error('Invalid input', 400, ['email' => ['Invalid email']]);

        $this->assertSame([
            'success' => false,
            'error' => 'Invalid input',
            'errors' => ['email' => ['Invalid email']],
        ], $response->getData(true));
        $this->assertSame(400, $response->getStatusCode());
    }
}
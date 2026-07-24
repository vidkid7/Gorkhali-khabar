<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscription;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        if (! $request->isJson()) {
            return ApiResponse::error('Invalid content type', 415);
        }

        $data = $request->validate([
            'email' => ['required', 'string', 'email', 'max:254'],
            'language' => ['sometimes', 'in:ne,en'],
        ]);
        $email = strtolower(trim($data['email']));
        $subscription = NewsletterSubscription::query()->updateOrCreate(
            ['email' => $email],
            [
                'language' => $data['language'] ?? 'ne',
                'source' => 'footer',
                'is_active' => true,
            ],
        );

        return ApiResponse::success($subscription->only(['id', 'email', 'is_active']), 201);
    }
}

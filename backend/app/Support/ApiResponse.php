<?php

namespace App\Support;

use Illuminate\Http\JsonResponse;

final class ApiResponse
{
    public static function success(mixed $data = null, int $status = 200, ?string $message = null): JsonResponse
    {
        $body = ['success' => true];

        if ($data !== null) {
            $body['data'] = $data;
        }
        if ($message !== null) {
            $body['message'] = $message;
        }

        return response()->json($body, $status);
    }

    public static function error(string $error, int $status, array $errors = []): JsonResponse
    {
        $body = [
            'success' => false,
            'error' => $error,
        ];

        if ($errors !== []) {
            $body['errors'] = $errors;
        }

        return response()->json($body, $status);
    }
}